<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use Illuminate\Support\Facades\DB;

class HRLeaveController extends Controller
{
    /**
     * Display the Leave Requests page for HR
     */
    public function index(Request $request)
    {
        // Base query for pending leaves
        $query = Leave::with(['employee', 'leaveType'])
            ->where('status', 'Pending')
            ->latest();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Apply leave type filter
        if ($request->filled('leave_type')) {
            $query->where('leave_type_id', $request->leave_type);
        }

        $leaves = $query->paginate(10);
        $leaveTypes = LeaveType::all();

        $recentLeaves = Leave::with(['employee', 'leaveType'])
            ->where('status', 'Pending')
            ->latest()
            ->take(5)
            ->get();

        $pendingCount = Leave::where('status', 'Pending')->count();

        $approvedThisMonth = Leave::where('status', 'Approved')
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->count();

        $rejectedThisMonth = Leave::where('status', 'Rejected')
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
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

public function approve(Leave $leave)
{
    if ($leave->status !== 'Pending') {
        return back()->with('error', 'Only pending leaves can be approved.');
    }

    DB::transaction(function () use ($leave) {
        // 1. Mark leave as approved
        $leave->update([
            'status'      => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // 2. Deduct from balance
        $year = now()->year; // 2025

        $updated = DB::table('leave_balances')
            ->where('employee_id', $leave->employee_id)
            ->where('leave_type_id', $leave->leave_type_id)
            ->where('year', $year)
            ->increment('used_days', $leave->leave_days); // e.g., +1.0 or +0.5

        if ($updated > 0) {
            // Recalculate remaining_days
            DB::table('leave_balances')
                ->where('employee_id', $leave->employee_id)
                ->where('leave_type_id', $leave->leave_type_id)
                ->where('year', $year)
                ->update(['remaining_days' => DB::raw('total_days - used_days')]);
        }
    });

    return back()->with('success', 'Leave approved and balance updated!');
}

public function reject(Request $request, Leave $leave)
{
    $request->validate([
        'reject_reason' => 'required|string|max:500',
    ]);

    $leave->update([
        'status' => 'rejected', // match your DB value
        'reject_reason' => $request->reject_reason,
        'rejected_by' => Auth::id(),    
        'rejected_at' => now(),          
        
        'approved_by' => null,
        'approved_at' => null,
    ]);

    return redirect()->back()->with('success', 'Leave request rejected successfully.');
}
}
