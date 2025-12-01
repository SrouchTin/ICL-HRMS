{{-- resources/views/employee/myprofile.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile • {{ Auth::user()->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-gray-50">

<div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

    @include('layout.employeeSidebar')

    <div class="flex-1 flex flex-col">

        <!-- Header -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-10">
            <div class="px-6 py-5 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
                    <p class="text-sm text-gray-500 mt-1">Your personal information and employment details</p>
                </div>
                <a href="{{ route('employee.dashboard') }}"
                   class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition shadow-sm">
                    Back
                </a>
            </div>
        </header>

        <!-- Full Content -->
        <main class="flex-1 p-6">
            <div class="max-w-7xl mx-auto w-full">

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                    <!-- Profile Header -->
                    <div class="p-8 border-b border-gray-100">
                        <div class="flex flex-col md:flex-row items-center gap-8">
                            <div class="relative">
                                <img src="{{ Auth::user()->employee?->image 
                                    ? asset('storage/' . Auth::user()->employee->image) 
                                    : asset('images/avatar-default.png') }}"
                                    alt="Profile"
                                    class="w-36 h-36 rounded-full object-cover border-4 border-gray-300 shadow-xl">
                                <div class="absolute bottom-2 right-2 w-10 h-10 bg-green-500 rounded-full border-4 border-white"></div>
                            </div>

                            <div class="text-center md:text-left flex-1">
                                <h2 class="text-4xl font-bold text-gray-900">
                                    {{ Auth::user()->employee?->personalInfo?->salutation ?? '' }} {{ Auth::user()->name }}
                                </h2>
                                <p class="text-xl text-gray-600 mt-2">
                                    {{ Auth::user()->employee?->position?->position_name ?? 'Employee' }}
                                </p>
                                <div class="flex flex-wrap items-center gap-6 mt-5 text-gray-600">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-building text-indigo-600"></i>
                                        {{ Auth::user()->employee?->branch?->branch_name ?? '—' }}
                                    </span>
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-envelope text-indigo-600"></i>
                                        {{ Auth::user()->email }}
                                    </span>
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-id-badge text-indigo-600"></i>
                                        #{{ Auth::user()->employee?->employee_code ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal & Employment Info -->
                    <div class="p-8 lg:p-10">
                        @php 
                            $info = Auth::user()->employee?->personalInfo;
                            $contact = Auth::user()->employee?->contact; 

                            $phoneNumber = '—';
                            if ($contact) {
                                $phoneNumber = $contact->phone_number 
                                            ?? $contact->office_phone 
                                            ?? $contact->home_phone 
                                            ?? '—';
                            }
                            // Fallback to personalInfo phone if no contact data
                            $phoneNumber = $phoneNumber !== '—' ? $phoneNumber : ($info?->phone ?? '—');
                        @endphp

                        <div class="space-y-8">

                            <!-- Row 1 -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Salutation</label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900 font-medium">
                                        {{ $info?->salutation ?? '—' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Employee Code</label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900 font-medium">
                                        {{ Auth::user()->employee?->employee_code ?? '—' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900 font-medium">
                                        {{ Auth::user()->employee?->position?->position_name ?? '—' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2 -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900 font-medium">
                                        {{ Auth::user()->employee?->department?->department_name ?? '—' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900 font-medium">
                                        {{ $phoneNumber }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Contract Type</label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900 font-medium">
                                        {{ $info?->contract_type ?? '—' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Row 3 -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name in Khmer</label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900">
                                        {{ $info?->full_name_kh ?? '—' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name in English</label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900">
                                        {{ $info?->full_name_en ?? Auth::user()->name }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Employee Type</label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900 font-medium">
                                        {{ $info?->employee_type ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Row 4 -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900 capitalize">
                                        {{ $info?->gender ?? '—' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900">
                                        {{ $info?->dob ? \Carbon\Carbon::parse($info->dob)->format('d F Y') : '—' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nationality</label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900">
                                        {{ $info?->nationality ?? '—' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Row 5 -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Marital Status</label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900 capitalize">
                                        {{ $info?->marital_status ?? '—' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Joining Date</label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900">
                                        {{ $info?->joining_date ? \Carbon\Carbon::parse($info->joining_date)->format('d F Y') : '—' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Effective Date</label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900">
                                        {{ $info?->effective_date ? \Carbon\Carbon::parse($info->effective_date)->format('d F Y') : '—' }}
                                    </div>
                                </div>
                            </div>

                        </div>

                        @if(!$info && !$contact)
                            <div class="text-center py-20 bg-gray-50 rounded-xl mt-10">
                                <i class="fas fa-user-slash text-7xl text-gray-300 mb-6"></i>
                                <p class="text-xl font-medium text-gray-600">No personal information recorded yet</p>
                                <p class="text-gray-500 mt-2">Please contact HR to complete your profile.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

</body>
</html>