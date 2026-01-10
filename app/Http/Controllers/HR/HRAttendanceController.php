<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Holiday;
use Carbon\Carbon;
use App\Models\EmployeeSchedule;
use Illuminate\Support\Facades\DB;

class HRAttendanceController extends Controller
{
    /**
     * Display attendance management page
     */
    public function index(Request $request)
    {
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
        $date = Carbon::parse($selectedDate);

        // Check if it's a day off (weekend or holiday)
        $isDayOff = $this->isDayOff($date);
        $dayName = $date->format('l');
        $holiday = Holiday::getHoliday($date);

        // Get departments for filter
        $departments = Department::orderBy('department_name')->get();

        // Get all active employees with department and personal info
        $allEmployees = Employee::where('status', 'active')
            ->with(['department', 'personalInfo'])
            ->orderBy('employee_code')
            ->get();

        // Get current shifts for all active employees on the selected date
        $currentSchedules = EmployeeSchedule::with('shift')
            ->whereIn('employee_id', $allEmployees->pluck('id'))
            ->where('start_date', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->where('end_date', '>=', $date)
                      ->orWhereNull('end_date');
            })
            ->get()
            ->keyBy('employee_id');

        // Get real attendance records for the selected date
        $existingAttendances = Attendance::with(['employee.personalInfo', 'employee.department', 'shift', 'leave.leaveType'])
            ->whereDate('date', $date)
            ->get()
            ->keyBy('employee_id');

        // Build full attendance list
        $attendances = collect();

        if ($isDayOff) {
            // On weekends/holidays, only show employees who are on leave or have actual check-ins
            foreach ($allEmployees as $employee) {
                $attendance = $existingAttendances->get($employee->id);
                
                // Only include if employee has leave or actual attendance record
                if ($attendance && ($attendance->leave_id || $attendance->check_in)) {
                    $currentShift = $currentSchedules->get($employee->id);
                    $attendance->shift_id = $currentShift?->shift_id;
                    $attendance->shift = $currentShift?->shift;
                    $attendances->push($attendance);
                }
            }
        } else {
            // Regular working day - show all employees
            foreach ($allEmployees as $employee) {
                $currentShift = $currentSchedules->get($employee->id);

                if (isset($existingAttendances[$employee->id])) {
                    $attendance = $existingAttendances[$employee->id];
                } else {
                    // Virtual attendance record
                    $attendance = new Attendance();
                    $attendance->employee_id = $employee->id;
                    $attendance->employee = $employee;
                    $attendance->date = $date;
                    $attendance->check_in = null;
                    $attendance->check_out = null;
                    $attendance->come_late = false;
                    $attendance->come_early = false;
                    $attendance->leave_early = false;
                    $attendance->leave_late = false;
                    $attendance->overtime_minutes = 0;
                    $attendance->leave_id = null;
                    $attendance->holiday_id = null;
                    $attendance->is_working_day = true;
                    $attendance->remark = null;
                }

                // Attach current shift info
                $attendance->shift_id = $currentShift?->shift_id;
                $attendance->shift = $currentShift?->shift;

                $attendances->push($attendance);
            }
        }

        // === Filters ===
        if ($request->filled('department')) {
            $attendances = $attendances->filter(fn($a) => 
                $a->employee && $a->employee->department_id == $request->department
            );
        }

