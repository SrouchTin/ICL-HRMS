<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\User;

class EmployeeLeaveController extends Controller
{
    /**
     * Get leave balance for employee and leave type
     */
public function getBalance(Request $request)
{
    try {
        $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
        ]);

        $employeeId   = $request->employee_id;
        $leaveTypeId  = $request->leave_type_id;
        $currentYear  = now()->year; // 2026

        $balance = LeaveBalance::where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $currentYear)
            ->first();

        if (!$balance) {
            $leaveType = LeaveType::find($leaveTypeId);
            $totalDays = $this->getDefaultLeaveDays($leaveType->name);
            
            $balance = LeaveBalance::create([
                'employee_id' => $employeeId,
                'leave_type_id' => $leaveTypeId,
                'year' => $currentYear,
                'total_days' => $totalDays,
                'used_days' => 0,
                'remaining_days' => $totalDays,
            ]);
            
            Log::info("Auto-created leave balance for Employee ID: {$employeeId}, Leave Type: {$leaveType->name}, Days: {$totalDays}");
        }

        return response()->json([
            'total'     => (float) $balance->total_days,
            'used'      => (float) $balance->used_days,
            'remaining' => (float) $balance->remaining_days,
        ]);
    } catch (\Exception $e) {
        Log::error('Leave balance fetch error: ' . $e->getMessage());
        return response()->json(['total' => 0, 'used' => 0, 'remaining' => 0], 500);
    }
}

    /**
     * Show pending leave requests for approval (Supervisor view)
     */
    public function pendingApprovals(Request $request)
    {
        $query = Auth::user()->employee->leavesToApprove()
            ->where('status', 'Pending')
            ->with(['employee.personalInfo', 'leaveType', 'personInCharge.personalInfo']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee.personalInfo', fn($q) => $q->where('full_name_en', 'like', "%{$search}%"))
                  ->orWhereHas('employee', fn($q) => $q->where('employee_code', 'like', "%{$search}%"));
        }

        $leaves = $query->latest()->paginate(15)->withQueryString();

        return view('employee.leaves.leave_pending', compact('leaves'));
    }

    /**
     * Show leave details in modal
     */
    public function details($id)
    {
        $leave = Leave::with(['employee.personalInfo', 'leaveType', 'personInCharge.personalInfo'])
            ->findOrFail($id);

        // Authorization check
        if (method_exists($leave, 'canBeApprovedBy') && !$leave->canBeApprovedBy(Auth::user()->employee)) {
            abort(403);
        }

        return view('employee.leaves.partials.details', compact('leave'));
    }

    /**
     * Approve a leave request (Supervisor approval)
     */
    public function approve(Leave $leave)
    {
        if ($leave->status !== 'Pending') {
            return back()->with('error', 'Only pending leave requests can be approved.');
        }

        if (method_exists($leave, 'canBeApprovedBy') && !$leave->canBeApprovedBy(Auth::user()->employee)) {
            return back()->with('error', 'You are not authorized to approve this request.');
        }

        DB::transaction(function () use ($leave) {
            $leave->update([
                'status'      => 'Approved',
                'approved_by' => Auth::user()->employee->id,
                'approved_at' => now(),
            ]);

            // Deduct from balance
            $year = now()->year;
            $balance = LeaveBalance::firstOrCreate(
                ['employee_id' => $leave->employee_id, 'leave_type_id' => $leave->leave_type_id, 'year' => $year],
                ['total_days' => 0, 'used_days' => 0, 'remaining_days' => 0]
            );

            $balance->increment('used_days', $leave->leave_days);
            $balance->remaining_days = $balance->total_days - $balance->used_days;
            $balance->save();
        });

        return back()->with('success', 'Leave request approved successfully!');
    }

    /**
     * Reject a leave request
     */
    public function reject(Request $request, Leave $leave)
    {
        if ($leave->status !== 'Pending') {
            return back()->with('error', 'Only pending leave requests can be rejected.');
        }

        if (method_exists($leave, 'canBeApprovedBy') && !$leave->canBeApprovedBy(Auth::user()->employee)) {
            return back()->with('error', 'You are not authorized to reject this request.');
        }

        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $leave->update([
            'status'        => 'Rejected',
            'reject_reason' => $request->reject_reason,
            'rejected_by'   => Auth::user()->employee->id,
            'rejected_at'   => now(),
            'approved_by'   => null,
            'approved_at'   => null,
        ]);

        return back()->with('success', 'Leave request rejected.');
    }

    /**
     * Default leave days by type
     */
