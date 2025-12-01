<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User • {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-100">

<div x-data="{ sidebarOpen: false }" class="flex min-h-screen">
    @include('layout.adminSidebar')

    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="px-6 py-5 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
                    <p class="text-gray-600 mt-1">Update account details, role, and status</p>
                </div>
                <a href="{{ route('admin.users.index') }}"
                   class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-10">
            <div class="max-w-3xl mx-auto">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8 lg:p-12">

                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Name & Email -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user text-indigo-600"></i> Full Name
                                </label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('name') border-red-500 @enderror"
                                       placeholder="Enter full name" required>
                                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-envelope text-indigo-600"></i> Email Address
                                </label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('email') border-red-500 @enderror"
                                       placeholder="user@company.com" required>
                                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Password (Optional) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-lock text-orange-600"></i> New Password <span class="text-gray-500 font-normal">(leave blank to keep current)</span>
                                </label>
                                <input type="password" name="password"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition @error('password') border-red-500 @enderror"
                                       placeholder="Minimum 8 characters">
                                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-lock text-orange-600"></i> Confirm New Password
                                </label>
                                <input type="password" name="password_confirmation"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                                       placeholder="Retype new password">
                            </div>
                        </div>

                        <!-- Role & Branch -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user-shield text-purple-600"></i> Role
                                </label>
                                <select name="role_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition @error('role_id') border-red-500 @enderror" required>
                                    <option value="">-- Select Role --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-building text-teal-600"></i> Branch
                                </label>
                                <select name="branch_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                                    <option value="">-- No Branch (Optional) --</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->branch_code }} - {{ $branch->branch_name }} ({{ $branch->location }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-4">
                                <i class="fas fa-toggle-on text-green-600"></i> Account Status
                            </label>
                            <div class="flex items-center gap-10 bg-gray-50 p-6 rounded-xl">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status" value="active" {{ old('status', $user->status) === 'active' ? 'checked' : '' }} class="mr-3 h-6 w-6 text-green-600 focus:ring-green-500">
                                    <span class="text-lg font-medium text-gray-800">Active</span>
                                    
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status" value="inactive" {{ old('status', $user->status) === 'inactive' ? 'checked' : '' }} class="mr-3 h-6 w-6 text-red-600 focus:ring-red-500">
                                    <span class="text-lg font-medium text-gray-800">Inactive</span>
                                    
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-8 border-t-2 border-gray-200 flex justify-end gap-4">
                            <a href="{{ route('admin.users.index') }}" class="px-8 py-4 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold rounded-xl transition">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="px-12 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-lg rounded-xl shadow-2xl transition transform hover:scale-105 flex items-center gap-3">
                                <i class="fas fa-save text-xl"></i>
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

</body>
</html>