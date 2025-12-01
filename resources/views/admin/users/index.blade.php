<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users • Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>[x-cloak] { display: none !important; }</style>
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
                    <p class="text-gray-500 mt-1">Add, edit, or manage user accounts</p>
                </div>
                <a href="{{ route('admin.users.create') }}"
                   class="px-7 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md flex items-center gap-2 transition">
                    Add New User
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-10">
            <div class="max-w-7xl mx-auto">

                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-2">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Clickable Stats Cards -->
                <div class="mb-8 grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <a href="{{ route('admin.users.index') }}"
                       class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 flex items-center gap-5
                              {{ !request('status') || request('status') === 'all' ? 'ring-4 ring-indigo-200' : '' }}">
                        <div class="flex-1">
                            <p class="text-5xl font-extrabold text-indigo-600 group-hover:text-indigo-700">{{ $totalAll }}</p>
                            <p class="text-gray-600 font-medium mt-1">Total System Users</p>
                        </div>
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                            <i class="fas fa-users text-2xl text-indigo-600"></i>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.index', ['status' => 'active']) }}"
                       class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 flex items-center gap-5
                              {{ request('status') === 'active' ? 'ring-4 ring-green-200' : '' }}">
                        <div class="flex-1">
                            <p class="text-5xl font-extrabold text-green-600 group-hover:text-green-700">{{ $totalActive }}</p>
                            <p class="text-gray-600 font-medium mt-1">Active Users</p>
                        </div>
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                            <i class="fas fa-user-check text-2xl text-green-600"></i>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.index', ['status' => 'inactive']) }}"
                       class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 flex items-center gap-5
                              {{ request('status') === 'inactive' ? 'ring-4 ring-red-200' : '' }}">
                        <div class="flex-1">
                            <p class="text-5xl font-extrabold text-red-600 group-hover:text-red-700">{{ $totalInactive }}</p>
                            <p class="text-gray-600 font-medium mt-1">Inactive Users</p>
                        </div>
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                            <i class="fas fa-user-slash text-2xl text-red-600"></i>
                        </div>
                    </a>
                </div>

                <!-- Search Bar -->
                <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6">
                    <div class="relative max-w-md">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search by name or email..." 
                               class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-gray-800 placeholder-gray-500">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        @if(request('search'))
                            <a href="{{ route('admin.users.index', request()->except('search')) }}" 
                               class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Users Table -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100 border-b border-gray-300">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Branch</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-sm text-gray-600">#{{ $user->id }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                                        <td class="px-6 py-4">
                                            @switch($user->role_id)
                                                @case(1) <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">Admin</span> @break
                                                @case(2) <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">HR</span> @break
                                                @default <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">Employee</span>
                                            @endswitch
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->branch?->branch_name ?? '—' }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="{{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-4 py-1.5 text-xs font-bold rounded-full">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-5 text-xl">
                                                <!-- Edit Icon -->
                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                   class="text-indigo-600 hover:text-indigo-800 transition"
                                                   title="Edit User">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- Trash Icon (Deactivate/Reactivate) -->
                                                @if(auth()->id() !== $user->id)
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                                        @csrf
                                                        @if($user->status === 'active')
                                                            @method('DELETE')
                                                            <button type="submit" onclick="return confirm('Deactivate this user?')"
                                                                    class="text-red-600 hover:text-red-800 transition" title="Deactivate">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        @else
                                                            @method('PATCH')
                                                            <button type="submit" onclick="return confirm('Reactivate this user?')"
                                                                    class="text-green-600 hover:text-green-800 transition" title="Reactivate">
                                                                <i class="fas fa-trash-restore"></i>
                                                            </button>
                                                        @endif
                                                    </form>
                                                @else
                                                    <span class="text-gray-400" title="Cannot modify yourself">
                                                        <i class="fas fa-user-lock"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-24 text-center text-gray-500">
                                            <i class="fas fa-users text-6xl mb-4 text-gray-300"></i>
                                            <p class="text-xl font-medium">No users found</p>
                                            <p class="mt-2">Try adjusting your search or filters.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-gray-600">
                            <div>
                                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} 
                                of {{ $users->total() }} user{{ $users->total() !== 1 ? 's' : '' }}
                            </div>
                            <div>
                                {{ $users->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

</body>
</html>