private function getDefaultLeaveDays($leaveTypeName): float
{
    $defaults = [
        'Annual Leave'        => 18,
        'Sick Leave'          => 15,
        'Personal Leave'      => 7,
        'Maternity Leave'     => 90,
        'Paternity Leave'     => 7,
        'Unpaid Leave'        => 0,
        'Compassionate Leave' => 3,
    ];

    return $defaults[$leaveTypeName] ?? 0;
}

    public function index()
    {
        $user = Auth::user();

        if (!in_array(optional($user->role)->name, ['employee', 'user'])) {
            abort(403, 'Unauthorized access.');
        }

        if (!$user->employee) {
            abort(403, 'Employee profile not found.');
        }

        $leaves = $user->employee->leaves()
            ->with(['leaveType', 'approver.personalInfo', 'approvedByEmployee.personalInfo', 'rejectedByEmployee.personalInfo'])
            ->latest('from_date')
            ->paginate(15);

        return view('employee.leaves.index', compact('leaves'));
    }

public function getAvailablePersonInCharge(Request $request)
{
    $validated = $request->validate([
        'exclude_employee_id' => 'required|exists:employees,id',
        'from_date'           => 'required|date',
        'to_date'             => 'nullable|date|after_or_equal:from_date',
    ]);

    $excludeId = $validated['exclude_employee_id'];
    $fromDate  = $validated['from_date'];
    $toDate    = $validated['to_date'] ?? $fromDate;

    $overlappingEmployeeIds = DB::table('leaves')
        ->where('status', 'Approved')
        ->where(function ($q) use ($fromDate, $toDate) {
            $q->whereBetween('from_date', [$fromDate, $toDate])
              ->orWhereBetween('to_date', [$fromDate, $toDate])
              ->orWhere(fn($qq) => $qq->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate));
        })
        ->pluck('employee_id')
        ->unique()
        ->toArray();

    $employees = Employee::with('personalInfo')
        ->where('status', 'active')
        ->where('id', '!=', $excludeId)
        ->when(!empty($overlappingEmployeeIds), fn($q) => $q->whereNotIn('id', $overlappingEmployeeIds))
        ->orderBy('employee_code')
        ->get()
        ->map(fn($emp) => [
            'id'   => $emp->id,
            'text' => $emp->employee_code . ' - ' . ($emp->personalInfo?->full_name_en ?? $emp->personalInfo?->full_name_kh ?? 'No Name'),
        ])
        ->values();

    return response()->json($employees);
}
 public function create()
    {
        $authEmployee = Auth::user()->employee;
        if (!$authEmployee) abort(403, 'Employee profile not found.');

        $employees = Employee::where('status', 'active')
            ->orWhere('id', $authEmployee->id)
            ->with('personalInfo')
            ->orderByRaw("id = ? DESC", [$authEmployee->id])
            ->get();

        $leaveTypes = LeaveType::where('status', 'active')->get();

        $hrEmployees = Employee::whereHas('user.role', fn($q) => $q->where('name', 'hr'))
            ->where('status', 'active')
            ->with('personalInfo')
            ->orderBy('employee_code')
            ->get();

        return view('employee.leaves.create', compact('employees', 'leaveTypes', 'hrEmployees'));
    }
    /**
     * Store new leave request
     */
