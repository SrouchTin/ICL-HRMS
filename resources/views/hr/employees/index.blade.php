{{-- resources/views/hr/employees/index.blade.php --}}
@extends('layouts.hr-app')

@section('title', 'Employees')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Employees Management</h1>
    <a href="{{ route('hr.employees.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-3 rounded-lg flex items-center gap-2 shadow">
        <i class="fas fa-plus"></i> Add New Employee
    </a>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-lg shadow border">
        <p class="text-gray-600 text-sm">Total Employees</p>
        <p class="text-2xl font-bold text-indigo-600">{{ $employees->total() }}</p>
    </div>
    <div class="bg-white p-5 rounded-lg shadow border">
        <p class="text-gray-600 text-sm">Active</p>
        <p class="text-2xl font-bold text-green-600">{{ $employees->where('status', 'active')->count() }}</p>
    </div>
    <div class="bg-white p-5 rounded-lg shadow border">
        <p class="text-gray-600 text-sm">On Leave</p>
        <p class="text-2xl font-bold text-yellow-600">12</p>
    </div>
    <div class="bg-white p-5 rounded-lg shadow border">
        <p class="text-gray-600 text-sm">Inactive</p>
        <p class="text-2xl font-bold text-red-600">{{ $employees->where('status', 'inactive')->count() }}</p>
    </div>
</div>

<!-- Search & Filter -->
<div class="bg-white rounded-lg shadow p-5 mb-6">
    <form action="{{ route('hr.employees.index') }}" method="GET">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, code, email..." class="border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            
            <select name="department_id" class="border rounded-lg px-4 py-2">
                <option value="">All Departments</option>
                @foreach(\App\Models\Department::all() as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->department_name }}</option>
                @endforeach
            </select>

            <select name="branch_id" class="border rounded-lg px-4 py-2">
                <option value="">All Branches</option>
                @foreach(\App\Models\Branch::all() as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                @endforeach
            </select>

            <button type="submit" class="bg-indigo-600 text-white rounded-lg px-6 hover:bg-indigo-700 transition">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </form>
</div>

<!-- Employees Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($employees as $employee)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        @if($employee->image)
                            <img src="{{ asset('storage/' . $employee->image) }}" alt="{{ $employee->user->name }}" class="h-12 w-12 rounded-full object-cover border">
                        @else
                            <div class="h-12 w-12 rounded-full bg-gray-300 border flex items-center justify-center text-gray-600 font-bold">
                                {{ substr($employee->user->name, 0, 2) }}
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $employee->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $employee->employee_code }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $employee->department?->department_name ?? '—' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $employee->branch?->branch_name ?? '—' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $employee->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($employee->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium space-x-2">
                        <a href="{{ route('hr.employees.show', $employee) }}" class="text-indigo-600 hover:text-indigo-900">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('hr.employees.edit', $employee) }}" class="text-yellow-600 hover:text-yellow-900">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('hr.employees.destroy', $employee) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this employee?')" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">No employees found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="bg-gray-50 px-6 py-3">
        {{ $employees->appends(request()->query())->links() }}
    </div>
</div>
@endsection