<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginPage()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
$request->validate([
    'email' => 'required|email',
    'password' => 'required'
]);

$credentials = $request->only('email', 'password');

if (Auth::attempt($credentials)) {
    $user = Auth::user();

    // Redirect by Role
    if ($user->role->name === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user->role->name === 'hr') {
        return redirect()->route('hr.dashboard');
    }

    return redirect()->route('employee.dashboard');
}

return back()->withErrors(['error' => 'Invalid email or password']);

    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login.page');
    }
}

