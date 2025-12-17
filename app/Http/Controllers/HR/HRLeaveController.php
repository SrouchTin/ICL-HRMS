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

        // Get all leave types for filter dropdown
        $leaveTypes = LeaveType::all();

        // Get recent leaves for notifications
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

    public function approve($id)
    {
        $leave = Leave::findOrFail($id);

        // Approve the leave
        $leave->update([
            'status' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Update leave balance
        $year = \Carbon\Carbon::parse($leave->from_date)->year;

        $balance = LeaveBalance::firstOrCreate(
            [
                'employee_id' => $leave->employee_id,
                'leave_type_id' => $leave->leave_type_id,
                'year' => $year,
            ],
            [
                'total_days' => 0,
                'used_days' => 0,
                'remaining_days' => 0,
            ]
        );

        // Increase used_days, decrease remaining_days
        $balance->increment('used_days', $leave->leave_days);
        $balance->decrement('remaining_days', $leave->leave_days);

        return redirect()->back()->with('success', 'Leave approved and balance updated.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'nullable|string|max:500',
        ]);

        $leave = Leave::findOrFail($id);

        $leave->update([
            'status' => 'Rejected',
            'reject_reason' => $request->reject_reason,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Leave request rejected.');
    }
}
