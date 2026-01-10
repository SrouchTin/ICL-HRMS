<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HROwnLeaveController extends Controller
{
    /**
     * List my leaves
     */
    public function index()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('hr.dashboard')->with('error', 'Employee profile not found.');
        }

        $leaves = $employee->leaves()
            ->with(['leaveType', 'approver.personalInfo', 'personInCharge.personalInfo'])
            ->latest('from_date')
            ->paginate(15);

        return view('hr.leaves.my_leave', compact('leaves'));
    }

    /**
     * Show create form
     */
public function create()
{
    $currentUser = Auth::user();

    $currentEmployee = $currentUser->employee;

    if (!$currentEmployee) {
        return redirect()->route('hr.dashboard')
            ->with('error', 'Your employee profile is not linked.');
    }

    $leaveTypes = LeaveType::where('status', 'active')->get();

    // FIX: Use 'role' (singular) instead of 'roles'
    $hrEmployees = Employee::where('status', 'active')
        ->whereHas('user', function ($query) {
            $query->whereHas('role', function ($q) {  // ← 'role' not 'roles'
                $q->where('name', 'hr');
            });
        })
        ->with('personalInfo')
        ->orderBy('employee_code')
        ->get();

    return view('hr.leaves.create_own', compact('currentEmployee', 'leaveTypes', 'hrEmployees'));
}
    /**
     * API: Get available Person In Charge (all employees not on leave)
     */
