<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

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
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'remember' => 'sometimes|boolean',
        ]);

        $input = trim($request->username);

        $user = User::whereRaw('LOWER(TRIM(username)) = ?', [strtolower($input)])
            ->with(['employee.personalInfo', 'role'])
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => 'ឈ្មោះអ្នកប្រើ ឬ លេខកូដបុគ្គលិក មិនត្រឹមត្រូវ។',
            ]);
        }

        // 1) check user active
        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'username' => 'គណនីរបស់អ្នកត្រូវបានផ្អាក។',
            ]);
        }

        // normalize role
        $roleName = strtolower(str_replace(' ', '_', trim($user->role?->name ?? '')));
        $isAdmin = in_array($roleName, ['admin', 'super_admin']);

        // 2) non-admin must have employee and that employee must be active
        if (!$isAdmin) {
            if (!$user->employee_id || !$user->employee) {
                throw ValidationException::withMessages([
                    'username' => 'គណនីរបស់អ្នកមិនទាន់បានភ្ជាប់ជាមួយបុគ្គលិក។ សូមទាក់ទងផ្នែក HR។',
                ]);
            }

            if ($user->employee->status !== 'active') {
                throw ValidationException::withMessages([
                    'username' => 'បុគ្គលិករបស់អ្នកមិនទាន់អនុញ្ញាត។',
                ]);
            }
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $this->redirectBasedOnRole();
    }



    public function logout(Request $request)
    {
        Auth::logout(); // ← គ្រប់គ្រាន់ហើយ មិនចាំបាច់ guard('web')
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'អ្នកបានចាកចេញដោយជោគជ័យ។');
    }

    private function redirectBasedOnRole()
    {
        $user = Auth::user();
        $roleName = strtolower(trim($user->role?->name ?? 'employee'));

        return match ($roleName) {
            'admin', 'super_admin', 'super admin' => redirect()->route('admin.dashboard'),
            'hr', 'human resource'                 => redirect()->route('hr.dashboard'),
            'employee', 'user'                     => redirect()->route('employee.dashboard'),
            default                                => redirect()->route('employee.dashboard'),
        };
    }
}
