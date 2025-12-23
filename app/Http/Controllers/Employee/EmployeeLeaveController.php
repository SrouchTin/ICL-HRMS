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
            $currentYear  = now()->year;

            $balance = DB::table('leave_balances')
                ->where('employee_id', $employeeId)
                ->where('leave_type_id', $leaveTypeId)
                ->where('year', $currentYear)
                ->select('total_days', 'used_days', 'remaining_days')
                ->first();

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
        } catch (\Exception $e) {
            Log::error('Leave balance fetch error: ' . $e->getMessage());

            return response()->json([
                'total'     => 0,
                'used'      => 0,
                'remaining' => 0,
            ], 500);
        }
    }

    /**
     * Display list of leaves
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to continue.');
        }

        $roleName = optional($user->role)->name;

        if (!in_array($roleName, ['employee', 'user'])) {
            abort(403, 'Unauthorized access. This page is for employees only.');
        }

        if (!$user->employee) {
            abort(404, 'Your user account is not linked to an employee record. Please contact HR.');
        }

        $leaves = $user->employee->leaves()
            ->with(['leaveType', 'approver'])
            ->latest('from_date')
            ->paginate(15);

        $leaves->appends(request()->query());

        return view('employee.leaves.index', compact('leaves'));
    }

public function getAvailablePersonInCharge(Request $request)
{
    // Validate input strictly
    $validated = $request->validate([
        'exclude_employee_id' => 'required|exists:employees,id',
        'from_date'           => 'required|date',
        'to_date'             => 'nullable|date|after_or_equal:from_date',
    ]);

    $excludeId = $validated['exclude_employee_id'];
    $fromDate  = $validated['from_date'];
    $toDate    = $validated['to_date'] ?? $fromDate;

    // Get employee IDs with APPROVED overlapping leave
    $overlappingEmployeeIds = DB::table('leaves')
        ->where('status', 'approved')
        ->where(function ($q) use ($fromDate, $toDate) {
            $q->whereBetween('from_date', [$fromDate, $toDate])
              ->orWhereBetween('to_date', [$fromDate, $toDate])
              ->orWhere(function ($qq) use ($fromDate, $toDate) {
                  $qq->where('from_date', '<=', $fromDate)
                     ->where('to_date', '>=', $toDate);
              });
        })
        ->pluck('employee_id')
        ->unique() 
        ->toArray();

    // Build available employees list
    $employees = Employee::with('personalInfo')
        ->where('status', 'active')
        ->where('id', '!=', $excludeId)
        ->when(!empty($overlappingEmployeeIds), function ($query) use ($overlappingEmployeeIds) {
            return $query->whereNotIn('id', $overlappingEmployeeIds);
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

    return response()->json($employees);
}

    /**
     * Show form to create new leave
     */
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

    /**
     * Store new leave request
     */
