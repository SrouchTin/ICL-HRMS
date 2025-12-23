<!-- ADMIN SIDEBAR – FINAL VERSION (Admin can only VIEW employees, not edit/delete) -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-indigo-800 to-indigo-900 text-white flex flex-col transform transition-transform duration-300 ease-in-out lg:w-60 lg:translate-x-0 lg:static lg:z-auto">

    <div class="px-5 py-5 border-b border-indigo-700 flex justify-between items-center">
        <h2 class="text-xl font-bold">Admin Dashboard</h2>
        <button @click="sidebarOpen = false" class="lg:hidden p-2 hover:bg-indigo-700 rounded-lg">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-700 font-medium shadow-lg' : '' }}">
            <i class="fas fa-tachometer-alt w-5"></i>
            <span>Dashboard</span>
        </a>

        <!-- MANAGE USERS (Full Control: Add/Edit/Delete) -->
        <a href="{{ route('admin.users.index') }}"
           class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base {{ request()->routeIs('admin.users.*') ? 'bg-indigo-700 font-medium shadow-lg' : '' }}">
            <i class="fas fa-users-cog w-5"></i>
            <span>Manage Users</span>
        </a>

        <!-- VIEW EMPLOYEES (Read-Only – No Edit/Delete) -->
        <a href="#"
           class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base {{ request()->routeIs('admin.employees.*') ? 'bg-indigo-700 font-medium shadow-lg' : '' }}">
            <i class="fas fa-users w-5"></i>
            <span>View Employees</span>
        </a>

        <!-- Optional: Other Menus (you can add later) -->
        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base opacity-70">
            <i class="fas fa-clock w-5"></i><span>Attendance</span>
        </a>
        <a href="{{ route('admin.leave.requests') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base opacity-70">
            <i class="fas fa-calendar-check w-5"></i><span>Leave Requests</span>
        </a>
        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base opacity-70">
            <i class="fas fa-route w-5"></i><span>Missions</span>
        </a>
        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base opacity-70">
            <i class="fas fa-car w-5"></i><span>Company Vehicles</span>
        </a>
        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base opacity-70">
            <i class="fas fa-money-bill-wave w-5"></i><span>Payroll</span>
        </a>
    </nav>

    <!-- Logout -->
    <div class="px-3 py-4 border-t border-indigo-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- Mobile Overlay & Button -->
<div x-show="sidebarOpen" @click="sidebarOpen = false"
     class="fixed inset-0 bg-black bg-opacity-70 z-40 lg:hidden"></div>

<button @click="sidebarOpen = true"
        class="fixed bottom-5 left-5 z-50 bg-indigo-700 hover:bg-indigo-800 text-white p-3.5 rounded-full shadow-xl lg:hidden transition transform hover:scale-110">
    <i class="fas fa-bars text-xl"></i>
</button>