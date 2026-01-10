<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees | {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="{{ asset('assets/toast/css.css') }}" rel="stylesheet">
    <style>
        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }

        .table-container::-webkit-scrollbar {
            width: 6px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .status-active {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-pending {
            background-color: #dbeafe;
            color: #1e40af;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased">
    @include('toastify.toast')
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">
        {{-- Sidebar --}}
        @include('layout.hrSidebar')
        {{-- Sidebar --}}

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-8 py-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">Employees Management</h1>
                        <p class="text-sm text-gray-500 mt-1">Manage all employee records and information</p>
                    </div>
                    <!-- Notification Bell -->
                    <div class="relative">
                        <button id="notificationBtn"
                            class="relative p-3 text-gray-600 hover:text-indigo-600 transition hover:bg-gray-100 rounded-full">
                            <i class="fas fa-bell text-xl"></i>
                            <span
                                class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">8</span>
                        </button>
                        <div id="notificationDropdown"
                            class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="font-semibold text-gray-800">Notifications</h3>
                            </div>
                            <div class="max-h-60 overflow-y-auto">
                                <a href="#"
                                    class="flex items-center px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                    <div class="flex-shrink-0 bg-indigo-100 p-2 rounded-full">
                                        <i class="fas fa-user-plus text-indigo-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">New employee registered</p>
                                        <p class="text-xs text-gray-500">5 minutes ago</p>
                                    </div>
                                </a>
                                <a href="#"
                                    class="flex items-center px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                    <div class="flex-shrink-0 bg-yellow-100 p-2 rounded-full">
                                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Leave request pending</p>
                                        <p class="text-xs text-gray-500">1 hour ago</p>
                                    </div>
                                </a>
                                <a href="#" class="flex items-center px-4 py-3 hover:bg-gray-50">
                                    <div class="flex-shrink-0 bg-green-100 p-2 rounded-full">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Payroll processed</p>
                                        <p class="text-xs text-gray-500">2 hours ago</p>
                                    </div>
                                </a>
                            </div>
                            <div class="p-2 border-t border-gray-200">
                                <a href="#"
                                    class="block text-center text-sm text-indigo-600 font-medium py-2 hover:bg-gray-50 rounded">View
                                    all notifications</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 overflow-auto">
                <div class="max-w-7xl mx-auto">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-indigo-100 p-4 rounded-lg">
                                    <i class="fas fa-users text-indigo-600 text-2xl"></i>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-500">Total Employees</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-1">
                                        {{ \App\Models\Employee::count() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-100 p-4 rounded-lg">
                                    <i class="fas fa-user-check text-green-600 text-2xl"></i>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-500">Active Employees</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-1">
                                        {{ \App\Models\Employee::where('status', 'active')->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg">
                                    <i class="fas fa-user-clock text-blue-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">On Leave</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $onLeaveCount ?? '0' }}</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition">

                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-100 p-4 rounded-lg">
                                    <i class="fas fa-user-plus text-yellow-600 text-2xl"></i>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-500">New This Month</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-1">
                                        {{ \App\Models\Employee::where('status', 'active')
    ->whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employee Table Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div
                            class="p-6 border-b border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">All Employees</h2>
                                <p class="text-sm text-gray-500 mt-1">Manage and view all employee records</p>
                            </div>
                            <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 w-full md:w-auto">
                                <div class="relative">
                                    <input type="text" id="searchInput" placeholder="Search employees..."
                                        class="w-full md:w-64 border border-gray-300 rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                                <a href="{{ route('hr.employees.create') }}"
                                    class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center justify-center">
                                    <i class="fas fa-plus mr-2"></i> Add Employee
                                </a>
                            </div>
                        </div>

                        <div class="table-container">
                            <table class="w-full">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center space-x-1 cursor-pointer hover:text-gray-700"
                                                id="sortId">
                                                ID <i class="fas fa-sort text-gray-400"></i>
                                            </div>
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Employee Profile
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Employee Name
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Position
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Department
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Branch
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Supervisor
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="employeeTableBody">
                                    @foreach($employees as $employee)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $employee->id }}</div>
                                                <div class="text-xs text-gray-500">{{ $employee->employee_code }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div
                                                        class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                                        @if($employee->image)
                                                            <img class="h-10 w-10 rounded-full"
                                                                src="{{ asset('storage/' . $employee->image) }}"
                                                                alt="{{ $employee->user?->name ?? 'Employee' }}">
                                                        @else
                                                            <i class="fas fa-user text-indigo-600"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $employee->personalInfo->full_name_en ?? 'No Name' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $employee->position?->position_name ?? 'No Position' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $employee->department?->department_name ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $employee->branch?->branch_name ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    @if($employee->supervisor)
                                                       {{ $employee->supervisor->personalInfo?->full_name_en ?? 'N/A' }}
                                                    @else
                                                        <span class="text-gray-400">No Supervisor</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $status = $employee->status ?? 'active';
                                                    $statusClass = 'status-' . $status;
                                                    $statusText = ucfirst($status);
                                                @endphp
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <!-- View Button -->
                                                    <a href="{{ route('hr.employees.show', $employee) }}"
                                                        class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded transition-colors"
                                                        title="View Details">
                                                        View
                                                    </a>

                                                    <!-- Edit Button -->
                                                    <a href="{{ route('hr.employees.edit', $employee) }}"
                                                        class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 px-3 py-2 rounded transition-colors"
                                                        title="Edit">
                                                        Edit
                                                    </a>

                                                    <!-- Active/Inactive Button -->
                                                    <form action="{{ route('hr.employees.destroy', $employee) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            onclick="return confirm('Are you sure you want to change this employee status?')"
                                                            class="{{ $employee->status === 'active' ? 'text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100' : 'text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100' }} px-3 py-2 rounded transition-colors"
                                                            title="{{ $employee->status === 'active' ? 'Set Inactive' : 'Set Active' }}">
                                                            {{ $employee->status === 'active' ? 'Inactive' : 'Active' }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-gray-700">
                                    Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }}
                                    of {{ $employees->total() }} results
                                </div>

                                <div class="flex items-center space-x-2">
                                    <!-- Previous -->
                                    @if($employees->onFirstPage())
                                        <span class="px-4 py-2 text-gray-400">Previous</span>
                                    @else
                                        <a href="{{ $employees->appends(request()->query())->previousPageUrl() }}"
                                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                                            Previous
                                        </a>
                                    @endif

                                    <!-- Page Numbers -->
                                    @foreach($employees->appends(request()->query())->links()->elements as $element)
                                        @if(is_string($element))
                                            <span class="px-3 py-2 text-gray-500">{{ $element }}</span>
                                        @endif
                                        @if(is_array($element))
                                            @foreach($element as $page => $url)
                                                @if($page == $employees->currentPage())
                                                    <span
                                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium">{{ $page }}</span>
                                                @else
                                                    <a href="{{ $url }}"
                                                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                                                        {{ $page }}
                                                    </a>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach

                                    <!-- Next -->
                                    @if($employees->hasMorePages())
                                        <a href="{{ $employees->appends(request()->query())->nextPageUrl() }}"
                                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                                            Next
                                        </a>
                                    @else
                                        <span class="px-4 py-2 text-gray-400">Next</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="{{ asset('assets/toast/script.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Notification dropdown toggle
            const notificationBtn = document.getElementById('notificationBtn');
            const notificationDropdown = document.getElementById('notificationDropdown');

            notificationBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function () {
                notificationDropdown.classList.add('hidden');
            });

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            const employeeTableBody = document.getElementById('employeeTableBody');
            const rows = employeeTableBody.getElementsByTagName('tr');

            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();

                for (let i = 0; i < rows.length; i++) {
                    const row = rows[i];
                    const text = row.textContent.toLowerCase();

                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });

            // Simple sorting functionality
            const sortId = document.getElementById('sortId');
            let sortDirection = 'asc';

            sortId.addEventListener('click', function () {
                const rowsArray = Array.from(rows);

                rowsArray.sort((a, b) => {
                    const aId = parseInt(a.cells[0].textContent);
                    const bId = parseInt(b.cells[0].textContent);

                    if (sortDirection === 'asc') {
                        return aId - bId;
                    } else {
                        return bId - aId;
                    }
                });

                // Clear table
                while (employeeTableBody.firstChild) {
                    employeeTableBody.removeChild(employeeTableBody.firstChild);
                }

                // Append sorted rows
                rowsArray.forEach(row => {
                    employeeTableBody.appendChild(row);
                });

                // Toggle sort direction
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';

                // Update sort icon
                const sortIcon = sortId.querySelector('i');
                sortIcon.className = sortDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
            });
        });
    </script>
</body>

</html>