<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\LeaveBalance;

class EmployeeLeaveController extends Controller
{
    // =========================
    // LIST MY LEAVES
    // =========================

    public function getBalance(Request $request)
    {
        $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
        ]);

        $employeeId   = $request->employee_id;
        $leaveTypeId  = $request->leave_type_id;
        $currentYear  = now()->year; // 2025

        // Read directly from leave_balances table
        $balance = DB::table('leave_balances')
            ->where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $currentYear)
            ->select('total_days', 'used_days', 'remaining_days')
            ->first();

        // If no record (should not happen after seeding), return 0
        if (!$balance) {
            return response()->json([
                'total'     => 0,
                'used'      => 0,
                'remaining' => 0,
            ]);
        }

        return response()->json([
            'total'     => (float) $balance->total_days,
            'used'      => (float) $balance->used_days,
            'remaining' => (float) $balance->remaining_days,
        ]);
    }

public function index()
{
    // Force login
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login')->with('error', 'Please log in to continue.');
    }

    // Safe role check using Laravel's optional helper
    $roleName = optional($user->role)->name;

    if (!in_array($roleName, ['employee', 'user'])) {
        abort(403, 'Unauthorized access. This page is for employees only.');
    }

    // Safe employee relationship
    $employee = $user->employee;

    if (!$employee) {
        abort(404, 'Your user account is not linked to an employee record. Contact HR.');
    }

    // Fetch leaves
    $leaves = Leave::with(['leaveType', 'approver'])
        ->where('employee_id', $employee->id)
        ->latest('from_date')
        ->paginate(15);

    return view('employee.leaves.index', compact('leaves'));
}

    // =========================
    // CREATE FORM
    // =========================
    public function create()
    {
        $authEmployee = Auth::user()->employee;

        if (!$authEmployee) {
            abort(403, 'Employee profile not found.');
        }

        $employees  = Employee::where('status', 'active')->get();
        $leaveTypes = LeaveType::where('status', 'active')->get();

        return view('employee.leaves.create', compact('employees', 'leaveTypes'));
    }

    // =========================
    // STORE LEAVE
    // =========================
    public function store(Request $request)
    {
        $authEmployee = Auth::user()->employee;

        if (!$authEmployee) {
            abort(403, 'Employee profile not found.');
        }

        $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'leave_for'     => 'required|in:full_day,half_day',
            'from_date'     => 'required|date',
            'to_date'       => 'required_if:leave_for,full_day|date|after_or_equal:from_date',
            'reason'        => 'required|string|max:1000',
            'remark'        => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $from = Carbon::parse($request->from_date);

            $to = $request->leave_for === 'half_day'
                ? $from
                : Carbon::parse($request->to_date);

            // ✅ Calculate leave days
            $leaveDays = $request->leave_for === 'half_day'
                ? 0.5
                : $from->diffInDays($to) + 1;

            Leave::create([
                'employee_id'   => $request->employee_id,
                'leave_type_id' => $request->leave_type_id,
                'leave_for'     => $request->leave_for,
                'from_date'     => $from,
                'to_date'       => $to,
                'leave_days'    => $leaveDays,
                'reason'        => $request->reason,
                'remark'        => $request->remark,
                'status'        => 'Pending',
            ]);

            DB::commit();

            return redirect()
                ->route('employee.leaves.index')
                ->with('success', 'Leave request submitted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Failed to submit leave request.');
        }
    }

    // =========================
    // EDIT LEAVE
    // =========================
    public function edit(Leave $leave)
    {
        $this->authorizeLeave($leave);

        if ($leave->status !== 'Pending') {
            return redirect()
                ->route('employee.leaves.index')
                ->with('error', 'Only pending leaves can be edited.');
        }

        $leaveTypes = LeaveType::where('status', 'active')->get();

        return view('employee.leaves.edit', compact('leave', 'leaveTypes'));
    }

    // =========================
    // UPDATE LEAVE
    // =========================
    public function update(Request $request, Leave $leave)
    {
        $this->authorizeLeave($leave);

        if ($leave->status !== 'Pending') {
            return redirect()
                ->route('employee.leaves.index')
                ->with('error', 'Only pending leaves can be updated.');
        }

        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'leave_for'     => 'required|in:full_day,half_day',
            'from_date'     => 'required|date',
            'to_date'       => 'required_if:leave_for,full_day|date|after_or_equal:from_date',
            'reason'        => 'required|string|max:1000',
            'remark'        => 'nullable|string|max:1000',
        ]);

        try {
            $from = Carbon::parse($request->from_date);

            $to = $request->leave_for === 'half_day'
                ? $from
                : Carbon::parse($request->to_date);

            $leaveDays = $request->leave_for === 'half_day'
                ? 0.5
                : $from->diffInDays($to) + 1;

            $leave->update([
                'leave_type_id' => $request->leave_type_id,
                'leave_for'     => $request->leave_for,
                'from_date'     => $from,
                'to_date'       => $to,
                'leave_days'    => $leaveDays,
                'reason'        => $request->reason,
                'remark'        => $request->remark,
            ]);

            return redirect()
                ->route('employee.leaves.index')
                ->with('success', 'Leave request updated successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to update leave request.');
        }
    }

    // =========================
    // DELETE LEAVE
    // =========================
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
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to delete leave request.');
        }
    }

    // =========================
    // SECURITY CHECK
    // =========================
    private function authorizeLeave(Leave $leave)
    {
        $employee = Auth::user()->employee;

        if (!$employee || $leave->employee_id !== $employee->id) {
            abort(403, 'Unauthorized access.');
        }
    }
}
