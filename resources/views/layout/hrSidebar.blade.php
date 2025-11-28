<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-indigo-800 to-indigo-900 text-white flex flex-col transform transition-transform duration-300 ease-in-out lg:w-60 lg:translate-x-0 lg:static lg:z-auto">

    <div class="px-5 py-5 border-b border-indigo-700 flex justify-between items-center">
        <h2 class="text-xl font-bold">HR Dashboard</h2>
        <button @click="sidebarOpen = false" class="lg:hidden p-2 hover:bg-indigo-700 rounded-lg">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1">
        <a href="{{ route('hr.dashboard') }}"
           class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base {{ request()->routeIs('hr.dashboard') ? 'bg-indigo-700 font-medium shadow' : '' }}">
            <i class="fas fa-tachometer-alt w-5"></i>
            <span>Dashboard</span>
        </a>

        <!-- កែត្រឹមនេះ! -->
        <a href="{{ route('hr.employees.index') }}"
           class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base {{ request()->routeIs('hr.employees.index') ? 'bg-indigo-700 font-medium shadow' : '' }}">
            <i class="fas fa-users w-5"></i>
            <span>Employees</span>
        </a>
        <!-- ចប់កែ -->

        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base">
            <i class="fas fa-clock w-5"></i><span>Attendance</span>
        </a>
        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base">
            <i class="fas fa-calendar-check w-5"></i><span>Leave Requests</span>
        </a>
        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base">
            <i class="fas fa-route w-5"></i><span>Missions</span>
        </a>
        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base">
            <i class="fas fa-car w-5"></i><span>Company Vehicles</span>
        </a>
        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base">
            <i class="fas fa-money-bill-wave w-5"></i><span>Payroll</span>
        </a>
    </nav>

    <div class="px-3 py-4 border-t border-indigo-700">
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition text-base">
                <i class="fas fa-sign-out-alt w-5"></i><span>Logout</span>
            </button>
        </form>
    </div>
</aside>

        <!-- Overlay ខ្មៅពេលបើក Sidebar នៅទូរស័ព្ទ -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            class="fixed inset-0 bg-black bg-opacity-70 z-40 lg:hidden"></div>

        <!-- ប៊ូតុងបើក Sidebar នៅទូរស័ព្ទ (តូច ស្អាត) -->
        <button @click="sidebarOpen = true"
            class="fixed bottom-5 left-5 z-50 bg-indigo-700 hover:bg-indigo-800 text-white p-3.5 rounded-full shadow-xl lg:hidden">
            <i class="fas fa-bars text-xl"></i>
        </button>