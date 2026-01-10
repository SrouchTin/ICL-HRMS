<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\EmployeeSchedule;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * Display attendance records for the employee
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;

            if (!$employee) {
                return redirect()->back()->with('error', 'Employee profile not found.');
            }

            // Get current month or requested month
            $month = $request->input('month', Carbon::now()->format('Y-m'));
            $startOfMonth = Carbon::parse($month)->startOfMonth();
            $endOfMonth = Carbon::parse($month)->endOfMonth();

            // Fetch attendances for the month
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->with(['shift', 'holiday', 'leave.leaveType'])
                ->orderBy('date', 'desc')
                ->get();

            // Calculate summary statistics
            $presentDays = $attendances->where('check_in', '!=', null)->count();
            $lateArrivals = $attendances->where('come_late', true)->count();
            $earlyDepartures = $attendances->where('leave_early', true)->count();
            $absents = $attendances->where('check_in', null)
                ->where('is_working_day', true)
                ->whereNull('leave_id')
                ->whereNull('holiday_id')
                ->count();

            // Available months for dropdown (last 12 months)
            $months = [];
            for ($i = 0; $i < 12; $i++) {
                $date = Carbon::now()->subMonths($i);
                $months[] = [
                    'value' => $date->format('Y-m'),
                    'label' => $date->format('F Y'),
                ];
            }

            return view('employee.attendances.index', compact(
                'attendances',
                'presentDays',
                'lateArrivals',
                'earlyDepartures',
                'absents',
                'months',
                'month'
            ));
        } catch (\Exception $e) {
            Log::error('Error fetching attendance: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'An error occurred while fetching attendance records.');
        }
    }

    /**
     * Handle employee check-in
     */
    public function checkIn(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            
            if (!$user) {
                Log::error('Check-in error: User not authenticated');
                return response()->json(['error' => 'User not authenticated'], 401);
            }
            
            $employee = $user->employee;

            if (!$employee) {
                Log::error('Check-in error: Employee not found for user ID ' . $user->id);
                return response()->json(['error' => 'Employee profile not found'], 404);
            }

            $today = Carbon::today();
            $now = Carbon::now();

            Log::info('Check-in attempt started', [
                'employee_id' => $employee->id,
                'employee_code' => $employee->employee_code ?? 'N/A',
                'date' => $today->format('Y-m-d'),
                'time' => $now->format('H:i:s'),
                'day_of_week' => $today->format('l'),
            ]);

            // Check if attendance record exists for today
            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('date', $today)
                ->first();

            // Check if already checked in
            if ($attendance && $attendance->check_in) {
                Log::warning('Already checked in', [
                    'employee_id' => $employee->id,
                    'check_in_time' => $attendance->check_in,
                ]);
                return response()->json([
                    'error' => 'You have already checked in today at ' . Carbon::parse($attendance->check_in)->format('h:i A')
                ], 400);
            }

            // Get today's shift
            $shift = $this->getTodayShift($employee);

            if (!$shift) {
                Log::error('Check-in error: No shift found', [
                    'employee_id' => $employee->id,
                    'date' => $today->format('Y-m-d'),
                ]);
                return response()->json([
                    'error' => 'No shift assigned for today. Please contact HR.'
                ], 400);
            }

            Log::info('Shift found for check-in', [
                'shift_id' => $shift->id,
                'shift_name' => $shift->name,
                'start_time' => $shift->start_time,
                'end_time' => $shift->end_time,
            ]);

            // Check if today is a working day
            $isWorkingDay = $this->isWorkingDay($employee, $today);
            
            Log::info('Working day check', [
                'is_working_day' => $isWorkingDay,
                'date' => $today->format('Y-m-d'),
                'day' => $today->format('l'),
            ]);

            // Check if today is a holiday (check if today falls between from_date and to_date)
            $holiday = Holiday::where('from_date', '<=', $today)
                ->where('to_date', '>=', $today)
                ->first();
            
            if ($holiday) {
                Log::info('Holiday detected', [
                    'holiday_id' => $holiday->id,
                    'holiday_name' => $holiday->name ?? 'N/A',
                ]);
            }

            // Check if employee is on leave
            $leave = Leave::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->whereDate('from_date', '<=', $today)
                ->whereDate('to_date', '>=', $today)
                ->first();

            if ($leave) {
                Log::info('Employee on leave', [
                    'leave_id' => $leave->id,
                    'leave_type' => $leave->leaveType->name ?? 'N/A',
                ]);
            }

            // Calculate if late or early
            $shiftStartTime = Carbon::parse($shift->start_time);
            $lateThreshold = $shiftStartTime->copy()->addMinutes($shift->late_after_min ?? 10);
            $comeLate = $now->greaterThan($lateThreshold);
            $comeEarly = $now->lessThan($shiftStartTime);

            Log::info('Check-in time analysis', [
                'shift_start' => $shiftStartTime->format('H:i:s'),
                'late_threshold' => $lateThreshold->format('H:i:s'),
                'current_time' => $now->format('H:i:s'),
                'come_late' => $comeLate,
                'come_early' => $comeEarly,
            ]);

            // Create or update attendance record
            if ($attendance) {
                $attendance->update([
                    'check_in' => $now->format('H:i:s'),
                    'shift_id' => $shift->id,
                    'come_late' => $comeLate,
                    'come_early' => $comeEarly,
                    'is_working_day' => $isWorkingDay,
                    'holiday_id' => $holiday ? $holiday->id : null,
                    'leave_id' => $leave ? $leave->id : null,
                ]);
                
                Log::info('Attendance record updated', ['attendance_id' => $attendance->id]);
            } else {
                $attendance = Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $today,
                    'shift_id' => $shift->id,
                    'check_in' => $now->format('H:i:s'),
                    'come_late' => $comeLate,
                    'come_early' => $comeEarly,
                    'is_working_day' => $isWorkingDay,
                    'holiday_id' => $holiday ? $holiday->id : null,
                    'leave_id' => $leave ? $leave->id : null,
                ]);
                
                Log::info('Attendance record created', ['attendance_id' => $attendance->id]);
            }

            DB::commit();

            Log::info('Check-in successful', [
                'attendance_id' => $attendance->id,
                'employee_id' => $employee->id,
                'check_in_time' => $now->format('H:i:s'),
            ]);

            return response()->json([
                'success' => true,
                'check_in' => $now->format('H:i'),
                'message' => 'Successfully checked in',
                'come_late' => $comeLate,
                'come_early' => $comeEarly,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Check-in error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'error' => 'Failed to check in. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Handle employee check-out
     */
    public function checkOut(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            $employee = $user->employee;

            if (!$employee) {
                Log::error('Check-out error: Employee not found for user ID ' . ($user->id ?? 'N/A'));
                return response()->json(['error' => 'Employee profile not found'], 404);
            }

            $today = Carbon::today();
            $now = Carbon::now();

            Log::info('Check-out attempt started', [
                'employee_id' => $employee->id,
                'date' => $today->format('Y-m-d'),
                'time' => $now->format('H:i:s'),
            ]);

            // Find today's attendance record
            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('date', $today)
                ->first();

            if (!$attendance) {
                Log::warning('No attendance record found for check-out', [
                    'employee_id' => $employee->id,
                    'date' => $today->format('Y-m-d'),
                ]);
                return response()->json(['error' => 'No check-in record found for today'], 400);
            }

            if (!$attendance->check_in) {
                Log::warning('Attempted check-out without check-in', [
                    'employee_id' => $employee->id,
                    'attendance_id' => $attendance->id,
                ]);
                return response()->json(['error' => 'You must check in first before checking out'], 400);
            }

            if ($attendance->check_out) {
                Log::warning('Already checked out', [
                    'employee_id' => $employee->id,
                    'check_out_time' => $attendance->check_out,
                ]);
                return response()->json([
                    'error' => 'You have already checked out today at ' . Carbon::parse($attendance->check_out)->format('h:i A')
                ], 400);
            }

            $shift = $attendance->shift;

            if (!$shift) {
                Log::error('Shift information not found for attendance', [
                    'attendance_id' => $attendance->id,
                ]);
                return response()->json(['error' => 'Shift information not found'], 400);
            }

            // Calculate if leaving early or late
            $shiftEndTime = Carbon::parse($shift->end_time);
            $leaveEarly = $now->lessThan($shiftEndTime);
            $leaveLate = $now->greaterThan($shiftEndTime);

            // Calculate overtime (only if left after shift end time)
            $overtimeMinutes = 0;
            if ($leaveLate) {
                $overtimeMinutes = $now->diffInMinutes($shiftEndTime);
            }

            Log::info('Check-out time analysis', [
                'shift_end' => $shiftEndTime->format('H:i:s'),
                'current_time' => $now->format('H:i:s'),
                'leave_early' => $leaveEarly,
                'leave_late' => $leaveLate,
                'overtime_minutes' => $overtimeMinutes,
            ]);

            // Update attendance record
            $attendance->update([
                'check_out' => $now->format('H:i:s'),
                'leave_early' => $leaveEarly,
                'leave_late' => $leaveLate,
                'overtime_minutes' => $overtimeMinutes,
            ]);

            DB::commit();

            Log::info('Check-out successful', [
                'attendance_id' => $attendance->id,
                'employee_id' => $employee->id,
                'check_out_time' => $now->format('H:i:s'),
                'overtime_minutes' => $overtimeMinutes,
            ]);

            return response()->json([
                'success' => true,
                'check_out' => $now->format('H:i'),
                'overtime_minutes' => $overtimeMinutes,
                'message' => 'Successfully checked out',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Check-out error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'error' => 'Failed to check out. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Export attendance records
     */
    public function export(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;

            if (!$employee) {
                return redirect()->back()->with('error', 'Employee profile not found.');
            }

            $month = $request->input('month', Carbon::now()->format('Y-m'));
            $startOfMonth = Carbon::parse($month)->startOfMonth();
            $endOfMonth = Carbon::parse($month)->endOfMonth();

            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->with(['shift', 'holiday', 'leave.leaveType'])
                ->orderBy('date', 'asc')
                ->get();

            // Generate CSV
            $filename = "attendance_{$employee->employee_code}_{$month}.csv";
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function() use ($attendances) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // CSV Headers
                fputcsv($file, [
                    'Date',
                    'Day',
                    'Shift',
                    'Shift Time',
                    'Check In',
                    'Check Out',
                    'Working Hours',
                    'Status',
                    'Overtime (minutes)',
                    'Remark'
                ]);

                // CSV Data
                foreach ($attendances as $attendance) {
                    $status = $this->getAttendanceStatus($attendance);
                    $workingHours = $this->calculateWorkingHours($attendance);
                    $shiftTime = $attendance->shift 
                        ? $attendance->shift->start_time . ' - ' . $attendance->shift->end_time 
                        : '-';

                    fputcsv($file, [
                        $attendance->date->format('Y-m-d'),
                        $attendance->date->format('l'),
                        $attendance->shift ? $attendance->shift->name : '-',
                        $shiftTime,
                        $attendance->check_in ?? '-',
                        $attendance->check_out ?? '-',
                        $workingHours,
                        $status,
                        $attendance->overtime_minutes ?? 0,
                        $attendance->remark ?? '-'
                    ]);
                }

                fclose($file);
            };

            Log::info('Attendance export', [
                'employee_id' => $employee->id,
                'month' => $month,
                'records_count' => $attendances->count(),
            ]);

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Export error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Failed to export attendance records.');
        }
    }

    /**
     * Get today's shift for the employee
     */
    private function getTodayShift($employee)
    {
        try {
            $today = Carbon::today();
            $dayOfWeek = strtolower($today->format('D')); // mon, tue, wed, thu, fri, sat, sun

            Log::info('Getting shift for employee', [
                'employee_id' => $employee->id,
                'date' => $today->format('Y-m-d'),
                'day_of_week' => $dayOfWeek,
            ]);

            // Find active schedule for today
            $schedule = EmployeeSchedule::where('employee_id', $employee->id)
                ->where('start_date', '<=', $today)
                ->where(function ($query) use ($today) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', $today);
                })
                ->with(['scheduleTemplate', 'shift'])
                ->first();

            if (!$schedule) {
                Log::warning('No schedule found for employee', [
                    'employee_id' => $employee->id,
                    'date' => $today->format('Y-m-d'),
                ]);
                
                // Try to find any shift as fallback
                $shift = Shift::first();
                
                if ($shift) {
                    Log::info('Using fallback shift', [
                        'shift_id' => $shift->id,
                        'shift_name' => $shift->name,
                    ]);
                }
                
                return $shift;
            }

            Log::info('Schedule found', [
                'schedule_id' => $schedule->id,
                'shift_id' => $schedule->shift_id,
                'template_id' => $schedule->schedule_template_id,
            ]);

            // Get the shift from the schedule
            if ($schedule->shift) {
                Log::info('Returning shift from schedule', [
                    'shift_id' => $schedule->shift->id,
                    'shift_name' => $schedule->shift->name,
                ]);
                return $schedule->shift;
            }

            // If no shift relation, try to find by shift_id
            if ($schedule->shift_id) {
                $shift = Shift::find($schedule->shift_id);
                
                if ($shift) {
                    Log::info('Found shift by ID', [
                        'shift_id' => $shift->id,
                        'shift_name' => $shift->name,
                    ]);
                    return $shift;
                }
            }

            // Last resort: get any available shift
            $shift = Shift::first();
            
            if ($shift) {
                Log::warning('Using any available shift as last resort', [
                    'shift_id' => $shift->id,
                    'shift_name' => $shift->name,
                ]);
            } else {
                Log::error('No shifts available in database');
            }

            return $shift;

        } catch (\Exception $e) {
            Log::error('Error getting shift', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Return first available shift as emergency fallback
            return Shift::first();
        }
    }

    /**
     * Check if today is a working day for the employee
     */
    private function isWorkingDay($employee, $date)
    {
        try {
            $dayOfWeek = strtolower($date->format('D')); // mon, tue, wed, thu, fri, sat, sun

            Log::info('Checking if working day', [
                'employee_id' => $employee->id,
                'date' => $date->format('Y-m-d'),
                'day_of_week' => $dayOfWeek,
            ]);

            $schedule = EmployeeSchedule::where('employee_id', $employee->id)
                ->where('start_date', '<=', $date)
                ->where(function ($query) use ($date) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', $date);
                })
                ->with('scheduleTemplate')
                ->first();

            if ($schedule && $schedule->scheduleTemplate) {
                $isWorking = (bool) ($schedule->scheduleTemplate->{$dayOfWeek} ?? false);
                
                Log::info('Working day from schedule template', [
                    'template_id' => $schedule->scheduleTemplate->id,
                    'day_column' => $dayOfWeek,
                    'value' => $schedule->scheduleTemplate->{$dayOfWeek} ?? 'null',
                    'is_working' => $isWorking,
                ]);
                
                return $isWorking;
            }

            // Default: Monday to Friday are working days
            $isWorking = !in_array($dayOfWeek, ['sat', 'sun']);
            
            Log::info('Using default working day logic', [
                'day' => $dayOfWeek,
                'is_working' => $isWorking,
            ]);
            
            return $isWorking;

        } catch (\Exception $e) {
            Log::error('Error checking working day', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Default to Monday-Friday as safe fallback
            $dayOfWeek = strtolower($date->format('D'));
            return !in_array($dayOfWeek, ['sat', 'sun']);
        }
    }

    /**
     * Get attendance status label
     */
    private function getAttendanceStatus($attendance)
    {
        if ($attendance->holiday_id) {
            return 'Holiday';
        } elseif ($attendance->leave_id) {
            return 'On Leave';
        } elseif (!$attendance->is_working_day) {
            return 'Day Off';
        } elseif ($attendance->check_in && $attendance->check_out) {
            return 'Complete';
        } elseif ($attendance->check_in) {
            return 'In Progress';
        } else {
            return 'Absent';
        }
    }

    /**
     * Calculate working hours
     */
    private function calculateWorkingHours($attendance)
    {
        if ($attendance->check_in && $attendance->check_out) {
            try {
                $checkIn = Carbon::parse($attendance->check_in);
                $checkOut = Carbon::parse($attendance->check_out);
                $hours = $checkOut->diffInMinutes($checkIn) / 60;
                return number_format($hours, 1) . ' hrs';
            } catch (\Exception $e) {
                Log::error('Error calculating working hours', [
                    'attendance_id' => $attendance->id,
                    'check_in' => $attendance->check_in,
                    'check_out' => $attendance->check_out,
                    'error' => $e->getMessage(),
                ]);
                return '-';
            }
        }
        return '-';
    }
}