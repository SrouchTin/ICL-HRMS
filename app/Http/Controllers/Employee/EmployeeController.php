<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user(); // Get logged-in user
        $branch = $user->branch; // Get branch via relationship

        return view('employee.dashbaord', compact('user', 'branch'));
    }
}
