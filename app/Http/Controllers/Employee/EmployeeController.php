<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Employee Dashboard
     */
    public function dashboard()
    {
        $user     = Auth::user();
        $employee = $user->employee; // assuming User hasOne Employee
        $branch   = $employee?->branch;

        // Safe fallbacks — no error even if relations don't exist yet
        $todayStatus     = "Present"; // or "Not Checked In" — you can change later
        $checkInTime     = "08:45 AM"; // static for now
        $pendingLeaves   = 3;           // static count
        $activeMission   = true;        // or false
        $missionTitle    = "Client Visit - Phnom Penh";
        $monthlySalary   = "$2,850";

        return view('employee.dashbaord', compact(
            'user',
            'employee',
            'branch',
            'todayStatus',
            'checkInTime',
            'pendingLeaves',
            'activeMission',
            'missionTitle',
            'monthlySalary'
        ));
    }

    /**
     * My Profile Page
     */
    public function myProfile()
    {
        $user     = Auth::user();
        $employee = $user->employee; // User → Employee relationship

        return view('employee.profile', compact('user', 'employee'));
    }
}