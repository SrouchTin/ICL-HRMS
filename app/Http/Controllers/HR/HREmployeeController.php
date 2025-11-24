<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PersonalInfo;
use App\Models\Contact;
use App\Models\Address;
use App\Models\EmergencyContact;
use App\Models\User;
use App\Models\Department;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class HREmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['user', 'department', 'branch', 'personalInfo','position']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhere('employee_code', 'like', "%{$search}%");
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('branch_id')) {
        $query->where('branch_id', $request->branch_id);
        }

        $employees = $query->latest()->paginate(15);

        $departments = Department::where('status', 'active')->get();
        $branches = Branch::where('status', 'active')->get();

        return view('hr.employees.index', compact('employees', 'departments', 'branches'));
    }

    public function create()
    {
        $departments = Department::where('status', 'active')->get();
        $branches = Branch::where('status', 'active')->get();

        return view('hr.employees.create', compact('departments', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_code' => 'required|unique:employees,employee_code',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:8|confirmed',
            'department_id' => 'required|exists:departments,id',
            'branch_id'     => 'required|exists:branches,id',
            'start_date'    => 'required|date',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Create User First
        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'branch_id'  => $request->branch_id,
            
        ]);

        // Handle Image Upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('employees', 'public');
        }

        // Create Employee
        $employee = Employee::create([
            'user_id'        => $user->id,
            'employee_code'  => $request->employee_code,
            'department_id'  => $request->department_id,
            'branch_id'      => $request->branch_id,
            'start_date'     => $request->start_date,
            'image'          => $imagePath,
            'status'         => 'active',
        ]);

        // Create Related Records (One-to-One)
        if ($request->filled('personal')) {
            $employee->personalInfo()->create($request->personal);
        }

        if ($request->filled('contact')) {
            $employee->contact()->create($request->contact);
        }

        if ($request->filled('address')) {
            $employee->address()->create($request->address);
        }

        if ($request->filled('emergency')) {
            $employee->emergencyContact()->create($request->emergency);
        }

        return redirect()->route('hr.employees.index')
            ->with('success', 'Employee created successfully!');
    }

    public function show(Employee $employee)
    {
        $employee->load([
            'user',
            'department',
            'branch',
            'personalInfo',
            'contacts',
            'addresses',
            'emergencyContacts',
            'familyMembers',
            'educationHistories',
            'trainingHistories',
            'employmentHistories',
            'achievements',
            'attachments'
        ]);

        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $employee->load(['personalInfo', 'contact', 'address', 'emergencyContact']);

        $departments = Department::where('status', 'active')->get();
        $branches = Branch::where('status', 'active')->get();

        return view('hr.employees.edit', compact('employee', 'departments', 'branches'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'employee_code' => 'required|unique:employees,employee_code,' . $employee->id,
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $employee->user_id,
            'password'      => 'nullable|min:8|confirmed',
            'department_id' => 'required|exists:departments,id',
            'branch_id'     => 'required|exists:branches,id',
            'start_date'    => 'required|date',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Update User
        $employee->user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'branch_id' => $request->branch_id,
        ]);

        if ($request->filled('password')) {
            $employee->user->update(['password' => Hash::make($request->password)]);
        }

        // Handle Image
        if ($request->hasFile('image')) {
            if ($employee->image) {
                Storage::disk('public')->delete($employee->image);
            }
            $imagePath = $request->file('image')->store('employees', 'public');
        } else {
            $imagePath = $employee->image;
        }

        // Update Employee
        $employee->update([
            'employee_code' => $request->employee_code,
            'department_id' => $request->department_id,
            'branch_id'     => $request->branch_id,
            'start_date'    => $request->start_date,
            'image'         => $imagePath,
        ]);

        // Update or Create Related Records
        $employee->personalInfo()->updateOrCreate([], $request->input('personal', []));
        $employee->contact()->updateOrCreate([], $request->input('contact', []));
        $employee->address()->updateOrCreate([], $request->input('address', []));
        $employee->emergencyContact()->updateOrCreate([], $request->input('emergency', []));

        return redirect()->route('hr.employees.index')
            ->with('success', 'Employee updated successfully!');
    }

    public function destroy(Employee $employee)
    {
        // Delete image
        if ($employee->image) {
            Storage::disk('public')->delete($employee->image);
        }

        // Delete related attachments if any
        foreach ($employee->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }

        // Delete user (optional - be careful!)
        $employee->user?->delete();

        $employee->delete();

        return back()->with('success', 'Employee deleted successfully!');
    }
}