<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users • Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-50">

    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">
        @include('layout.adminSidebar')

        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
                <div class="px-6 py-5 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Manage Users</h1>
                        <p class="text-gray-500 mt-1">View and manage all system login accounts</p>
                    </div>
                    <a href="{{ route('admin.users.create') }}"
                        class="px-7 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg flex items-center gap-3 transition transform hover:scale-105">
                        <i class="fas fa-user-plus"></i> Add New User
                    </a>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-6 lg:p-10">
                <div class="max-w-7xl mx-auto">

                    <!-- Success Message -->
                    @if(session('success'))
                        <div
                            class="mb-6 p-5 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3 shadow-sm">
                            <i class="fas fa-check-circle text-2xl"></i>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Stats Cards -->
                    <div class="mb-8 grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <a href="{{ route('admin.users.index') }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 flex items-center gap-5
                              {{ !request('status') ? 'ring-4 ring-indigo-300' : '' }}">
                            <div class="flex-1">
                                <p class="text-5xl font-extrabold text-indigo-600 group-hover:text-indigo-700">
                                    {{ $totalAll }}
                                </p>
                                <p class="text-gray-600 font-medium mt-1">Total Users</p>
                            </div>
                            <div
                                class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                                <i class="fas fa-users text-2xl text-indigo-600"></i>
                            </div>
                        </a>

                        <a href="{{ route('admin.users.index', ['status' => 'active']) }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 flex items-center gap-5
                              {{ request('status') === 'active' ? 'ring-4 ring-green-300' : '' }}">
                            <div class="flex-1">
                                <p class="text-5xl font-extrabold text-green-600 group-hover:text-green-700">
                                    {{ $totalActive }}
                                </p>
                                <p class="text-gray-600 font-medium mt-1">Active Users</p>
                            </div>
                            <div
                                class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                                <i class="fas fa-user-check text-2xl text-green-600"></i>
                            </div>
                        </a>

                        <a href="{{ route('admin.users.index', ['status' => 'inactive']) }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 flex items-center gap-5
                              {{ request('status') === 'inactive' ? 'ring-4 ring-red-300' : '' }}">
                            <div class="flex-1">
                                <p class="text-5xl font-extrabold text-red-600 group-hover:text-red-700">
                                    {{ $totalInactive }}
                                </p>
                                <p class="text-gray-600 font-medium mt-1">Inactive Users</p>
                            </div>
                            <div
                                class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                                <i class="fas fa-user-slash text-2xl text-red-600"></i>
                            </div>
                        </a>
                    </div>

                    <!-- Search + Filter -->
                    <div class="mb-6 flex flex-col sm:flex-row gap-4">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="flex-1">
                            <div class="relative max-w-md">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Search by name, code, or username..."
                                    class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition text-gray-800">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                @if(request('search'))
                                    <a href="{{ route('admin.users.index', request()->except('search')) }}"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Users Table -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[700px]">
                                <thead class="bg-gray-800 text-white">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-semibold uppercase">#</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold uppercase">Employee</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold uppercase">Username</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold uppercase">Role</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold uppercase">Branch</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold uppercase">Created</th>
                                        <th class="px-6 py-3 text-center text-sm font-semibold uppercase">Status</th>
                                        <th class="px-6 py-3 text-center text-sm font-semibold uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($users as $user)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $loop->iteration }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $user->personalInfo->full_name_en ?? '—' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 font-mono">
                                                {{ $user->username }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                                            @if($user->role?->name === 'super_admin') bg-red-100 text-red-800
                                                            @elseif($user->role?->name === 'admin') bg-purple-100 text-purple-800
                                                            @elseif($user->role?->name === 'hr') bg-blue-100 text-blue-800
                                                            @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $user->role?->name ?? '—')) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700">
                                                {{ $user->employee?->branch->branch_code ?? '—' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $user->created_at }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                                            {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst($user->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center flex justify-center gap-3">
                                                <!-- Edit -->
                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                    class="text-indigo-600 hover:text-indigo-800 px-3 py-1 border border-indigo-600 rounded hover:bg-indigo-50 transition-colors"
                                                    title="Edit">
                                                    Edit
                                                </a>

                                                <!-- Active/Inactive (cannot change self) -->
                                                @if(auth()->id() !== $user->id)
                                                    <form action="{{ route('admin.users.toggleStatus', $user) }}" method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return confirm('Are you sure you want to change this user status?')"
                                                            class="{{ $user->status === 'active' ? 'text-red-600 hover:text-red-800 border border-red-600 hover:bg-red-50' : 'text-green-600 hover:text-green-800 border border-green-600 hover:bg-green-50' }} px-3 py-1 rounded transition-colors"
                                                            title="{{ $user->status === 'active' ? 'Set Inactive' : 'Set Active' }}">
                                                            {{ $user->status === 'active' ? 'Inactive' : 'Active' }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-20 text-center text-gray-400">
                                                <i class="fas fa-users text-5xl mb-3"></i>
                                                <p class="text-lg font-medium">No users found</p>
                                                <a href="{{ route('admin.users.create') }}"
                                                    class="text-indigo-600 underline mt-2 inline-block">
                                                    Create the first user
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            {{ $users->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="resetModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
            <h3 class="text-2xl font-bold mb-4">Reset Password</h3>
            <form action="" id="resetForm" method="POST">
                @csrf @method('PATCH')
                <p class="text-gray-600 mb-4">Enter new password for: <strong id="modal-username"></strong></p>
                <input type="password" name="password" required
                    class="w-full px-5 py-3 border border-gray-300 rounded-xl mb-4" placeholder="New password">
                <input type="password" name="password_confirmation" required
                    class="w-full px-5 py-3 border border-gray-300 rounded-xl" placeholder="Confirm password">
                <div class="flex gap-3 mt-6">
                    <button type="submit"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Update</button>
                    <button type="button" onclick="closeResetModal()"
                        class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openResetModal(userId, username) {
            document.getElementById('resetForm').action = `/admin/users/${userId}/reset-password`;
            document.getElementById('modal-username').textContent = username;
            document.getElementById('resetModal').classList.remove('hidden');
        }
        function closeResetModal() {
            document.getElementById('resetModal').classList.add('hidden');
        }
        document.getElementById('resetModal').addEventListener('click', function (e) {
            if (e.target === this) closeResetModal();
        });
    </script>

</body>

</html>