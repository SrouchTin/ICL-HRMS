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

        $remember = $request->filled('remember');

        // Step 1: Try login first
        if (!Auth::attempt($request->only('email', 'password'), $remember)) {
            throw ValidationException::withMessages([
                'email' => 'អ៊ីមែល ឬ ពាក្យសម្ងាត់មិនត្រឹមត្រូវ។',
            ]);
        }

        $user = Auth::user();

        // Step 2: NOW check employee status
        if ($user->employee && $user->employee->status !== 'active') {
            Auth::logout(); // Kick them out immediately
            throw ValidationException::withMessages([
                'email' => 'គណនីនេះមិនអនុញ្ញាតឱ្យចូលប្រើប្រព័ន្ធ។ ស្ថានភាពបុគ្គលិក: ' . ucfirst($user->employee->status),
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
