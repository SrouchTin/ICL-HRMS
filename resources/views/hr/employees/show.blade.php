{{-- resources/views/hr/employees/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $employee->user->name ?? 'Employee' }} - Profile | {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .rotate-180 {
            transform: rotate(180deg);
        }
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased">

    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
            @include('layout.hrSidebar')
        {{-- Sidebar --}}
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200 px-8 py-5 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Employee Profile</h1>
                <a href="{{ route('hr.employees.index') }}"
                    class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </header>

            <main class="flex-1 overflow-y-auto p-8">
                <div class="max-w-7xl mx-auto space-y-6">

                    <!-- Profile Header -->
<!-- PERFECT PROFILE HEADER FOR HR - NO CUT HEAD, ALWAYS BEAUTIFUL -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 md:p-10">
    <div class="flex flex-col md:flex-row items-center gap-10">

        <!-- BIG & SMART AVATAR - NEVER CUTS HEAD -->
        <div class="shrink-0">
            <div class="w-48 h-48 md:w-56 md:h-56 lg:w-64 lg:h-64 rounded-full overflow-hidden border-8 border-white shadow-2xl bg-gray-200 ring-4 ring-indigo-100">
                <img src="{{ $employee->image 
                    ? asset('storage/' . $employee->image) 
                    : asset('images/default-avatar.png') }}"
                    alt="Profile Photo"
                    class="w-full h-full object-cover object-top"> <!-- KEY: object-top = head never cut -->
            </div>
        </div>

        <!-- Info Section -->
        <div class="text-center md:text-left flex-1">
            <h2 class="text-4xl font-extrabold text-gray-900">
                {{ $employee->user->name 
                    ?? $employee->personalInfo?->full_name_en 
                    ?? $employee->personalInfo?->full_name_kh 
                    ?? 'Employee Name Not Set' }}
            </h2>

            <p class="text-2xl text-indigo-600 font-medium mt-2">
                {{ $employee->position?->position_name ?? 'No Position Assigned' }}
            </p>

            <div class="flex flex-wrap gap-8 mt-6 text-gray-600 text-lg">
                <span class="flex items-center gap-2">
                    <i class="fas fa-building text-indigo-600"></i>
                    {{ $employee->branch?->branch_name ?? '—' }}
                </span>
                <span class="flex items-center gap-2">
                    <i class="fas fa-envelope text-indigo-600"></i>
                    {{ $employee->contact?->email ?? 'No Email' }}
                </span>
                <span class="flex items-center gap-2">
                    <i class="fas fa-id-badge text-indigo-600"></i>
                    #{{ str_pad($employee->id, 6, '0', STR_PAD_LEFT) }}
                </span>
            </div>
        </div>
    </div>
