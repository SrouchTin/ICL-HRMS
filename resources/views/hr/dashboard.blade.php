<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HR Dashboard</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen">

    <!-- Sidebar -->
    <div class="w-64 bg-gradient-to-b from-indigo-800 to-indigo-900 text-white flex flex-col">
        <div class="p-6 text-center border-b border-indigo-700">
            <h2 class="text-2xl font-bold">HR Dashboard</h2>
            
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('hr.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-indigo-700 text-white font-medium">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('hr.employees.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-users"></i>
                <span>Employees</span>
            </a>
            <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-clock"></i>
                <span>Attendance</span>
            </a>
            <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-calendar-check"></i>
                <span>Leave Requests</span>
                
            </a>
            <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-route"></i>
                <span>Missions</span>
            </a>
            <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-car"></i>
                <span>Company Vehicles</span>
            </a>
            <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-money-bill-wave"></i>
                <span>Payroll</span>
            </a>
        </nav>

        <div class="p-4 border-t border-indigo-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-8 py-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">
                        Welcome back, <span class="text-indigo-600">{{ $user->name }}</span>
                    </h1>
                    <p class="text-gray-600 text-sm">
                        Branch: <strong>{{ $branch->branch_name ?? 'Not assigned' }}</strong>
                    </p>
                </div>

                <!-- Notification Bell -->
                <div class="relative">
                    <button id="notificationBtn" class="relative p-3 text-gray-600 hover:text-indigo-600 transition hover:bg-gray-100 rounded-full">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">8</span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                        <div class="px-4 py-3 border-b border-gray-200 font-semibold text-gray-700">
                            Notifications <span class="text-red-500">(8 new)</span>
                        </div>
                        <ul class="max-h-96 overflow-y-auto">
                            <li class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                <div class="flex items-start">
                                    <i class="fas fa-calendar-check text-green-500 mt-1"></i>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium">Leave Request Approved</p>
                                        <p class="text-xs text-gray-500">Ahmed Salem's annual leave was approved</p>
                                        <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                                    </div>
                                </div>
                            </li>
                            <li class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                <div class="flex items-start">
                                    <i class="fas fa-user-clock text-yellow-500 mt-1"></i>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium">Late Arrival</p>
                                        <p class="text-xs text-gray-500">Sara Ahmed arrived at 10:15 AM</p>
                                        <p class="text-xs text-gray-400 mt-1">Today, 10:30 AM</p>
                                    </div>
                                </div>
                            </li>
                            <!-- Add more notifications dynamically -->
                        </ul>
                        <a href="#" class="block text-center py-3 text-indigo-600 hover:bg-gray-50 text-sm font-medium">
                            View all notifications
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="flex-1 p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Stat Cards -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Employees</p>
                            <p class="text-3xl font-bold text-gray-800 mt-2">248</p>
                        </div>
                        <i class="fas fa-users text-4xl text-indigo-500 opacity-20"></i>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pending Leaves</p>
                            <p class="text-3xl font-bold text-yellow-600 mt-2">12</p>
                        </div>
                        <i class="fas fa-calendar-times text-4xl text-yellow-500 opacity-20"></i>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Today's Present</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">229</p>
                        </div>
                        <i class="fas fa-check-circle text-4xl text-green-500 opacity-20"></i>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Active Missions</p>
                            <p class="text-3xl font-bold text-purple-600 mt-2">7</p>
                        </div>
                        <i class="fas fa-route text-4xl text-purple-500 opacity-20"></i>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="#" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow hover:shadow-lg transition text-center">
                    <i class="fas fa-clock text-3xl mb-3"></i>
                    <p class="font-medium">Mark Attendance</p>
                </a>
                <a href="#" class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow hover:shadow-lg transition text-center">
                    <i class="fas fa-plus-circle text-3xl mb-3"></i>
                    <p class="font-medium">New Leave Request</p>
                </a>
                <a href="#" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow hover:shadow-lg transition text-center">
                    <i class="fas fa-user-plus text-3xl mb-3"></i>
                    <p class="font-medium">Add Employee</p>
                </a>
                <a href="#" class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white p-6 rounded-xl shadow hover:shadow-lg transition text-center">
                    <i class="fas fa-file-invoice-dollar text-3xl mb-3"></i>
                    <p class="font-medium">Generate Payroll</p>
                </a>
            </div>
        </main>
    </div>
</div>

<!-- Simple JS for Notification Toggle -->
<script>
    document.getElementById('notificationBtn').addEventListener('click', function() {
        document.getElementById('notificationDropdown').classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#notificationBtn') && !e.target.closest('#notificationDropdown')) {
            document.getElementById('notificationDropdown').classList.add('hidden');
        }
    });
</script>

</body>
</html>