public function store(Request $request)
{
    Log::info('=== LEAVE STORE METHOD CALLED ===', $request->all());

    $authEmployee = Auth::user()->employee;

    if (!$authEmployee) {
        Log::error('Employee profile not found for user ID: ' . Auth::id());
        return back()
            ->withInput()
            ->with('error', 'Employee profile not found.');
    }

    $validated = $request->validate([
        'subject'            => 'required|string|max:255',
        'employee_id'        => 'required|exists:employees,id',
        'person_incharge_id' => 'required|exists:employees,id|different:employee_id',
        'leave_type_id'      => 'required|exists:leave_types,id',
        'leave_for'          => 'required|in:full_day,half_day',
        'half_day_type'      => 'required_if:leave_for,half_day|in:morning,afternoon|nullable',
        'from_date'          => 'required|date',
        'to_date'            => 'required|date|after_or_equal:from_date',
        'reason'             => 'required|string|max:1000',
        'remark'             => 'nullable|string|max:1000',
        'flow_type'          => 'required|in:supervisor,hr',
        'hr_id'              => 'required_if:flow_type,hr|exists:employees,id|nullable',
    ]);

    Log::info('Validation passed', $validated);

    return DB::transaction(function () use ($validated) {
        $from = Carbon::parse($validated['from_date']);
        $to   = Carbon::parse($validated['to_date']);
        $totalCalendarDays = $from->diffInDays($to) + 1;

        // Calculate leave days — last day is half when half_day selected
        if ($validated['leave_for'] === 'full_day') {
            $leaveDays = $totalCalendarDays;
            $halfDayType = null;
        } else {
            $leaveDays = $totalCalendarDays === 1 ? 0.5 : ($totalCalendarDays - 1) + 0.5;
            $halfDayType = $validated['half_day_type'];
        }

        Log::info("Leave calculation: {$validated['leave_for']}, Days: {$totalCalendarDays}, Requested: {$leaveDays}");

        // Check leave balance
        $currentYear = now()->year;
        $balance = LeaveBalance::firstOrCreate(
            [
                'employee_id'   => $validated['employee_id'],
                'leave_type_id' => $validated['leave_type_id'],
                'year'          => $currentYear,
            ],
            ['total_days' => 0, 'used_days' => 0, 'remaining_days' => 0] // fallback
        );

        if ($leaveDays > $balance->remaining_days) {
            return back()
                ->withInput()
                ->with('error', "Insufficient leave balance. You have only {$balance->remaining_days} day(s) remaining, but requested {$leaveDays}.");
        }

        // Check Person In Charge availability (exclude half-day overlaps)
        $picOverlap = Leave::where('employee_id', $validated['person_incharge_id'])
            ->where('status', 'Approved')
            ->where(function ($query) use ($from, $to) {
                $query->whereBetween('from_date', [$from, $to])
                      ->orWhereBetween('to_date', [$from, $to])
                      ->orWhere(function ($q) use ($from, $to) {
                          $q->where('from_date', '<=', $from)
                            ->where('to_date', '>=', $to);
                      });
            })
            ->exists();

        if ($picOverlap) {
            return back()
                ->withInput()
                ->withErrors(['person_incharge_id' => 'The selected Person In Charge is unavailable during your requested period. Please choose another.']);
        }

        // Determine approver based on flow_type
        if ($validated['flow_type'] === 'hr') {
            $approverId = $validated['hr_id'];
        } else {
            $employee = Employee::find($validated['employee_id']);
            if (!$employee->supervisor_id) {
                return back()
                    ->withInput()
                    ->with('error', 'This employee has no assigned supervisor. Please select HR approval flow or contact administrator.');
            }
            $approverId = $employee->supervisor_id;
        }

        // Create the leave request — INCLUDING flow_type!
        $leave = Leave::create([
            'subject'            => $validated['subject'],
            'employee_id'        => $validated['employee_id'],
            'person_incharge_id' => $validated['person_incharge_id'],
            'approver_id'        => $approverId,
            'leave_type_id'      => $validated['leave_type_id'],
            'leave_for'          => $validated['leave_for'],
            'half_day_type'      => $halfDayType,
            'from_date'          => $from,
            'to_date'            => $to,
            'leave_days'         => $leaveDays,
            'reason'             => $validated['reason'],
            'remark'             => $validated['remark'],
            'status'             => 'Pending',
            'flow_type'          => $validated['flow_type'], // ← CRITICAL: Now saved!
        ]);

        Log::info("Leave request created successfully | ID: {$leave->id} | Flow: {$validated['flow_type']} | Approver ID: {$approverId}");

        $message = $validated['flow_type'] === 'hr'
            ? 'Leave request submitted successfully and sent to HR for approval!'
            : 'Leave request submitted successfully and sent to your Supervisor for approval!';

        return redirect()
            ->route('employee.leaves.index')
            ->with('success', $message);
    });
}

    /**
     * Show form to edit leave
     */
public function edit(Leave $leave)
{
    $this->authorizeLeave($leave);

    if ($leave->status !== 'Pending') {
        return redirect()
            ->route('employee.leaves.index')
            ->with('error', 'Only pending leave requests can be edited.');
    }

    // All active employees (for Person In Charge selection)
    $employees = Employee::where('status', 'active')
        ->with('personalInfo')
        ->orderBy('employee_code')
        ->get();

    // All active leave types
    $leaveTypes = LeaveType::where('status', 'active')->get();

    // HR employees only — for HR approval flow selection
    $hrEmployees = Employee::whereHas('user.role', fn($q) => $q->where('name', 'hr'))
        ->where('status', 'active')
        ->with('personalInfo')
        ->orderBy('employee_code')
        ->get();

    return view('employee.leaves.edit', compact(
        'leave',
        'employees',
        'leaveTypes',
        'hrEmployees'
    ));
}

    /**
     * Update leave request
     */
