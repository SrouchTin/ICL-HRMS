<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PersonalInfo;
use App\Models\Contact;
use App\Models\Address;
use App\Models\EmergencyContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HREmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['user', 'department', 'branch', 'personalInfo']);

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"))
                  ->orWhere('employee_code', 'like', "%{$request->search}%");
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $employees = $query->latest()->paginate(15);

        return view('hr.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('hr.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_code' => 'required|unique:employees',
            'department_id' => 'required|exists:departments,id',
            'branch_id'     => 'required|exists:branches,id',
            'image'       => 'nullable|image|max:2048',
            // Add more validation as needed
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('employees', 'public');
        }

        $employee = Employee::create($data);

        // Handle related models
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

        return redirect()->route('hr.employees.index')->with('success', 'Employee created successfully!');
    }

    public function show(Employee $employee)
    {
        $employee->load([
            'personalInfo', 'contact', 'address', 'emergencyContact',
            'familyMembers', 'educationHistories', 'attachments'
        ]);

        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $employee->load(['personalInfo', 'contact', 'address', 'emergencyContact']);
        return view('hr.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'employee_code' => 'required|unique:employees,employee_code,' . $employee->id,
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($employee->image) Storage::disk('public')->delete($employee->image);
            $data['image'] = $request->file('image')->store('employees', 'public');
        }

        $employee->update($data);

        // Update or create related records
        if ($request->filled('personal')) {
            $employee->personalInfo()->updateOrCreate([], $request->personal);
        }
        if ($request->filled('contact')) {
            $employee->contact()->updateOrCreate([], $request->contact);
        }
        if ($request->filled('address')) {
            $employee->address()->updateOrCreate([], $request->address);
        }
        if ($request->filled('emergency')) {
            $employee->emergencyContact()->updateOrCreate([], $request->emergency);
        }

        return redirect()->route('hr.employees.index')->with('success', 'Employee updated successfully!');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->image) {
            Storage::disk('public')->delete($employee->image);
        }
        $employee->delete();

        return back()->with('success', 'Employee deleted successfully!');
    }
}