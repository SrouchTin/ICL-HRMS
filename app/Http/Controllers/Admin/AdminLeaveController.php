<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Carbon\Carbon;

class AdminLeaveController extends Controller
{
    /**
     * Display pending leave requests for Admin
     */
    public function index(Request $request)
    {
        $query = Leave::with([
                'employee.personalInfo',
                'leaveType',
                'personInCharge.personalInfo'  // ← Critical: Load Person In Charge for display
            ])
            ->where('status', 'Pending')
            ->latest('created_at');

        // Search by employee code, username, or full name (EN/KH)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhereHas('personalInfo', function ($q) use ($search) {
                      $q->where('full_name_en', 'like', "%{$search}%")
                        ->orWhere('full_name_kh', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by leave type
        if ($request->filled('leave_type')) {
            $query->where('leave_type_id', $request->leave_type);
        }

        $leaves = $query->paginate(15)->withQueryString();

        $leaveTypes = LeaveType::where('status', 'active')->get();

        $pendingCount = Leave::where('status', 'Pending')->count();

        $approvedThisMonth = Leave::where('status', 'Approved')
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->count();

        $rejectedThisMonth = Leave::where('status', 'Rejected')
            ->whereMonth('rejected_at', now()->month)
            ->whereYear('rejected_at', now()->year)
            ->count();

        // Recent pending leaves for notification bell
        $recentLeaves = Leave::with([
                'employee.personalInfo',
                'leaveType',
                'personInCharge.personalInfo'
            ])
            ->where('status', 'Pending')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.leaves.leave_request', compact(
            'leaves',
            'leaveTypes',
            'pendingCount',
            'approvedThisMonth',
            'rejectedThisMonth',
            'recentLeaves'
        ));
    }

    /**
     * Approve a leave request (Admin)
     */
    public function approve(Leave $leave)
    {
        if ($leave->status !== 'Pending') {
            return back()->with('error', 'This leave request is no longer pending.');
        }

        DB::transaction(function () use ($leave) {
            $leave->update([
                'status'      => 'Approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Use the year of the leave period (more accurate than current year)
            $leaveYear = Carbon::parse($leave->from_date)->year;

            $updated = DB::table('leave_balances')
                ->where('employee_id', $leave->employee_id)
                ->where('leave_type_id', $leave->leave_type_id)
                ->where('year', $leaveYear)
                ->increment('used_days', $leave->leave_days);

            if ($updated > 0) {
                DB::table('leave_balances')
                    ->where('employee_id', $leave->employee_id)
                    ->where('leave_type_id', $leave->leave_type_id)
                    ->where('year', $leaveYear)
                    ->update(['remaining_days' => DB::raw('total_days - used_days')]);
            }
        });

        return back()->with('success', 'Leave request approved successfully!');
    }

    /**
     * Reject a leave request (Admin)
     */
    public function reject(Request $request, Leave $leave)
    {
        if ($leave->status !== 'Pending') {
            return back()->with('error', 'This leave request is no longer pending.');
        }

        $request->validate([
            'reject_reason' => 'required|string|max:1000',
        ]);

        $leave->update([
            'status'        => 'Rejected',
            'reject_reason' => $request->reject_reason,
            'rejected_by'   => Auth::id(),
            'rejected_at'   => now(),
            'approved_by'   => null,
            'approved_at'   => null,
        ]);

        return back()->with('success', 'Leave request rejected successfully.');
    }
}