</div>

                    <!-- ALL 11 ACCORDIONS - NOW ALL IN CLEAN TABLES -->
                    <div class="space-y-5">

                        @php
                            $tableHead = "bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider";
                            $tableCell = "px-6 py-4 whitespace-nowrap text-sm text-gray-900";
                        @endphp

                        <!-- 1. Personal Information -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('personal')">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fas fa-user mr-3 text-indigo-600"></i>PERSONAL INFORMATION
                                </h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-personal"></i>
                            </div>

                            <div class="p-8 hidden" id="content-personal">

                                @php $info = $employee->personalInfo; @endphp

                                @if($info)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                    <!-- Employee Code -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Employee Code</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $employee->employee_code ?? '-' }}</div>
                                    </div>

                                    <!-- Salutation -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Salutation</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $info->salutation ?? '-' }}</div>
                                    </div>

                                    <!-- Full Name KH -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Name (KH)</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $info->full_name_kh ?? '-' }}</div>
                                    </div>

                                    <!-- Full Name EN -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Name (EN)</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $info->full_name_en ?? '-' }}</div>
                                    </div>

                                    <!-- Gender -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Gender</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ ucfirst($info->gender) ?? '-' }}</div>
                                    </div>

                                    <!-- DOB -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Date of Birth</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $info->dob ?? '-' }}</div>
                                    </div>

                                    <!-- Nationality -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Nationality</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $info->nationality ?? '-' }}</div>
                                    </div>

                                    <!-- Marital Status -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Marital Status</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">
                                            {{ ucfirst($info->marital_status) ?? '-' }}
                                        </div>
                                    </div>

                                    <!-- Religion -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Religion</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $info->religion ?? '-' }}</div>
                                    </div>

                                    <!-- Blood Group -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Blood Group</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $info->blood_group ?? '-' }}</div>
                                    </div>
                                    <!-- Bank Account -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Bank Account Name</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $info->bank_account_name ?? '-' }}</div>
                                    </div>
                                    <!-- Bank Account -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Bank Account Number</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $info->bank_account_number ?? '-' }}</div>
                                    </div>

                                    <!-- Joining Date -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Join Date</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $info->joining_date ?? '-' }}</div>
                                    </div>

                                    <!-- Effective Date -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Effective Date</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $info->effective_date ?? '-' }}</div>
                                    </div>

                                    <!-- End Date -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">End Date</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $info->end_date ?? '-' }}</div>
                                    </div>

                                    <!-- Contract Type -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Contract Type</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $info->contract_type ?? '-' }}</div>
                                    </div>

                                    <!-- Employee Type -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Employee Type</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $info->employee_type ?? '-' }}</div>
                                    </div>

                                </div>
                                @else
                                    <p class="text-gray-500 italic">No personal information recorded yet.</p>
                                @endif

                            </div>
                        </div>

                        <!-- 3. Addresses -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('address')">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fas fa-home mr-3 text-indigo-600"></i>PERMANENT ADDRESSES
                                </h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-address"></i>
                            </div>

                            <div class="p-8 hidden" id="content-address">

                                @php $addr = $employee->address; @endphp

                                @if($addr)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">City</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $addr->city ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Province</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $addr->province ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Postal Code</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $addr->state ?? $addr->postal_code ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Country</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $addr->country ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Full Address</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $addr->address ?? '-' }}</div>
                                    </div>
                                </div>
                                @else
                                    <p class="text-gray-500 italic">No address information recorded yet.</p>
                                @endif

                            </div>
                        </div>

                        <!-- 4. Contact Information -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('contact')">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fas fa-phone mr-3 text-indigo-600"></i>CONTACT INFORMATION
                                </h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-contact"></i>
                            </div>

                            <div class="p-8 hidden" id="content-contact">

                                @php $c = $employee->contact; @endphp

                                @if($c)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Phone Number</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $c->phone_number ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Home Phone</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $c->home_phone ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Office Phone</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $c->office_phone ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Email</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $c->email ?? '-' }}</div>
                                    </div>

                                </div>
                                @else
                                    <p class="text-gray-500 italic">No contact information recorded yet.</p>
                                @endif

                            </div>
                        </div>

                        <!-- 2. Identifications -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('identification')">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fas fa-id-card mr-3 text-indigo-600"></i>IDENTIFICATIONS
                                </h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-identification"></i>
                            </div>

                            <div class="p-8 hidden" id="content-identification">

                                @php $id = $employee->identification; @endphp

                                @if($id)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                    <!-- Identification Type -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Identification Type</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">
                                            {{ $id->identification_type ?? '-' }}
                                        </div>
                                    </div>

                                    <!-- Identification Number -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Identification Number</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">
                                            {{ $id->identification_number ?? '-' }}
                                        </div>
                                    </div>

                                    <!-- Expiration Date -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Expiration Date</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">
                                            {{ $id->expiration_date ?? '-' }}
                                        </div>
                                    </div>

                                </div>
                                @else
                                    <p class="text-gray-500 italic">No identification information recorded yet.</p>
                                @endif

                            </div>
                        </div>
                        
                        <!-- 5. Emergency Contacts -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('emergencyContact')">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fas fa-phone-alt mr-3 text-red-600"></i>EMERGENCY CONTACTS
                                </h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-emergencyContact"></i>
                            </div>

                            <div class="p-8 hidden" id="content-emergencyContact">

                                @if($employee->emergencyContacts->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    @foreach($employee->emergencyContacts as $ec)
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Contact Person</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $ec->contact_person }}</div>
                                        </div>

                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Relationship</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $ec->relationship }}</div>
                                        </div>

                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Phone Number</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $ec->phone_number }}</div>
                                        </div>
                                    @endforeach
                                </div>
                                @else
                                    <p class="text-gray-500 italic">No emergency contacts recorded yet.</p>
                                @endif

                            </div>
                        </div>


                        <!-- 6. Family Members -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('family')">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fas fa-users mr-3 text-indigo-600"></i>FAMILY MEMBERS
                                </h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-family"></i>
                            </div>

                            <div class="p-8 hidden" id="content-family">

                                @if($employee->familyMembers->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    @foreach($employee->familyMembers as $f)
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Name</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $f->name }}</div>
                                        </div>

                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Relationship</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $f->relationship }}</div>
                                        </div>

                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Date of Birth</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $f->dob }}</div>
                                        </div>

                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Gender</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $f->gender }}</div>
                                        </div>

                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Nationality</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $f->nationality }}</div>
                                        </div>

                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Tax Filing</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $f->tax_filing }}</div>
                                        </div>

                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Phone Number</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $f->phone_number }}</div>
                                        </div>

                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Remark</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $f->remark }}</div>
                                        </div>

                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Attachment</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">
                                                <a href="{{ asset('storage/' . $f->attachment) }}" target="_blank"
                                                class="text-indigo-600 hover:text-indigo-800 flex items-center">
                                                    <i class="fas fa-download mr-2"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @else
                                    <p class="text-gray-500 italic">No family members recorded yet.</p>
                                @endif

                            </div>
                        </div>


                        <!-- 7. Education History -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                            onclick="toggleAcc('education')">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-graduation-cap mr-3 text-indigo-600"></i>EDUCATION HISTORY
                            </h3>
                            <i class="fas fa-chevron-down transition duration-300" id="icon-education"></i>
                        </div>

                        <div class="p-8 hidden" id="content-education">

                            @if($employee->educationHistories->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($employee->educationHistories as $edu)

                                    <!-- Institute -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Institute</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $edu->institute }}</div>
                                    </div>

                                    <!-- Subject -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Subject</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $edu->subject }}</div>
                                    </div>

                                    <!-- Degree -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Degree</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $edu->degree }}</div>
                                    </div>

                                    <!-- Start Date -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Start Date</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $edu->start_date }}</div>
                                    </div>

                                    <!-- End Date -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">End Date</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $edu->end_date }}</div>
                                    </div>

                                    <!-- Remark -->
                                    <div>
                                        <label class="text-gray-600 text-sm font-semibold">Remark</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $edu->remark ?? '-' }}</div>
                                    </div>
                                @endforeach
                            </div>
                            @else
                                <p class="text-gray-500 italic">No education history recorded.</p>
                            @endif

                        </div>
                    </div>


                        <!-- 8. Training & Certifications -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('training')">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fas fa-certificate mr-3 text-indigo-600"></i>TRAINING HISTORY INFO
                                </h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-training"></i>
                            </div>

                            <div class="p-8 hidden" id="content-training">
                                @if($employee->trainingHistories->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($employee->trainingHistories as $train) 
                                        <!-- Institute -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Institute</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $train->institute ?? '-' }}</div>
                                        </div>

                                        <!-- Subject -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Subject</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $train->subject ?? '-' }}</div>
                                        </div>

                                        <!-- Start Date -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Start Date</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $train->start_date ?? '-' }}</div>
                                        </div>

                                        <!-- End Date -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">End Date</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $train->end_date ?? '-' }}</div>
                                        </div>

                                        <!-- Remark -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Remark</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $train->remark ?? '-' }}</div>
                                        </div>

                                        <!-- Attachment -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Attachment</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">
                                                @if($train->attachment)
                                                    <a href="{{ asset('storage/' . $train->attachment) }}" target="_blank"
                                                        class="text-indigo-600 hover:text-indigo-800 flex items-center">
                                                        <i class="fas fa-download mr-2"></i> Download
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @else
                                    <p class="text-gray-500 italic">No training records found.</p>
                                @endif
                            </div>
                        </div>


                        <!-- 9. EMPLOYMENT HISTORY INFO -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('employment')">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fas fa-briefcase mr-3 text-indigo-600"></i>EMPLOYMENT HISTORY INFO
                                </h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-employment"></i>
                            </div>

                            <div class="p-8 hidden" id="content-employment">
                                @if($employee->employmentHistories->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($employee->employmentHistories as $job)

                                        <!-- Company Name -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Company Name</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $job->company_name ?? '-' }}</div>
                                        </div>

                                        <!-- Start Date -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Start Date</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $job->start_date ?? '-' }}</div>
                                        </div>

                                        <!-- End Date -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">End Date</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $job->end_date ?? '-' }}</div>
                                        </div>

                                        <!-- Designation -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Designation</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $job->designation ?? '-' }}</div>
                                        </div>

                                        <!-- Supervisor Name -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Supervisor Name</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $job->supervisor_name ?? '-' }}</div>
                                        </div>

                                        <!-- Remark -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Remark</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $job->remark ?? '-' }}</div>
                                        </div>

                                        <!-- Rate -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Rate</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $job->rate ?? '-' }}</div>
                                        </div>

                                        <!-- Reason for Leaving -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Reason for Leaving</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $job->reason_for_leaving ?? '-' }}</div>
                                        </div>
                                    @endforeach
                                </div>
                                @else
                                    <p class="text-gray-500 italic">No previous employment recorded.</p>
                                @endif
                            </div>
                        </div>


                        <!-- 10. Achievements & Awards -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('achievements')">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fas fa-trophy mr-3 text-indigo-600"></i>ACHIEVEMENTS
                                </h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-achievements"></i>
                            </div>

                            <div class="p-8 hidden" id="content-achievements">
                                @if($employee->achievements->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($employee->achievements as $ach)

                                        <!-- Title -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Title</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $ach->title ?? '-' }}</div>
                                        </div>

                                        <!-- Year Awarded -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Year Awarded</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $ach->year_awarded ?? '-' }}</div>
                                        </div>

                                        <!-- Program Name -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Program Name</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $ach->program_name ?? '-' }}</div>
                                        </div>

                                        <!-- Country -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Country</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $ach->country ?? '-' }}</div>
                                        </div>

                                        <!-- Organizer Name -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Organizer Name</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $ach->organizer_name ?? '-' }}</div>
                                        </div>

                                        <!-- Remark -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Remark</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">{{ $ach->remark ?? '-' }}</div>
                                        </div>

                                        <!-- Attachment -->
                                        <div>
                                            <label class="text-gray-600 text-sm font-semibold">Attachment</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border">
                                                @if($ach->attachment)
                                                    <a href="{{ asset('storage/' . $ach->attachment) }}" target="_blank"
                                                    class="text-indigo-600 hover:text-indigo-800 flex items-center">
                                                    <i class="fas fa-download mr-2"></i> Download
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @else
                                    <p class="text-gray-500 italic">No achievements recorded yet.</p>
                                @endif
                            </div>
                        </div>


                        <!-- 11. Documents & Attachments -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('attachments')">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fas fa-paperclip mr-3 text-indigo-600"></i>ATTACHMENTS
                                </h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-attachments"></i>
                            </div>

                            <div class="p-8 hidden" id="content-attachments">
                                @if($employee->attachments->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach($employee->attachments as $file)

                                            <!-- Attachment Name -->
                                            <div>
                                                <label class="text-gray-600 text-sm font-semibold">Attachment Name</label>
                                                <div class="mt-1 p-3 bg-gray-50 rounded-lg border">
                                                    {{ $file->attachment_name ?? '-' }}
                                                </div>
                                            </div>

                                            <!-- File / Download -->
                                            <div>
                                                <label class="text-gray-600 text-sm font-semibold">File</label>
                                                <div class="mt-1 p-3 bg-gray-50 rounded-lg border">
                                                    @if(!empty($file->file_path))
                                                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                                        class="text-indigo-600 hover:text-indigo-800 flex items-center">
                                                        <i class="fas fa-download mr-2"></i> Download
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                            </div>

                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 italic">No documents uploaded.</p>
                                @endif
                            </div>
                        </div>


                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Accordion Toggle Script -->
    <script>
        function toggleAcc(id) {
            const content = document.getElementById('content-' + id);
            const icon = document.getElementById('icon-' + id);

            content.classList.toggle('hidden');
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
        }

        // Open Personal Information by default
        document.addEventListener('DOMContentLoaded', () => {
            toggleAcc('personal');
        });
    </script>

    @include('hr.partials.notification-script')
</body>

</html>