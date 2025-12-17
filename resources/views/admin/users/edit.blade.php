<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit User • {{ $user->username }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-50">

    <div x-data="{ sidebarOpen:false }" class="flex min-h-screen">
        @include('layout.adminSidebar')

        <div class="flex-1 flex flex-col">
            <!-- HEADER -->
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
                <div class="px-6 py-5 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit User Account</h1>
                        <p class="text-gray-600 mt-1">Update role, password, and account status</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}"
                        class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl transition flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </header>

            <!-- MAIN CONTENT -->
            <main class="flex-1 p-6 lg:p-10">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-xl shadow-lg p-8">

                        <!-- Success / Error Messages -->
                        @if(session('success'))
                            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-md">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-md">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- FORM -->
                        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <!-- Employee + Username -->
                            <!-- Employee + Username -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Employee Name (readonly) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Employee Name</label>
                                    <input type="text"
                                        value="{{ $user->personalInfo?->full_name_en ?? $user->employee?->full_name_kh ?? '—' }}"
                                        readonly
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-800 cursor-not-allowed">
                                </div>

                                <!-- Username (editable) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Username</label>
                                    <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-800 focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500"
                                        placeholder="Enter username">
                                    @error('username')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>



                            <!-- Password + Confirm Password -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">New Password</label>
                                    <input type="password" name="password" placeholder="Leave blank to keep current"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-100 focus:border-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                    <input type="password" name="password_confirmation" placeholder="Retype password"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-100 focus:border-orange-500">
                                </div>
                            </div>

                            <!-- Role + Status -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Role -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Role</label>
                                    <select name="role_id" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-100 focus:border-purple-500">
                                        <option value="">-- Select Role --</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Account Status</label>
                                    <div class="flex gap-6 mt-1">
                                        <label class="flex items-center gap-2">
                                            <input type="radio" name="status" value="active" {{ old('status', $user->status) === 'active' ? 'checked' : '' }}
                                                class="h-4 w-4 text-green-600">
                                            Active
                                        </label>
                                        <label class="flex items-center gap-2">
                                            <input type="radio" name="status" value="inactive" {{ old('status', $user->status) === 'inactive' ? 'checked' : '' }}
                                                class="h-4 w-4 text-red-600">
                                            Inactive
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                                <a href="{{ route('admin.users.index') }}"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md">Cancel</a>
                                <button type="submit"
                                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md flex items-center gap-2">
                                    <i class="fas fa-save"></i> Update
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