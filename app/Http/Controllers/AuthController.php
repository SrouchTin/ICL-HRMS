<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->filled('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'អ៊ីមែល ឬ ពាក្យសម្ងាត់មិនត្រឹមត្រូវ។',
            ]);
        }

        $user = Auth::user();

        // ONLY block if:
        // 1. User account is not active, OR
        // 2. User has employee record AND it's not active
        if (
            $user->status !== 'active' ||
            ($user->employee && $user->employee->status !== 'active')
        ) {
            Auth::logout();

            $reasons = [];
            if ($user->status !== 'active') {
                $reasons[] = "គណនី: " . ucfirst($user->status);
            }
            if ($user->employee && $user->employee->status !== 'active') {
                $reasons[] = "បុគ្គលិក: " . ucfirst($user->employee->status);
            }

            throw ValidationException::withMessages([
                'email' => 'គណនីមិនអនុញ្ញាតឱ្យចូលប្រើ។ ' . implode(' | ', $reasons),
            ]);
        }

        $request->session()->regenerate();

        return $this->redirectBasedOnRole();
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'អ្នកបានចាកចេញដោយជោគជ័យ។');
    }

    private function redirectBasedOnRole()
    {
        $user = Auth::user();
        $roleName = optional($user->role)->name ?? 'employee';

        return match ($roleName) {
            'admin'     => redirect()->route('admin.dashboard'),
            'hr'        => redirect()->route('hr.dashboard'),
            default     => redirect()->route('employee.dashboard'),
        };
    }
}