public function store(Request $request)
{
    Log::info('=== LEAVE STORE METHOD CALLED ===');
    Log::info('Request data:', $request->all());

    try {
        $authEmployee = Auth::user()->employee;

        if (!$authEmployee) {
            Log::error('Employee profile not found for user: ' . Auth::id());
            return back()
                ->withInput()
                ->with('error', 'Employee profile not found.');
        }

        // Validate request
        $validated = $request->validate([
            'subject'            => 'required|string|max:255',
            'employee_id'        => 'required|exists:employees,id',
            'person_incharge_id' => 'required|exists:employees,id|different:employee_id',
            'leave_type_id'      => 'required|exists:leave_types,id',
            'leave_for'          => 'required|in:full_day,half_day',
            'half_day_type'      => 'required_if:leave_for,half_day|in:morning,afternoon|nullable',
            'from_date'          => 'required|date',
            'to_date'            => 'nullable|date|after_or_equal:from_date',
            'reason'             => 'required|string|max:1000',
            'remark'             => 'nullable|string|max:1000',
        ]);

        Log::info('Validation passed');

        // Validate to_date for full_day
        if ($validated['leave_for'] === 'full_day' && empty($validated['to_date'])) {
            return back()
                ->withInput()
                ->withErrors(['to_date' => 'To date is required for full day leave.']);
        }

        DB::beginTransaction();

        try {
            $from = Carbon::parse($validated['from_date']);

            // Determine leave period, days, and half_day_type
            if ($validated['leave_for'] === 'half_day') {
                $to = $from->copy();
                $leaveDays = 0.5;
                $leavePeriod = 'Half Day';
                $halfDayType = $validated['half_day_type']; // ← Correct: take from form
            } else {
                $to = Carbon::parse($validated['to_date']);
                $leaveDays = $from->diffInDays($to) + 1;
                $leavePeriod = 'Full Day';
                $halfDayType = null; // ← Only null for full day
            }

            Log::info("Calculated - Period: {$leavePeriod}, Days: {$leaveDays}");

            // Check balance
            $currentYear = now()->year;
            $balance = LeaveBalance::where('employee_id', $validated['employee_id'])
                ->where('leave_type_id', $validated['leave_type_id'])
                ->where('year', $currentYear)
                ->first();

            if ($balance && $leaveDays > $balance->remaining_days) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->with('error', "Insufficient leave balance. You have {$balance->remaining_days} days remaining but requested {$leaveDays} days.");
            }

            // === CHECK PERSON IN CHARGE AVAILABILITY ===
            $picOverlap = DB::table('leaves')
                ->where('employee_id', $validated['person_incharge_id'])
                ->where('status', 'approved')
                ->where(function ($q) use ($from, $to) {
                    $q->whereBetween('from_date', [$from->format('Y-m-d'), $to->format('Y-m-d')])
                      ->orWhereBetween('to_date', [$from->format('Y-m-d'), $to->format('Y-m-d')])
                      ->orWhere(function ($qq) use ($from, $to) {
                          $qq->where('from_date', '<=', $from->format('Y-m-d'))
                             ->where('to_date', '>=', $to->format('Y-m-d'));
                      });
                })
                ->exists();

            if ($picOverlap) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->withErrors(['person_incharge_id' => 'The selected Person In Charge is on approved leave during your requested period. Please choose someone else.']);
            }

            // Create leave — ONLY ONE half_day_type entry
            $leave = Leave::create([
                'subject'            => $validated['subject'],
                'employee_id'        => $validated['employee_id'],
                'person_incharge_id' => $validated['person_incharge_id'],
                'leave_type_id'      => $validated['leave_type_id'],
                'leave_period'       => $leavePeriod,
                'half_day_type'      => $halfDayType, // ← This is now correct
                'from_date'          => $from->format('Y-m-d'),
                'to_date'            => $to->format('Y-m-d'),
                'leave_days'         => $leaveDays,
                'reason'             => $validated['reason'],
                'remark'             => $validated['remark'] ?? null,
                'status'             => 'Pending',
            ]);

            Log::info("Leave created successfully with ID: {$leave->id}");

            DB::commit();

            return redirect()
                ->route('employee.leaves.index')
                ->with('success', 'Leave request submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Database error in leave creation: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()
                ->withInput()
                ->with('error', 'Database error: ' . $e->getMessage());
        }
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed: ' . json_encode($e->errors()));
        throw $e;
    } catch (\Exception $e) {
        Log::error('Unexpected error in leave store: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());

        return back()
            ->withInput()
            ->with('error', 'Failed to submit leave request. Error: ' . $e->getMessage());
    }
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
                ->with('error', 'Only pending leaves can be edited.');
        }

        $employees  = Employee::where('status', 'active')->get();
        $leaveTypes = LeaveType::where('status', 'active')->get();

        return view('employee.leaves.edit', compact('leave', 'employees', 'leaveTypes'));
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
            ->with('error', 'Only pending leaves can be updated.');
    }

    $validated = $request->validate([
        'leave_type_id'      => 'required|exists:leave_types,id',
        'leave_for'          => 'required|in:full_day,half_day',
        'from_date'          => 'required|date',
        'to_date'            => 'nullable|date|after_or_equal:from_date',
        'reason'             => 'required|string|max:1000',
        'remark'             => 'nullable|string|max:1000',
        'subject'            => 'required|string|max:255',
        'person_incharge_id' => 'required|exists:employees,id|different:employee_id',
        'half_day_type'      => 'required_if:leave_for,half_day|in:morning,afternoon|nullable',
    ]);

    if ($validated['leave_for'] === 'full_day' && empty($validated['to_date'])) {
        return back()
            ->withInput()
            ->withErrors(['to_date' => 'To date is required for full day leave.']);
    }

    DB::beginTransaction();

    try {
        $from = Carbon::parse($validated['from_date']);

        // Determine leave period, days, and half_day_type
        if ($validated['leave_for'] === 'half_day') {
            $to = $from->copy();
            $leaveDays = 0.5;
            $leavePeriod = 'Half Day';
            $halfDayType = $validated['half_day_type']; // ← Correct: take from form
        } else {
            $to = Carbon::parse($validated['to_date']);
            $leaveDays = $from->diffInDays($to) + 1;
            $leavePeriod = 'Full Day';
            $halfDayType = null; // ← Only null for full day
        }

        // Check balance
        $currentYear = now()->year;
        $balance = LeaveBalance::where('employee_id', $leave->employee_id)
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('year', $currentYear)
            ->first();

        if ($balance && $leaveDays > $balance->remaining_days) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', "Insufficient leave balance. You have {$balance->remaining_days} days remaining but requested {$leaveDays} days.");
        }

        // === CHECK PERSON IN CHARGE AVAILABILITY ===
        $picOverlap = DB::table('leaves')
            ->where('employee_id', $validated['person_incharge_id'])
            ->where('status', 'approved')
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('from_date', [$from->format('Y-m-d'), $to->format('Y-m-d')])
                  ->orWhereBetween('to_date', [$from->format('Y-m-d'), $to->format('Y-m-d')])
                  ->orWhere(function ($qq) use ($from, $to) {
                      $qq->where('from_date', '<=', $from->format('Y-m-d'))
                         ->where('to_date', '>=', $to->format('Y-m-d'));
                  });
            })
            ->exists();

        if ($picOverlap) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['person_incharge_id' => 'The selected Person In Charge is on approved leave during your requested period. Please choose someone else.']);
        }

        // Update leave — ONLY ONE half_day_type entry
        $leave->update([
            'subject'            => $validated['subject'],
            'person_incharge_id' => $validated['person_incharge_id'],
            'leave_type_id'      => $validated['leave_type_id'],
            'leave_period'       => $leavePeriod,
            'half_day_type'      => $halfDayType, // ← This is now correct
            'from_date'          => $from->format('Y-m-d'),
            'to_date'            => $to->format('Y-m-d'),
            'leave_days'         => $leaveDays,
            'reason'             => $validated['reason'],
            'remark'             => $validated['remark'] ?? null,
        ]);

        DB::commit();

        return redirect()
            ->route('employee.leaves.index')
            ->with('success', 'Leave request updated successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Leave update error: ' . $e->getMessage());

        return back()
            ->withInput()
            ->with('error', 'Failed to update leave request: ' . $e->getMessage());
    }
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
