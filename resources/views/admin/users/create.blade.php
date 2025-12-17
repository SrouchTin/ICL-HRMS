<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Login Account • Admin</title>

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
                    <h1 class="text-2xl font-bold text-gray-900">Create Login Account</h1>
                    <p class="text-gray-600 mt-1">Assign login credentials to an existing employee</p>
                </div>

                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition flex items-center gap-2">
                    <i class="fas fa-arrow-left text-sm"></i> Back to Users
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-10">
            <div class="max-w-3xl mx-auto">

                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="mx-8 mt-8 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
                            <i class="fas fa-check-circle text-xl"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Errors -->
                    @if($errors->any())
                        <div class="mx-8 mt-8 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="p-8 lg:p-10">

<form action="{{ route('admin.users.store') }}" method="POST" class="space-y-10">
    @csrf

    <!-- Employee & Username Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Select Employee -->
<div>
    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3">
        <i class="fas fa-user-tie text-indigo-600"></i>
        Select Employee <span class="text-red-500">*</span>
    </label>

    <select name="employee_id" id="employee_id" required
            class="w-full px-5 py-4 border rounded-xl 
            {{ $errors->has('employee_id') ? 'border-red-400' : 'border-gray-300' }} 
            focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition text-gray-800">

        <option value="">-- Choose employee --</option>

        @foreach($employeesWithoutAccount as $emp)
            <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                {{ $emp->employee_code }} - {{ $emp->personalInfo?->full_name_en ?? '—' }}
            </option>
        @endforeach
    </select>

    @error('employee_id')
        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
    @enderror
</div>

        <!-- Username -->
        <div>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3">
                <i class="fas fa-at text-indigo-600"></i>
                Username <span class="text-red-500">*</span>
            </label>

            <input type="text" name="username" id="username-input" required
                   class="w-full px-5 py-4 border rounded-xl 
                   {{ $errors->has('username') ? 'border-red-400' : 'border-gray-300' }} 
                   focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition"
                   placeholder="Enter username..."
                   value="{{ old('username') }}">

            @error('username')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Password & Confirm Password Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Password -->
        <div>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3">
                <i class="fas fa-lock text-red-600"></i>
                Password <span class="text-red-500">*</span>
            </label>

            <input type="password" name="password" required
                   class="w-full px-5 py-4 border rounded-xl 
                   {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }} 
                   focus:ring-4 focus:ring-red-100 focus:border-red-500 transition"
                   placeholder="••••••••">

            @error('password')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3">
                <i class="fas fa-lock text-red-600"></i>
                Confirm Password <span class="text-red-500">*</span>
            </label>

            <input type="password" name="password_confirmation" required
                   class="w-full px-5 py-4 border rounded-xl 
                   focus:ring-4 focus:ring-red-100 focus:border-red-500 transition"
                   placeholder="••••••••">
        </div>
    </div>

    <!-- Role & Status Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Role -->
        <div>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3">
                <i class="fas fa-user-shield text-purple-600"></i>
                Role <span class="text-red-500">*</span>
            </label>

            <select name="role_id" required
                    class="w-full px-5 py-4 border rounded-xl 
                           {{ $errors->has('role_id') ? 'border-red-400' : 'border-gray-300' }} 
                           focus:ring-4 focus:ring-purple-100 focus:border-purple-500 transition">
                <option value="">-- Select role --</option>

                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Status -->
        <div>
            <label class="text-sm font-semibold text-gray-700 mb-4 block">
                <i class="fas fa-power-off text-green-600"></i> Account Status
            </label>

            <div class="flex gap-10">
                <label class="flex items-center">
                    <input type="radio" name="status" value="active"
                           {{ old('status', 'active') === 'active' ? 'checked' : '' }}
                           class="w-5 h-5 text-green-600 focus:ring-green-500">
                    <span class="ml-3 font-medium text-gray-700">Active</span>
                </label>

                <label class="flex items-center">
                    <input type="radio" name="status" value="inactive"
                           {{ old('status') === 'inactive' ? 'checked' : '' }}
                           class="w-5 h-5 text-red-600 focus:ring-red-500">
                    <span class="ml-3 font-medium text-gray-700">Inactive</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="pt-8 border-t-2 border-gray-200">
        <button type="submit"
                class="w-full py-5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold text-lg rounded-xl shadow-xl transition hover:scale-[1.02] flex items-center justify-center gap-3">
            <i class="fas fa-user-plus"></i>
            Create Login Account
        </button>
    </div>

</form>


                    </div>
                </div>

            </div>
        </main>

    </div>
</div>

<script>
document.getElementById('employee_id').addEventListener('change', function () {
    const option = this.options[this.selectedIndex];
    const card = document.getElementById('preview-card');

    if (this.value) {
        card.classList.remove('hidden');

        const code   = option.dataset.code;
        const khName = option.dataset.khname;
        const enName = option.dataset.enname;
        const branch = option.dataset.branch;

        // initials
        const initials = khName.trim().split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
        document.getElementById('preview-initials').textContent = initials || '??';

        // employee info
        document.getElementById('preview-khname').textContent = khName;
        document.getElementById('preview-enname').textContent = enName;
        document.getElementById('preview-branch').textContent = branch ? 'Branch: ' + branch : 'No branch';

        // auto-fill username
        const autoUsername = code.toLowerCase();
        document.getElementById('username-field').value = autoUsername;
        document.getElementById('username-input').value = autoUsername;

    } else {
        card.classList.add('hidden');
    }
});

// trigger on reload
document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('employee_id');
    if (select.value) select.dispatchEvent(new Event('change'));
});
</script>

</body>
</html>
