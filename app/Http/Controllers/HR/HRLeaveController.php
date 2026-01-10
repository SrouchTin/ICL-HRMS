<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HRLeaveController extends Controller
{
    /**
     * Display the Leave Requests page for HR
     */
    public function index(Request $request)
    {
        // Base query: only pending leaves for HR approval
        $query = Leave::with([
                'employee.personalInfo',
                'leaveType',
                'personInCharge.personalInfo'
            ])
            ->where('status', 'Pending')
            ->latest();

        // Search by employee code or username
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('employee.personalInfo', function ($q) use ($search) {
                $q->where('full_name_en', 'like', "%{$search}%")
                  ->orWhere('full_name_kh', 'like', "%{$search}%");
            })->orWhereHas('employee', function ($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%");
                 
            });
        }

        // Filter by leave type
        if ($request->filled('leave_type')) {
            $query->where('leave_type_id', $request->input('leave_type'));
        }

        // Paginated results
        $leaves = $query->paginate(10)->withQueryString();

        // All leave types for filter dropdown
        $leaveTypes = LeaveType::orderBy('name')->get();

        // Recent pending leaves (for sidebar or dashboard widget)
        $recentLeaves = Leave::with([
                'employee.personalInfo',
                'leaveType'
            ])
            ->where('status', 'Pending')
            ->latest()
            ->take(5)
            ->get();

        // Stats
        $pendingCount = Leave::where('status', 'Pending')->count();

        $currentMonth = Carbon::now()->month;
        $currentYear  = Carbon::now()->year;

        $approvedThisMonth = Leave::where('status', 'Approved')
            ->whereMonth('approved_at', $currentMonth)
            ->whereYear('approved_at', $currentYear)
            ->count();

        $rejectedThisMonth = Leave::where('status', 'Rejected')
            ->whereMonth('updated_at', $currentMonth)
            ->whereYear('updated_at', $currentYear)
            ->count();

        return view('hr.leaves.leave_request', compact(
            'leaves',
            'leaveTypes',
            'recentLeaves',
            'pendingCount',
            'approvedThisMonth',
            'rejectedThisMonth'
        ));
    }

    /**
     * Approve a leave request (HR final approval)
     */
    public function approve(Leave $leave)
    {
        // Prevent approving already processed requests
        if ($leave->status !== 'Pending') {
            return back()->with('error', 'This leave request cannot be approved because it is no longer pending.');
        }

        DB::transaction(function () use ($leave) {
            $year = Carbon::now()->year;

            // 1. Update leave status
            $leave->update([
                'status'      => 'Approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // 2. Deduct from leave balance
            $balance = LeaveBalance::firstOrCreate(
                [
                    'employee_id'    => $leave->employee_id,
                    'leave_type_id'  => $leave->leave_type_id,
                    'year'           => $year,
                ],
                [
                    'total_days'     => 0, // will be set by your entitlement logic elsewhere
                    'used_days'      => 0,
                    'remaining_days' => 0,
                ]
            );

            // Increment used days
            $balance->increment('used_days', $leave->leave_days);

            // Update remaining days automatically
            $balance->remaining_days = $balance->total_days - $balance->used_days;
            $balance->save();
        });

        return back()->with('success', 'Leave request approved successfully and balance has been updated.');
    }

    /**
     * Reject a leave request with reason
     */
    public function reject(Request $request, Leave $leave)
    {
        // Only pending leaves can be rejected
        if ($leave->status !== 'Pending') {
            return back()->with('error', 'This leave request cannot be rejected because it is no longer pending.');
        }

        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $leave->update([
            'status'        => 'Rejected',
            'reject_reason' => $request->reject_reason,
            'rejected_by'   => Auth::id(),
            'rejected_at'   => now(),
            // Clear any previous approval data
            'approved_by'   => null,
            'approved_at'   => null,
        ]);

        return back()->with('success', 'Leave request has been rejected.');
    }
}