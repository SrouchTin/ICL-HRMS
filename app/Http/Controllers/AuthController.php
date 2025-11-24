<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * បង្ហាញទំព័រ Login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.login');
    }

    /**
     * ដំណើរការ Login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Remember me — បើមាន checkbox ឬចង់ចងចាំជានិច្ច ដាក់ true
        $remember = $request->has('remember'); // ឬ $remember = true; បើចង់ចងចាំរហូត

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return $this->redirectBasedOnRole();
        }

        throw ValidationException::withMessages([
            'email' => 'អ៊ីមែល ឬ ពាក្យសម្ងាត់មិនត្រឹមត្រូវ។',
        ]);
    }

    /**
     * ចាកចេញ (Logout)
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'អ្នកបានចាកចេញដោយជោគជ័យ។');
    }

    /**
     * បញ្ជូនទៅ dashboard តាម role
     */
    private function redirectBasedOnRole()
    {
        $user = Auth::user();

        // ប្រើ relationship role() → role->name
        $roleName = optional($user->role)->name;

        return match ($roleName) {
            'admin'     => redirect()->route('admin.dashboard'),
            'hr'        => redirect()->route('hr.dashboard'),
            'employee'  => redirect()->route('employee.dashboard'),
            default     => redirect()->route('employee.dashboard'), // fallback សុវត្ថិភាព
        };
    }
}