        if ($request->filled('status')) {
            $attendances = $attendances->filter(function ($a) use ($request) {
                return match ($request->status) {
                    'present'   => $a->check_in && !$a->leave_id,
                    'absent'    => !$a->check_in && !$a->leave_id,
                    'late'      => $a->come_late,
                    'on_leave'  => $a->leave_id,
                    default     => true,
                };
            });
        }

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $attendances = $attendances->filter(function ($a) use ($search) {
                $emp = $a->employee;
                if (!$emp) return false;

                $enName = strtolower($emp->personalInfo?->full_name_en ?? '');
                $khName = strtolower($emp->personalInfo?->full_name_kh ?? '');
                $code   = strtolower($emp->employee_code ?? '');

                return str_contains($enName, $search) ||
                       str_contains($khName, $search) ||
                       str_contains($code, $search);
            });
        }

        // === Statistics ===
        if ($isDayOff) {
            $todayStats = [
                'present'  => 0,
                'absent'   => 0,
                'late'     => 0,
                'onLeave'  => $existingAttendances->where('leave_id', '!=', null)->count(),
            ];
        } else {
            $todayStats = [
                'present'  => $existingAttendances->where('check_in', '!=', null)->where('leave_id', null)->count(),
                'absent'   => $allEmployees->count() - $existingAttendances->where('check_in', '!=', null)->count() - $existingAttendances->where('leave_id', '!=', null)->count(),
                'late'     => $existingAttendances->where('come_late', true)->count(),
                'onLeave'  => $existingAttendances->where('leave_id', '!=', null)->count(),
            ];
        }

        return view('hr.attendnaces.index', compact(
            'attendances',
            'departments',
            'allEmployees',
            'selectedDate',
            'todayStats',
            'isDayOff',
            'dayName',
            'holiday'
        ));
    }

    /**
     * Check if a date is a day off (Weekend or Public Holiday)
     */
    private function isDayOff($date)
    {
        // Check if it's Sunday (0) or Saturday (6)
        $dayOfWeek = $date->dayOfWeek;
        
        if ($dayOfWeek == 0 || $dayOfWeek == 6) {
            return true;
        }

        // Check if it's a public holiday
        $isHoliday = Holiday::isHoliday($date);
        if ($isHoliday) {
            return true;
        }

        return false;
    }

    /**
     * Check in employee
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $date = Carbon::parse($request->date);
            $employee = Employee::findOrFail($request->employee_id);

            // Check if it's a day off
            if ($this->isDayOff($date)) {
                $holiday = Holiday::getHoliday($date);
                $message = $holiday 
                    ? 'Cannot check in on public holiday: ' . $holiday->holiday_name
                    : 'Cannot check in on a day off (' . $date->format('l') . ')';
                    
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }

            // Get employee's current shift
            $currentSchedule = EmployeeSchedule::with('shift')
                ->where('employee_id', $employee->id)
                ->where('start_date', '<=', $date)
                ->where(function ($q) use ($date) {
                    $q->where('end_date', '>=', $date)
                      ->orWhereNull('end_date');
                })
                ->first();

            // Check if already checked in
            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $date)
                ->first();

            if ($attendance && $attendance->check_in) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Employee already checked in today'
                ], 400);
            }

            $checkInTime = Carbon::now();

            // Create or update attendance
            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->employee_id = $employee->id;
                $attendance->date = $date;
                $attendance->shift_id = $currentSchedule?->shift_id;
                $attendance->is_working_day = true;
            }

            $attendance->check_in = $checkInTime;

            // Check if late
            if ($currentSchedule && $currentSchedule->shift) {
                $shiftStart = Carbon::parse($date->format('Y-m-d') . ' ' . $currentSchedule->shift->start_time);
                if ($checkInTime->gt($shiftStart)) {
                    $attendance->come_late = true;
                } else {
                    $attendance->come_early = true;
                }
            }

            $attendance->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee checked in successfully at ' . $checkInTime->format('H:i'),
                'check_in' => $checkInTime->format('H:i')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to check in: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check out employee
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $date = Carbon::parse($request->date);
            $employee = Employee::findOrFail($request->employee_id);

            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $date)
                ->first();

            if (!$attendance || !$attendance->check_in) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Employee has not checked in yet'
                ], 400);
            }

            if ($attendance->check_out) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Employee already checked out'
                ], 400);
            }

            $checkOutTime = Carbon::now();
            $attendance->check_out = $checkOutTime;

            // Check if early departure or overtime
            if ($attendance->shift) {
                $shiftEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $attendance->shift->end_time);
                if ($checkOutTime->lt($shiftEnd)) {
                    $attendance->leave_early = true;
                } elseif ($checkOutTime->gt($shiftEnd)) {
                    $attendance->leave_late = true;
                    $attendance->overtime_minutes = $shiftEnd->diffInMinutes($checkOutTime);
                }
            }

            $attendance->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee checked out successfully at ' . $checkOutTime->format('H:i'),
                'check_out' => $checkOutTime->format('H:i'),
                'overtime_minutes' => $attendance->overtime_minutes ?? 0
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to check out: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manual check in with custom time
     */
    public function manualCheckIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in_time' => 'required',
            'remark' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $date = Carbon::parse($request->date);
            $employee = Employee::findOrFail($request->employee_id);

            // Get employee's current shift
            $currentSchedule = EmployeeSchedule::with('shift')
                ->where('employee_id', $employee->id)
                ->where('start_date', '<=', $date)
                ->where(function ($q) use ($date) {
                    $q->where('end_date', '>=', $date)
                      ->orWhereNull('end_date');
                })
                ->first();

            // Check if already checked in
            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $date)
                ->first();

            if ($attendance && $attendance->check_in) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Employee already checked in for this date'
                ], 400);
            }

            // Parse check in time
            $checkInTime = Carbon::parse($date->format('Y-m-d') . ' ' . $request->check_in_time);

            // Create or update attendance
            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->employee_id = $employee->id;
                $attendance->date = $date;
                $attendance->shift_id = $currentSchedule?->shift_id;
                $attendance->is_working_day = true;
            }

            $attendance->check_in = $checkInTime;
            $attendance->remark = $request->remark;

            // Check if late
            if ($currentSchedule && $currentSchedule->shift) {
                $shiftStart = Carbon::parse($date->format('Y-m-d') . ' ' . $currentSchedule->shift->start_time);
                if ($checkInTime->gt($shiftStart)) {
                    $attendance->come_late = true;
                    $attendance->come_early = false;
                } else {
                    $attendance->come_early = true;
                    $attendance->come_late = false;
                }
            }

            $attendance->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee checked in successfully at ' . $checkInTime->format('H:i')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to check in: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export attendance to CSV
     */
    public function export(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        // Get all active employees
        $allEmployees = Employee::where('status', 'active')
            ->with(['department', 'personalInfo'])
            ->orderBy('employee_code')
            ->get();

        // Get current shifts
        $currentSchedules = EmployeeSchedule::with('shift')
            ->whereIn('employee_id', $allEmployees->pluck('id'))
            ->where('start_date', '<=', $selectedDate)
            ->where(function ($q) use ($selectedDate) {
                $q->where('end_date', '>=', $selectedDate)
                  ->orWhereNull('end_date');
            })
            ->get()
            ->keyBy('employee_id');

        // Get attendance
        $existingAttendances = Attendance::with(['shift', 'leave.leaveType'])
            ->whereDate('date', $selectedDate)
            ->get()
            ->keyBy('employee_id');

        $filename = 'attendance_' . $selectedDate->format('Y-m-d') . '.csv';

        // UTF-8 BOM for Excel compatibility
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $handle = fopen('php://output', 'w');
        
        // UTF-8 BOM
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header row
        fputcsv($handle, [
            'No',
            'Employee Code',
            'Employee Name (EN)',
            'Employee Name (KH)',
            'Department',
            'Shift',
            'Shift Time',
            'Check In',
            'Check Out',
            'Working Hours',
            'Late (min)',
            'Overtime (min)',
            'Status',
            'Remark'
        ]);

        $no = 1;
        foreach ($allEmployees as $employee) {
            $schedule = $currentSchedules->get($employee->id);
            $attendance = $existingAttendances->get($employee->id);

            $checkIn = $attendance?->check_in ? Carbon::parse($attendance->check_in)->format('H:i') : '-';
            $checkOut = $attendance?->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : '-';

            $workingHours = '-';
            if ($attendance?->check_in && $attendance?->check_out) {
                $minutes = Carbon::parse($attendance->check_in)->diffInMinutes(Carbon::parse($attendance->check_out));
                $workingHours = number_format($minutes / 60, 2) . ' hrs';
            }

            $lateMinutes = '-';
            if ($attendance?->come_late && $attendance?->shift && $attendance?->check_in) {
                $shiftStart = Carbon::parse($selectedDate->format('Y-m-d') . ' ' . $attendance->shift->start_time);
                $checkInTime = Carbon::parse($attendance->check_in);
                $lateMinutes = $shiftStart->diffInMinutes($checkInTime);
            }

            $status = 'Absent';
            if ($attendance) {
                if ($attendance->leave_id) $status = 'On Leave';
                elseif ($attendance->holiday_id) $status = 'Holiday';
                elseif ($attendance->check_in && $attendance->check_out) $status = $attendance->come_late ? 'Present (Late)' : 'Present';
                elseif ($attendance->check_in) $status = 'In Progress';
            }

            fputcsv($handle, [
                $no++,
                $employee->employee_code ?? '-',
                $employee->personalInfo?->full_name_en ?? '-',
                $employee->personalInfo?->full_name_kh ?? '-',
                $employee->department?->department_name ?? '-',
                $schedule?->shift?->name ?? '-',
                $schedule?->shift ? ($schedule->shift->start_time . ' - ' . $schedule->shift->end_time) : '-',
                $checkIn,
                $checkOut,
                $workingHours,
                $lateMinutes,
                $attendance?->overtime_minutes > 0 ? $attendance->overtime_minutes : '-',
                $status,
                $attendance?->remark ?? '-',
            ]);
        }

        fclose($handle);
        exit;
    }

    /**
     * View employee attendance history
     */
    public function employeeAttendance($employeeId)
    {
        $employee = Employee::with('department')->findOrFail($employeeId);
        
        $attendances = Attendance::where('employee_id', $employeeId)
            ->with(['shift', 'leave'])
            ->orderBy('date', 'desc')
            ->paginate(30);

        // Calculate statistics
        $stats = [
            'totalDays' => Attendance::where('employee_id', $employeeId)->count(),
            'presentDays' => Attendance::where('employee_id', $employeeId)
                ->whereNotNull('check_in')
                ->count(),
            'lateDays' => Attendance::where('employee_id', $employeeId)
                ->where('come_late', true)
                ->count(),
            'leaveDays' => Attendance::where('employee_id', $employeeId)
                ->whereNotNull('leave_id')
                ->count(),
        ];

        return view('hr.attendance.employee-history', compact('employee', 'attendances', 'stats'));
    }
}