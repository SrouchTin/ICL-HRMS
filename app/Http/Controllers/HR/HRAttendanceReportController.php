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

class HRAttendanceReportController extends Controller
{
    /**
     * Monthly Attendance Report
     */
    public function monthlyReport(Request $request)
    {
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $endDate = Carbon::parse($selectedMonth . '-01')->endOfMonth();

        // Get departments for filter
        $departments = Department::orderBy('department_name')->get();

        // Get employees
        $employeesQuery = Employee::where('status', 'active')
            ->with(['department', 'personalInfo']);

        // Filter by department
        if ($request->filled('department')) {
            $employeesQuery->where('department_id', $request->department);
        }

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $employeesQuery->where(function($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                  ->orWhereHas('personalInfo', function($q2) use ($search) {
                      $q2->where('full_name_en', 'like', "%{$search}%")
                         ->orWhere('full_name_kh', 'like', "%{$search}%");
                  });
            });
        }

        $employees = $employeesQuery->orderBy('employee_code')->get();

        // Get all attendance records for the month
        $attendances = Attendance::with(['leave.leaveType', 'shift'])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->groupBy('employee_id');

        // Get all days in the month with holiday info
        $daysInMonth = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $isWeekend = $currentDate->isWeekend();
            $holiday = Holiday::getHoliday($currentDate);
            
            $daysInMonth[] = [
                'date' => $currentDate->copy(),
                'day' => $currentDate->format('d'),
                'dayName' => $currentDate->format('D'),
                'isWeekend' => $isWeekend,
                'isHoliday' => $holiday ? true : false,
                'holidayName' => $holiday?->holiday_name,
                'isDayOff' => $isWeekend || $holiday ? true : false,
            ];
            $currentDate->addDay();
        }

        // Calculate statistics for each employee
        $employeeStats = [];
        foreach ($employees as $employee) {
            $employeeAttendances = $attendances->get($employee->id, collect());
            
            $stats = [
                'employee' => $employee,
                'total_days' => $daysInMonth,
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'leave' => 0,
                'weekend' => 0,
                'working_days' => 0,
                'attendance_rate' => 0,
            ];

            foreach ($daysInMonth as $day) {
                if ($day['isDayOff']) {
                    $stats['weekend']++; // Count both weekends and holidays as day off
                } else {
                    $stats['working_days']++;
                    
                    $att = $employeeAttendances->where('date', $day['date']->format('Y-m-d'))->first();
                    
                    if ($att) {
                        if ($att->leave_id) {
                            $stats['leave']++;
                        } elseif ($att->check_in) {
                            $stats['present']++;
                            if ($att->come_late) {
                                $stats['late']++;
                            }
                        } else {
                            $stats['absent']++;
                        }
                    } else {
                        // No record = absent (only on working days)
                        $stats['absent']++;
                    }
                }
            }

            // Calculate attendance rate
            if ($stats['working_days'] > 0) {
                $stats['attendance_rate'] = round(($stats['present'] / $stats['working_days']) * 100, 1);
            }

            $employeeStats[] = $stats;
        }

        // Overall statistics
        $overallStats = [
            'total_employees' => $employees->count(),
            'total_working_days' => collect($daysInMonth)->where('isDayOff', false)->count(),
            'total_present' => collect($employeeStats)->sum('present'),
            'total_absent' => collect($employeeStats)->sum('absent'),
            'total_late' => collect($employeeStats)->sum('late'),
            'total_leave' => collect($employeeStats)->sum('leave'),
            'avg_attendance_rate' => $employees->count() > 0 ? round(collect($employeeStats)->avg('attendance_rate'), 1) : 0,
        ];

        return view('hr.reports.monthly-attendance', compact(
            'employeeStats',
            'overallStats',
            'daysInMonth',
            'departments',
            'selectedMonth',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export Monthly Report to CSV
     */
    public function exportMonthly(Request $request)
    {
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $endDate = Carbon::parse($selectedMonth . '-01')->endOfMonth();

        // Get employees
        $employeesQuery = Employee::where('status', 'active')
            ->with(['department', 'personalInfo']);

        if ($request->filled('department')) {
            $employeesQuery->where('department_id', $request->department);
        }

        $employees = $employeesQuery->orderBy('employee_code')->get();

        // Get all attendance records
        $attendances = Attendance::with(['leave.leaveType'])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->groupBy('employee_id');

        // Get all days in the month
        $daysInMonth = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $daysInMonth[] = $currentDate->copy();
            $currentDate->addDay();
        }

        $filename = 'monthly_attendance_' . $selectedMonth . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        $handle = fopen('php://output', 'w');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        // Header row
        $headers = ['No', 'Employee Code', 'Name (EN)', 'Name (KH)', 'Department'];
        foreach ($daysInMonth as $day) {
            $headers[] = $day->format('d') . ' (' . $day->format('D') . ')';
        }
        $headers[] = 'Present';
        $headers[] = 'Absent';
        $headers[] = 'Late';
        $headers[] = 'Leave';
        $headers[] = 'Attendance %';

        fputcsv($handle, $headers);

        // Data rows
        $no = 1;
        foreach ($employees as $employee) {
            $employeeAttendances = $attendances->get($employee->id, collect());
            
            $row = [
                $no++,
                $employee->employee_code,
                $employee->personalInfo?->full_name_en ?? '-',
                $employee->personalInfo?->full_name_kh ?? '-',
                $employee->department?->department_name ?? '-',
            ];

            $present = 0;
            $absent = 0;
            $late = 0;
            $leave = 0;
            $workingDays = 0;

            foreach ($daysInMonth as $day) {
                $isDayOff = $day->isWeekend() || Holiday::isHoliday($day);
                
                if ($isDayOff) {
                    $row[] = '—'; // Day Off
                } else {
                    $workingDays++;
                    $att = $employeeAttendances->where('date', $day->format('Y-m-d'))->first();
                    
                    if ($att) {
                        if ($att->leave_id) {
                            $row[] = 'L';
                            $leave++;
                        } elseif ($att->check_in) {
                            if ($att->come_late) {
                                $row[] = 'P*';
                                $late++;
                            } else {
                                $row[] = 'P';
                            }
                            $present++;
                        } else {
                            $row[] = 'A';
                            $absent++;
                        }
                    } else {
                        $row[] = 'A';
                        $absent++;
                    }
                }
            }

            $attendanceRate = $workingDays > 0 ? round(($present / $workingDays) * 100, 1) : 0;

            $row[] = $present;
            $row[] = $absent;
            $row[] = $late;
            $row[] = $leave;
            $row[] = $attendanceRate . '%';

            fputcsv($handle, $row);
        }

        fclose($handle);
        exit;
    }

    /**
     * Get working days count (excluding weekends and holidays)
     */
    private function getWorkingDays($startDate, $endDate)
    {
        $count = 0;
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            if (!$current->isWeekend() && !Holiday::isHoliday($current)) {
                $count++;
            }
            $current->addDay();
        }
        
        return $count;
    }
}