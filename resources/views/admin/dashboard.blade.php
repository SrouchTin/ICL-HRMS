{{-- resources/views/admin/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard • {{ Auth::user()->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-100">

<div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

    @include('layout.adminSidebar')

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col">

        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="px-6 py-5 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h1>
                    <p class="text-gray-600 mt-1">You have full control of the system</p>
                </div>
                <a href="#"
                   class="px-7 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg flex items-center gap-3 transition transform hover:scale-105">
                    <i class="fas fa-user-plus text-lg"></i>
                    Add New User
                </a>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="flex-1 p-6 lg:p-10">
            <div class="max-w-7xl mx-auto space-y-8">

                <!-- Hero Welcome Card -->
 

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 hover:shadow-xl transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-4xl font-bold text-indigo-600">248</p>
                                <p class="text-gray-600 mt-2">Total Employees</p>
                            </div>
                            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-2xl text-indigo-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 hover:shadow-xl transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-4xl font-bold text-purple-600">12</p>
                                <p class="text-gray-600 mt-2">System Users</p>
                            </div>
                            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-shield text-2xl text-purple-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 hover:shadow-xl transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-4xl font-bold text-green-600">231</p>
                                <p class="text-gray-600 mt-2">Present Today</p>
                            </div>
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-check text-2xl text-green-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 hover:shadow-xl transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-4xl font-bold text-orange-600">8</p>
                                <p class="text-gray-600 mt-2">Pending Actions</p>
                            </div>
                            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-bell text-2xl text-orange-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <a href="{{ route('admin.users.index') }}"
                           class="group bg-white rounded-2xl shadow-lg p-10 text-center border border-gray-100 hover:border-purple-300 hover:shadow-2xl transition transform hover:-translate-y-2">
                            <div class="w-20 h-20 bg-purple-100 rounded-full mx-auto mb-6 flex items-center justify-center group-hover:bg-purple-200 transition">
                                <i class="fas fa-users-cog text-4xl text-purple-600"></i>
                            </div>
                            <h4 class="text-2xl font-bold text-gray-800">Manage Users</h4>
                            <p class="text-gray-600 mt-3">Add • Edit • Delete • Assign Roles</p>
                        </a>

                        <a href="#"
                           class="group bg-white rounded-2xl shadow-lg p-10 text-center border border-gray-100 hover:border-indigo-300 hover:shadow-2xl transition transform hover:-translate-y-2">
                            <div class="w-20 h-20 bg-indigo-100 rounded-full mx-auto mb-6 flex items-center justify-center group-hover:bg-indigo-200 transition">
                                <i class="fas fa-user-tie text-4xl text-indigo-600"></i>
                            </div>
                            <h4 class="text-2xl font-bold text-gray-800">All Employees</h4>
                            <p class="text-gray-600 mt-3">View & manage employee profiles</p>
                        </a>

                        <a href="#"
                           class="group bg-white rounded-2xl shadow-lg p-10 text-center border border-gray-100 hover:border-green-300 hover:shadow-2xl transition transform hover:-translate-y-2">
                            <div class="w-20 h-20 bg-green-100 rounded-full mx-auto mb-6 flex items-center justify-center group-hover:bg-green-200 transition">
                                <i class="fas fa-shield-alt text-4xl text-green-600"></i>
                            </div>
                            <h4 class="text-2xl font-bold text-gray-800">System Backup</h4>
                            <p class="text-gray-600 mt-3">Download or restore database</p>
                        </a>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>

</body>
</html>