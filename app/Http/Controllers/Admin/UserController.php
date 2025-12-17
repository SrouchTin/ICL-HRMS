<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of users with filters and search.
     */
    public function index(Request $request)
    {
        $query = User::with(['employee.personalInfo', 'employee.branch', 'role']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('employee', function ($eq) use ($search) {
                    // search employee_code or personalInfo.full_name_en
                    $eq->where('employee_code', 'like', "%{$search}%")
                        ->orWhereHas('personalInfo', function ($pi) use ($search) {
                            $pi->where('full_name_en', 'like', "%{$search}%");
                        });
                })
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        $totalAll      = User::count();
        $totalActive   = User::where('status', 'active')->count();
        $totalInactive = User::where('status', 'inactive')->count();

        return view('admin.users.index', compact(
            'users',
            'totalAll',
            'totalActive',
            'totalInactive'
        ));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();

        $employeesWithoutAccount = Employee::whereDoesntHave('user')
            ->where('status', 'active')                 // only active employees
            ->with('personalInfo', 'branch')
            ->orderBy('employee_code')
            ->get();

        return view('admin.users.create', compact('roles', 'employeesWithoutAccount'));
    }

    /**
     * Store a newly created user in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id|unique:users,employee_id',
            'username'    => 'required|string|unique:users,username',
            'password'    => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'role_id'     => 'required|exists:roles,id',
            'status'      => 'required|in:active,inactive',
        ]);

        $employee = Employee::findOrFail($request->employee_id);

        User::create([
            'employee_id' => $employee->id,
            'username'    => strtolower($request->username),
            'role_id'     => $request->role_id,
            'branch_id'   => $employee->branch_id ?? null,
            'status'      => $request->status,
            'password'    => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        // Active employees without account
        $employeesWithoutAccount = Employee::whereDoesntHave('user')
            ->where('status', 'active')
            ->with('personalInfo', 'branch')
            ->orderBy('employee_code')
            ->get();

        // If user's employee exists and is not in $employeesWithoutAccount (because has account), include it
        $currentEmp = $user->employee ? $user->employee->load('personalInfo', 'branch') : null;

        // pass both, but in blade use readonly employee display (no update)
        return view('admin.users.edit', compact('user', 'roles', 'employeesWithoutAccount', 'currentEmp'));
    }


    /**
     * Update the specified user in the database.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'role_id'  => 'required|exists:roles,id',
            'status'   => 'required|in:active,inactive',
        ]);

        $data = [
            'role_id' => $request->role_id,
            'status'  => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }


    /**
     * Toggle user status (active ↔ inactive)
     */
    public function toggle(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot change your own status!');
        }

        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active'
        ]);

        return back()->with('success', 'User status updated successfully!');
    }

    /**
     * Permanently delete the user.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot deactivate your own account!');
        }

        $user->update(['status' => 'inactive']);

        return back()->with('success', 'User has been deactivated successfully!');
    }


    /**
     * Reset user password from modal.
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password reset successfully!');
    }
    public function toggleStatus(User $user)
{
    // Prevent self status change
    if (auth()->id() === $user->id) {
        return redirect()->back()->with('error', 'You cannot change your own status.');
    }

    // Toggle status
    $user->status = $user->status === 'active' ? 'inactive' : 'active';
    $user->save();

    return redirect()->back()->with('success', 'User status updated successfully.');
}

}
