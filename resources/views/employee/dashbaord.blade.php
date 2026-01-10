<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Employee Dashboard</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

<div x-data="{ sidebarOpen: false, notificationOpen: false }" 
     class="flex h-screen"
     @click="notificationOpen = false">

    {{-- Sidebar --}}
    @include('layout.employeeSidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-8 py-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">
                        Welcome back, <span class="text-indigo-600">{{ Auth::user()->name ?? 'Employee' }}</span>!
                    </h1>
                    <p class="text-gray-600 text-sm">Have a productive day!</p>
                </div>

                <!-- Notification Bell -->
                <div class="relative">
                    <button @click.stop="notificationOpen = !notificationOpen"
                            class="relative p-3 text-gray-600 hover:text-indigo-600 transition hover:bg-gray-100 rounded-full">
                        <i class="fas fa-bell text-xl"></i>
                        @if($unreadNotificationsCount > 0)
                            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $unreadNotificationsCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="notificationOpen"
                         x-transition
                         @click.away="notificationOpen = false"
                         x-cloak
                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="px-4 py-3 border-b border-gray-200 font-semibold text-gray-700">
                            Notifications 
                            @if($unreadNotificationsCount > 0)
                                <span class="text-red-500">({{ $unreadNotificationsCount }} new)</span>
                            @endif
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            @forelse($recentNotifications as $notification)
                                <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                    <div class="flex items-start gap-3">
                                        <i class="fas {{ $notification['icon'] ?? 'fa-info-circle' }} text-indigo-500 mt-1"></i>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $notification['title'] }}</p>
                                            <p class="text-xs text-gray-600 mt-1">{{ $notification['message'] }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $notification['time'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 py-8 text-sm">No new notifications</p>
                            @endforelse
                        </div>

                        <a href="#" class="block text-center py-3 text-indigo-600 hover:bg-gray-50 text-sm font-medium">
                            View all notifications
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="flex-1 p-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Today's Status</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">Present</p>
                        </div>
                        <i class="fas fa-check-circle text-4xl text-green-500 opacity-20"></i>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pending Leaves</p>
                            <p class="text-3xl font-bold text-yellow-600 mt-2">3</p>
                        </div>
                        <i class="fas fa-calendar-times text-4xl text-yellow-500 opacity-20"></i>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Active Missions</p>
                            <p class="text-3xl font-bold text-purple-600 mt-2">1</p>
                        </div>
                        <i class="fas fa-route text-4xl text-purple-500 opacity-20"></i>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">This Month Salary</p>
                            <p class="text-3xl font-bold text-indigo-600 mt-2">$2,850</p>
                        </div>
                        <i class="fas fa-dollar-sign text-4xl text-indigo-500 opacity-20"></i>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('employee.attendance.index') }}" class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white p-6 rounded-xl shadow hover:shadow-lg transition text-center">
                    <i class="fas fa-clock text-3xl mb-3"></i>
                    <p class="font-medium">Check In / Out</p>
                </a>
                <a href="{{ route('employee.leaves.index') }}" class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow hover:shadow-lg transition text-center">
                    <i class="fas fa-plus-circle text-3xl mb-3"></i>
                    <p class="font-medium">Request Leave</p>
                </a>
                <a href="#" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow hover:shadow-lg transition text-center">
                    <i class="fas fa-route text-3xl mb-3"></i>
                    <p class="font-medium">My Missions</p>
                </a>
                <a href="#" class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white p-6 rounded-xl shadow hover:shadow-lg transition text-center">
                    <i class="fas fa-file-invoice-dollar text-3xl mb-3"></i>
                    <p class="font-medium">View Payslip</p>
                </a>
            </div>
        </main>
    </div>
</div>

<!-- Notification Toggle Script -->
<script>
    document.getElementById('notificationBtn')?.addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('notificationDropdown').classList.toggle('hidden');
    });

    document.addEventListener('click', function() {
        document.getElementById('notificationDropdown')?.classList.add('hidden');
    });
</script>

</body>
</html>