public function update(Request $request, Leave $leave)
{
    $this->authorizeLeave($leave);

    if ($leave->status !== 'Pending') {
        return redirect()
            ->route('employee.leaves.index')
            ->with('error', 'Only pending leave requests can be updated.');
    }

    $validated = $request->validate([
        'subject'            => 'required|string|max:255',
        'leave_type_id'      => 'required|exists:leave_types,id',
        'person_incharge_id' => 'required|exists:employees,id|different:employee_id',
        'leave_for'          => 'required|in:full_day,half_day',
        'half_day_type'      => 'required_if:leave_for,half_day|in:morning,afternoon|nullable',
        'from_date'          => 'required|date',
        'to_date'            => 'required|date|after_or_equal:from_date',
        'reason'             => 'required|string|max:1000',
        'remark'             => 'nullable|string|max:1000',
        'flow_type'          => 'required|in:supervisor,hr',
        'hr_id'              => 'required_if:flow_type,hr|exists:employees,id|nullable',
    ]);

    return DB::transaction(function () use ($validated, $leave) {
        $from = Carbon::parse($validated['from_date']);
        $to   = Carbon::parse($validated['to_date']);
        $totalCalendarDays = $from->diffInDays($to) + 1;

        // Calculate leave days — last day is half when half_day
        if ($validated['leave_for'] === 'full_day') {
            $leaveDays = $totalCalendarDays;
            $halfDayType = null;
        } else {
            $leaveDays = $totalCalendarDays === 1 ? 0.5 : ($totalCalendarDays - 1) + 0.5;
            $halfDayType = $validated['half_day_type'];
        }

        Log::info("Leave update calculation | ID: {$leave->id} | Type: {$validated['leave_for']} | Days: {$totalCalendarDays} | Requested: {$leaveDays}");

        // Check current leave balance
        $currentYear = now()->year;
        $balance = LeaveBalance::firstOrCreate(
            [
                'employee_id'   => $leave->employee_id,
                'leave_type_id' => $validated['leave_type_id'],
                'year'          => $currentYear,
            ],
            ['total_days' => 0, 'used_days' => 0, 'remaining_days' => 0]
        );

        if ($leaveDays > $balance->remaining_days) {
            return back()
                ->withInput()
                ->with('error', "Insufficient leave balance. Only {$balance->remaining_days} day(s) remaining, but you requested {$leaveDays}.");
        }

        // Check Person In Charge availability — exclude the current leave record
        $picOverlap = Leave::where('employee_id', $validated['person_incharge_id'])
            ->where('status', 'Approved')
            ->where('id', '!=', $leave->id) // Important: don't count current leave
            ->where(function ($query) use ($from, $to) {
                $query->whereBetween('from_date', [$from, $to])
                      ->orWhereBetween('to_date', [$from, $to])
                      ->orWhere(function ($q) use ($from, $to) {
                          $q->where('from_date', '<=', $from)
                            ->where('to_date', '>=', $to);
                      });
            })
            ->exists();

        if ($picOverlap) {
            return back()
                ->withInput()
                ->withErrors(['person_incharge_id' => 'The selected Person In Charge is not available during this period. Please choose another.']);
        }

        // Determine new approver based on flow_type
        if ($validated['flow_type'] === 'hr') {
            $approverId = $validated['hr_id'];
        } else {
            if (!$leave->employee->supervisor_id) {
                return back()
                    ->withInput()
                    ->with('error', 'This employee has no assigned supervisor. Please use HR approval flow.');
            }
            $approverId = $leave->employee->supervisor_id;
        }

        // Update the leave request — INCLUDING flow_type!
        $leave->update([
            'subject'            => $validated['subject'],
            'leave_type_id'      => $validated['leave_type_id'],
            'person_incharge_id' => $validated['person_incharge_id'],
            'approver_id'        => $approverId,
            'leave_for'          => $validated['leave_for'],
            'half_day_type'      => $halfDayType,
            'from_date'          => $from,
            'to_date'            => $to,
            'leave_days'         => $leaveDays,
            'reason'             => $validated['reason'],
            'remark'             => $validated['remark'],
            'flow_type'          => $validated['flow_type'], // ← NOW SAVED ON UPDATE!
        ]);

        Log::info("Leave request updated successfully | ID: {$leave->id} | Flow: {$validated['flow_type']} | Approver ID: {$approverId}");

        return redirect()
            ->route('employee.leaves.index')
            ->with('success', 'Leave request updated successfully.');
    });
}

    /**
     * Delete leave request
     */
    public function destroy(Leave $leave)
    {
        $this->authorizeLeave($leave);

        if ($leave->status !== 'Pending') {
            return back()->with('error', 'Only pending leaves can be deleted.');
        }

        try {
            $leave->delete();

            return redirect()
                ->route('employee.leaves.index')
                ->with('success', 'Leave request deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Leave deletion error: ' . $e->getMessage());

            return back()->with('error', 'Failed to delete leave request.');
        }
    }

    /**
     * Check if authenticated user can access this leave
     */
    private function authorizeLeave(Leave $leave)
    {
        $employee = Auth::user()->employee;

        if (!$employee || $leave->employee_id !== $employee->id) {
            abort(403, 'Unauthorized access.');
        }
    }
}