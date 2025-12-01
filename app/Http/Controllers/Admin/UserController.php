<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Branch;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('branch');

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->status === 'active') {
            $query->where('status', 'active');
        } elseif ($request->status === 'inactive') {
            $query->where('status', 'inactive');
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

    public function create()
    {
        $roles = Role::all();
        $branches = Branch::all();

        return view('admin.users.create', compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|in:1,2,3',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:active,inactive',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'branch_id' => $request->branch_id,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    // FIXED: Added closing brace and proper spacing
    public function edit(User $user)
    {
        $roles = Role::all();
        $branches = Branch::all();

        return view('admin.users.edit', compact('user', 'roles', 'branches'));
    }


    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|in:1,2,3',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:active,inactive',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->role_id = $request->role_id;
        $user->branch_id = $request->branch_id;
        $user->status = $request->status;
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot deactivate your own account!');
        }

        $user->status = 'inactive';
        $user->save();

        return back()->with('success', 'User has been deactivated successfully');
    }
}
