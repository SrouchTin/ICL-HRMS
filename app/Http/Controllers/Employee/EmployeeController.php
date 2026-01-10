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
    // Example: Get recent notifications (you can customize this query)
    $recentNotifications = [
        [
            'icon'    => 'fa-check-circle text-green-500',
            'title'   => 'Check-in Successful',
            'message' => 'You checked in at 08:45 AM',
            'time'    => 'Today',
        ],
        [
            'icon'    => 'fa-calendar-check text-yellow-500',
            'title'   => 'Leave Request Pending',
            'message' => 'Your annual leave is under review',
            'time'    => '2 days ago',
        ],
        // Add more as needed
    ];

    $unreadNotificationsCount = count($recentNotifications); // Or from DB count

    return view('employee.dashbaord', compact(
        'recentNotifications',
        'unreadNotificationsCount'
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