public function availablePersonInCharge(Request $request)
{
    $request->validate([
        'exclude_employee_id' => 'required|exists:employees,id',
        'from_date'           => 'required|date',
        'to_date'             => 'required|date|after_or_equal:from_date',
    ]);

    $excludeId = $request->exclude_employee_id;
    $from = $request->from_date;
    $to = $request->to_date;

    // Fix: Use case-insensitive comparison for status
    $overlappingEmployeeIds = DB::table('leaves')
        ->whereIn('status', ['Approved', 'approved', 'APPROVED']) // cover all cases
        ->where(function ($q) use ($from, $to) {
            $q->whereBetween('from_date', [$from, $to])
              ->orWhereBetween('to_date', [$from, $to])
              ->orWhere(function ($qq) use ($from, $to) {
                  $qq->where('from_date', '<=', $to)
                     ->where('to_date', '>=', $from);
              });
        })
        ->pluck('employee_id')
        ->unique()
        ->toArray();

    $employees = Employee::with('personalInfo')
        ->where('status', 'active')
        ->where('id', '!=', $excludeId)
        ->when(!empty($overlappingEmployeeIds), function ($q) use ($overlappingEmployeeIds) {
            return $q->whereNotIn('id', $overlappingEmployeeIds);
        })
        ->orderBy('employee_code')
        ->get()
        ->map(function ($emp) {
            $name = $emp->personalInfo?->full_name_en 
                ?? $emp->personalInfo?->full_name_kh 
                ?? 'No Name';
            return [
                'id'   => $emp->id,
                'text' => $emp->employee_code . ' - ' . $name,
            ];
        })
        ->values();

    // Debug: Remove this in production
    // Log::info('Available PIC:', $employees->toArray());

    return response()->json($employees);
}

    /**
     * Store new leave request
     */
    public function store(Request $request)
    {
        $authEmployee = Auth::user()->employee;

        if (!$authEmployee) {
            return back()->with('error', 'Employee profile not found.');
        }

        $validated = $request->validate([
            'employee_id'        => 'required|exists:employees,id',
            'leave_type_id'      => 'required|exists:leave_types,id',
            'flow_type'          => 'required|in:supervisor,hr',
            'hr_id'              => 'required_if:flow_type,hr|exists:employees,id|nullable',
            'leave_for'          => 'required|in:full_day,half_day',
            'half_day_type'      => 'required_if:leave_for,half_day|in:morning,afternoon|nullable',
            'from_date'          => 'required|date',
            'to_date'            => 'required|date|after_or_equal:from_date',
            'subject'            => 'required|string|max:255',
            'reason'             => 'required|string|max:1000',
            'remark'             => 'nullable|string|max:1000',
            'person_incharge_id' => 'required|exists:employees,id|different:employee_id',
        ]);

        $selectedEmployee = Employee::find($validated['employee_id']);
        if (!$selectedEmployee || !$selectedEmployee->user?->hasRole('hr')) {
            return back()->withInput()->with('error', 'You can only create leave requests for HR employees.');
        }

        return DB::transaction(function () use ($validated, $selectedEmployee) {
            $from = Carbon::parse($validated['from_date']);
            $to   = Carbon::parse($validated['to_date']);
            $totalDays = $from->diffInDays($to) + 1;

            $leaveDays = $validated['leave_for'] === 'full_day'
                ? $totalDays
                : ($totalDays === 1 ? 0.5 : ($totalDays - 1) + 0.5);

            $halfDayType = $validated['leave_for'] === 'half_day' ? $validated['half_day_type'] : null;

            // Balance check
            $year = now()->year;
            $balance = LeaveBalance::firstOrCreate(
                ['employee_id' => $validated['employee_id'], 'leave_type_id' => $validated['leave_type_id'], 'year' => $year],
                ['total_days' => 0, 'used_days' => 0, 'remaining_days' => 0]
            );

            if ($leaveDays > $balance->remaining_days) {
                return back()->withInput()->with('error', "Insufficient leave balance. Only {$balance->remaining_days} day(s) remaining.");
            }

            // PIC availability
            $picOverlap = Leave::where('employee_id', $validated['person_incharge_id'])
                ->where('status', 'Approved')
                ->where(function ($q) use ($from, $to) {
                    $q->whereBetween('from_date', [$from, $to])
                      ->orWhereBetween('to_date', [$from, $to])
                      ->orWhere(fn($qq) => $qq->where('from_date', '<=', $from)->where('to_date', '>=', $to));
                })
                ->exists();

            if ($picOverlap) {
                return back()->withInput()->withErrors(['person_incharge_id' => 'Selected Person In Charge is not available.']);
            }

            // Approver
            $approverId = $validated['flow_type'] === 'hr'
                ? $validated['hr_id']
                : ($selectedEmployee->supervisor_id ?? null);

            if ($validated['flow_type'] === 'supervisor' && !$approverId) {
                return back()->withInput()->with('error', 'No supervisor assigned. Please use HR approval.');
            }

            Leave::create([
                'employee_id'        => $validated['employee_id'],
                'leave_type_id'      => $validated['leave_type_id'],
                'flow_type'          => $validated['flow_type'],
                'hr_id'              => $validated['flow_type'] === 'hr' ? $validated['hr_id'] : null,
                'approver_id'        => $approverId,
                'leave_for'          => $validated['leave_for'],
                'half_day_type'      => $halfDayType,
                'from_date'          => $from,
                'to_date'            => $to,
                'leave_days'         => $leaveDays,
                'subject'            => $validated['subject'],
                'reason'             => $validated['reason'],
                'remark'             => $validated['remark'],
                'person_incharge_id' => $validated['person_incharge_id'],
                'status'             => 'Pending',
                'requested_by'       => Auth::id(),
                'requested_at'       => now(),
            ]);

            $message = $validated['flow_type'] === 'hr'
                ? 'Leave request sent to HR for approval!'
                : 'Leave request sent to Supervisor for approval!';

            return redirect()->route('hr.own-leave.index')->with('success', $message);
        });
    }

    /**
     * Show edit form
     */
    public function edit(Leave $leave)
    {
        $currentEmployee = Auth::user()->employee;

        if (!$currentEmployee || $leave->employee_id !== $currentEmployee->id) {
            return redirect()->route('hr.own-leave.index')->with('error', 'Unauthorized.');
        }

        if ($leave->status !== 'Pending') {
            return redirect()->route('hr.own-leave.index')->with('error', 'Only pending requests can be edited.');
        }

        $leaveTypes = LeaveType::where('status', 'active')->get();

        $hrEmployees = Employee::whereHas('user.role', fn($q) => $q->where('name', 'hr'))
            ->where('status', 'active')
            ->with('personalInfo')
            ->get();

        return view('hr.leaves.edit_own', compact('leave', 'leaveTypes', 'hrEmployees'));
    }

    /**
     * Update leave request
     */
    public function update(Request $request, Leave $leave)
    {
        $currentEmployee = Auth::user()->employee;

        if (!$currentEmployee || $leave->employee_id !== $currentEmployee->id) {
            return back()->with('error', 'Unauthorized.');
        }

        if ($leave->status !== 'Pending') {
            return back()->with('error', 'Only pending requests can be updated.');
        }

        $validated = $request->validate([
            'leave_type_id'      => 'required|exists:leave_types,id',
            'flow_type'          => 'required|in:supervisor,hr',
            'hr_id'              => 'required_if:flow_type,hr|exists:employees,id|nullable',
            'leave_for'          => 'required|in:full_day,half_day',
            'half_day_type'      => 'required_if:leave_for,half_day|in:morning,afternoon|nullable',
            'from_date'          => 'required|date',
            'to_date'            => 'required|date|after_or_equal:from_date',
            'subject'            => 'required|string|max:255',
            'reason'             => 'required|string|max:1000',
            'remark'             => 'nullable|string|max:1000',
            'person_incharge_id' => 'required|exists:employees,id|different:employee_id',
        ]);

        return DB::transaction(function () use ($validated, $leave) {
            $from = Carbon::parse($validated['from_date']);
            $to   = Carbon::parse($validated['to_date']);
            $totalDays = $from->diffInDays($to) + 1;

            $leaveDays = $validated['leave_for'] === 'full_day'
                ? $totalDays
                : ($totalDays === 1 ? 0.5 : ($totalDays - 1) + 0.5);

            $halfDayType = $validated['leave_for'] === 'half_day' ? $validated['half_day_type'] : null;

            $year = now()->year;
            $balance = LeaveBalance::firstOrCreate(
                ['employee_id' => $leave->employee_id, 'leave_type_id' => $validated['leave_type_id'], 'year' => $year],
                ['total_days' => 0, 'used_days' => 0, 'remaining_days' => 0]
            );

            if ($leaveDays > $balance->remaining_days) {
                return back()->withInput()->with('error', "Insufficient balance. Only {$balance->remaining_days} day(s) left.");
            }

            $picOverlap = Leave::where('employee_id', $validated['person_incharge_id'])
                ->where('status', 'Approved')
                ->where('id', '!=', $leave->id)
                ->where(function ($q) use ($from, $to) {
                    $q->whereBetween('from_date', [$from, $to])
                      ->orWhereBetween('to_date', [$from, $to])
                      ->orWhere(fn($qq) => $qq->where('from_date', '<=', $from)->where('to_date', '>=', $to));
                })
                ->exists();

            if ($picOverlap) {
                return back()->withInput()->withErrors(['person_incharge_id' => 'Person In Charge not available.']);
            }

            $approverId = $validated['flow_type'] === 'hr'
                ? $validated['hr_id']
                : ($leave->employee->supervisor_id ?? null);

            if ($validated['flow_type'] === 'supervisor' && !$approverId) {
                return back()->withInput()->with('error', 'No supervisor. Use HR flow.');
            }

            $leave->update([
                'leave_type_id'      => $validated['leave_type_id'],
                'flow_type'          => $validated['flow_type'],
                'hr_id'              => $validated['flow_type'] === 'hr' ? $validated['hr_id'] : null,
                'approver_id'        => $approverId,
                'leave_for'          => $validated['leave_for'],
                'half_day_type'      => $halfDayType,
                'from_date'          => $from,
                'to_date'            => $to,
                'leave_days'         => $leaveDays,
                'subject'            => $validated['subject'],
                'reason'             => $validated['reason'],
                'remark'             => $validated['remark'],
                'person_incharge_id' => $validated['person_incharge_id'],
            ]);

            return redirect()->route('hr.own-leave.index')->with('success', 'Leave request updated successfully!');
        });
    }

    /**
     * Delete pending leave
     */
    public function destroy(Leave $leave)
    {
        $currentEmployee = Auth::user()->employee;

        if (!$currentEmployee || $leave->employee_id !== $currentEmployee->id) {
            return back()->with('error', 'Unauthorized.');
        }

        if ($leave->status !== 'Pending') {
            return back()->with('error', 'Only pending requests can be deleted.');
        }

        $leave->delete();

        return redirect()->route('hr.own-leave.index')->with('success', 'Leave request deleted.');
    }
        /**
     * Cancel/Reject own pending leave request
     */
    public function reject(Leave $leave)
    {
        $currentEmployee = Auth::user()->employee;

        // Security checks
        if (!$currentEmployee) {
            return back()->with('error', 'Employee profile not found.');
        }

        if ($leave->employee_id !== $currentEmployee->id) {
            return back()->with('error', 'Unauthorized action.');
        }

        if ($leave->status !== 'Pending') {
            return back()->with('error', 'Only pending leave requests can be cancelled.');
        }

        // Update status to Rejected (or Cancelled if you prefer a different status)
        $leave->update([
            'status'       => 'Rejected', // You can change this to 'Cancelled' if your system uses that
            'rejected_by'  => Auth::id(),
            'rejected_at'  => now(),
            // Optional: add a remark
            // 'remark' => $leave->remark . "\n[Cancelled by requester on " . now() . "]",
        ]);

        return redirect()->route('hr.own-leave.index')
            ->with('success', 'Your leave request has been cancelled successfully.');
    }
}