<!-- Sidebar - Same Indigo Theme as HR -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-indigo-800 to-indigo-900 text-white flex flex-col transform transition-transform duration-300 ease-in-out lg:w-60 lg:translate-x-0 lg:static lg:z-auto">

    <div class="px-5 py-5 border-b border-indigo-700 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <div
                class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center font-bold text-lg shadow-lg">
                {{ Str::substr(Auth::user()->name ?? 'E', 0, 1) }}
            </div>
            <div>
                <h2 class="text-xl font-bold">Employee</h2>
                
            </div>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden p-2 hover:bg-indigo-700 rounded-lg">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1">
        <a href="{{ route('employee.dashboard') }}"
            class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base {{ request()->routeIs('employee.dashboard') ? 'bg-indigo-700 font-medium shadow' : '' }}">
            <i class="fas fa-tachometer-alt w-5"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('employee.profile') }}"
            class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base {{ request()->routeIs('employee.profile') ? 'bg-indigo-700 font-medium shadow' : '' }}">
            <i class="fas fa-user-circle w-5"></i>
            <span>My Profile</span>
        </a>

        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base">
            <i class="fas fa-clock w-5"></i>
            <span>Attendance</span>
        </a>

        <a href="{{ route('employee.leaves.index') }}"
            class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base {{ request()->routeIs('employee.leaves.*') ? 'bg-indigo-700 font-medium shadow' : '' }}">
            <i class="fas fa-calendar-times w-5"></i>
            <span>Leave Requests</span>
        </a>

        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base">
            <i class="fas fa-route w-5"></i>
            <span>Missions</span>
        </a>

        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base">
            <i class="fas fa-file-invoice-dollar w-5"></i>
            <span>Payslips</span>
        </a>
    </nav>

    <div class="px-3 py-4 border-t border-indigo-700">
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button
                class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- Mobile Overlay -->
<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-70 z-40 lg:hidden">
</div>

<!-- Mobile Menu Button -->
<button @click="sidebarOpen = true"
    class="fixed bottom-5 left-5 z-50 bg-indigo-700 hover:bg-indigo-800 text-white p-3.5 rounded-full shadow-xl lg:hidden">
    <i class="fas fa-bars text-xl"></i>
</button>