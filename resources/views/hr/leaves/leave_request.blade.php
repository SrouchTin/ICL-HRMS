<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Leave Requests - HR Dashboard</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

<div x-data="{ sidebarOpen: false }" class="flex h-screen">

    {{-- Sidebar --}}
    @include('layout.hrSidebar')
    {{-- End Sidebar --}}

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-8 py-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Leave Requests</h1>
                    <p class="text-gray-600 text-sm">Review and manage employee leave applications</p>
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
                                    <i class="fas fa-calendar-plus text-blue-500 mt-1"></i>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium">New Leave Request</p>
                                        <p class="text-xs text-gray-500">Sara Ahmed submitted a sick leave request</p>
                                        <p class="text-xs text-gray-400 mt-1">1 hour ago</p>
                                    </div>
                                </div>
                            </li>
                            <!-- More notifications can be added dynamically -->
                        </ul>
                        <a href="#" class="block text-center py-3 text-indigo-600 hover:bg-gray-50 text-sm font-medium">
                            View all notifications
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-y-auto">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pending Requests</p>
                            <p class="text-3xl font-bold text-yellow-600 mt-2">12</p>
                        </div>
                        <i class="fas fa-clock text-4xl text-yellow-500 opacity-20"></i>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Approved This Month</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">28</p>
                        </div>
                        <i class="fas fa-check-circle text-4xl text-green-500 opacity-20"></i>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Rejected This Month</p>
                            <p class="text-3xl font-bold text-red-600 mt-2">5</p>
                        </div>
                        <i class="fas fa-times-circle text-4xl text-red-500 opacity-20"></i>
                    </div>
                </div>
            </div>

            <!-- Leave Requests Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h2 class="text-lg font-semibold text-gray-800">Pending Leave Requests</h2>
                    <div class="flex gap-3 w-full sm:w-auto">
                        <input type="text" placeholder="Search employee..." class="flex-1 sm:flex-initial px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option>All Types</option>
                            <option>Annual Leave</option>
                            <option>Sick Leave</option>
                            <option>Emergency Leave</option>
                            <option>Maternity Leave</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-gray-200 border-2 border-dashed rounded-full w-10 h-10"></div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">Sara Ahmed</div>
                                            <div class="text-sm text-gray-500">EMP-045</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Annual Leave</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">Dec 20 - Dec 25, 2025</td>
                                <td class="px-6 py-4 text-sm text-gray-900">5 days</td>
                                <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-xs">Family vacation in Dubai</td>
                                <td class="px-6 py-4 text-sm">
                                    <button class="text-green-600 hover:text-green-800 font-medium mr-4">Approve</button>
                                    <button class="text-red-600 hover:text-red-800 font-medium">Reject</button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-gray-200 border-2 border-dashed rounded-full w-10 h-10"></div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">Mohamed Ali</div>
                                            <div class="text-sm text-gray-500">EMP-112</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Sick Leave</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">Dec 18 – Dec 19, 2025</td>
                                <td class="px-6 py-4 text-sm text-gray-900">2 days</td>
                                <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-xs">Medical treatment and recovery</td>
                                <td class="px-6 py-4 text-sm">
                                    <button class="text-green-600 hover:text-green-800 font-medium mr-4">Approve</button>
                                    <button class="text-red-600 hover:text-red-800 font-medium">Reject</button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-gray-200 border-2 border-dashed rounded-full w-10 h-10"></div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">Fatima Khalid</div>
                                            <div class="text-sm text-gray-500">EMP-078</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">Emergency Leave</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">Dec 17, 2025</td>
                                <td class="px-6 py-4 text-sm text-gray-900">1 day</td>
                                <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-xs">Family emergency</td>
                                <td class="px-6 py-4 text-sm">
                                    <button class="text-green-600 hover:text-green-800 font-medium mr-4">Approve</button>
                                    <button class="text-red-600 hover:text-red-800 font-medium">Reject</button>
                                </td>
                            </tr>
                            <!-- Add more rows from database in real implementation -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (optional) -->
                <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                    <p class="text-sm text-gray-600">Showing 1 to 10 of 12 results</p>
                    <div class="flex gap-2">
                        <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">Previous</button>
                        <button class="px-3 py-1 bg-indigo-600 text-white rounded">1</button>
                        <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">2</button>
                        <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">Next</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Notification Dropdown Toggle Script -->
<script>
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');

    notificationBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationDropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', function(e) {
        if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.add('hidden');
        }
    });
</script>

</body>
</html>