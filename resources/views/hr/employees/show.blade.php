{{-- resources/views/hr/employees/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $employee->user->name ?? 'Employee' }} - Profile | {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        .rotate-180 {
            transform: rotate(180deg);
        }
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-indigo-800 to-indigo-900 text-white flex flex-col">
            <div class="p-6 text-center border-b border-indigo-700">
                <h2 class="text-2xl font-bold">HR Dashboard</h2>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('hr.dashboard') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
                <a href="{{ route('hr.employees.index') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-indigo-700 font-bold">
                    <i class="fas fa-users"></i><span>Employees</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-clock"></i><span>Attendance</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-calendar-check"></i><span>Leave Requests</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-route"></i><span>Missions</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-car"></i><span>Company Vehicles</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-money-bill-wave"></i><span>Payroll</span>
                </a>
            </nav>
            <div class="p-4 border-t border-indigo-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

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
                    <div
                        class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 flex flex-col md:flex-row items-center gap-8">
                        <img src="{{ $employee->image ? asset('storage/' . $employee->image) : asset('images/default-avatar.png') }}"
                            alt="Profile"
                            class="w-40 h-40 rounded-full object-cover border-6 border-indigo-100 shadow-lg">
                        <div class="text-center md:text-left">
                            <h2 class="text-4xl font-extrabold text-gray-900">{{ $employee->user->name }}</h2>
                            <p class="text-2xl text-indigo-600 font-medium mt-1">
                                {{ $employee->position?->position_name ?? 'No Position Assigned' }}
                            </p>
                            <div class="flex flex-wrap gap-6 mt-4 text-gray-600">
                                <span><i
                                        class="fas fa-building mr-2"></i>{{ $employee->branch?->branch_name ?? '-' }}</span>
                                <span><i class="fas fa-envelope mr-2"></i>{{ $employee->user->email }}</span>
                                <span><i
                                        class="fas fa-id-badge mr-2"></i>#{{ str_pad($employee->id, 6, '0', STR_PAD_LEFT) }}</span>
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

                            <div class="p-8 overflow-x-auto" id="content-personal">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <!-- Table header always visible -->
                                    <thead
                                        class="{{ $tableHead ?? 'bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider' }}">
                                        <tr>
                                            <th class="px-6 py-3">No.</th>
                                            <th class="px-6 py-3">Employee Code</th>
                                            <th class="px-6 py-3">Salutation</th>
                                            <th class="px-6 py-3">Name(KH)</th>
                                            <th class="px-6 py-3">Name(EN)</th>
                                            <th class="px-6 py-3">Gender</th>
                                            <th class="px-6 py-3">Date of Birth</th>
                                            <th class="px-6 py-3">Nationality</th>
                                            <th class="px-6 py-3">Marital Status</th>
                                            <th class="px-6 py-3">Religion</th>
                                            <th class="px-6 py-3">Blood Group</th>
                                            <th class="px-6 py-3">Bank Account</th>
                                            <th class="px-6 py-3">Join data</th>
                                            <th class="px-6 py-3">Effective Data</th>
                                            <th class="px-6 py-3">End Data</th>
                                            <th class="px-6 py-3">Contract Type</th>
                                            <th class="px-6 py-3">Employee Type</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse([$employee->personalInfo] as $info)
                                            <!-- This runs only if personalInfo exists -->
                                            <tr>
                                                <td class="{{ $tableCell ?? 'px-6 py-4 text-sm text-gray-900' }}">
                                                    {{ $employee->id ?? '-' }}
                                                </td>
                                                <td class="{{ $tableCell ?? 'px-6 py-4 text-sm text-gray-900' }}">
                                                    {{ $employee->employee_code ?? '-' }}
                                                </td>
                                                <td class="{{ $tableCell ?? 'px-6 py-4 text-sm text-gray-900' }}">
                                                    {{ $info?->salutation ?? '-' }}
                                                </td>
                                                <td class="{{ $tableCell ?? 'px-6 py-4 text-sm text-gray-900' }}">
                                                    {{ $info?->full_name_kh ?? '-' }}
                                                </td>
                                                <td class="{{ $tableCell }}">{{ $info?->full_name_en ?? '-' }}</td>
                                                <td class="{{ $tableCell }}">
                                                    {{ $info?->gender ? ucfirst($info->gender) : '-' }}
                                                </td>
                                                <td class="{{ $tableCell }}">{{ $info?->dob ?? '-' }}</td>
                                                <td class="{{ $tableCell }}">{{ $info?->nationality ?? '-' }}</td>
                                                <td class="{{ $tableCell }}">
                                                    {{ $info?->marital_status ? ucfirst($info->marital_status) : '-' }}
                                                </td>
                                                <td class="{{ $tableCell }}">{{ $info?->religion ?? '-' }}</td>
                                                <td class="{{ $tableCell }}">{{ $info?->blood_group ?? '-' }}</td>
                                                <td class="{{ $tableCell }}">{{ $info?->bank_account_number ?? '-' }}</td>
                                                <td class="{{ $tableCell }}">{{ $info?->joining_data ?? '-' }}</td>
                                                <td class="{{ $tableCell }}">{{ $info?->effective_data ?? '-' }}</td>
                                                <td class="{{ $tableCell }}">{{ $info?->end_data ?? '-' }}</td>
                                                <td class="{{ $tableCell }}">{{ $info?->contract_type ?? '-' }}</td>
                                                <td class="{{ $tableCell }}">{{ $info?->employee_type ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <!-- Shown when personalInfo is null or missing -->
                                            <tr>
                                                <td colspan="9" class="px-6 py-12 text-center text-gray-500 italic">
                                                    No personal information recorded yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- 2. Identifications -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('identifications')">
                                <h3 class="text-lg font-semibold text-gray-800"><i
                                        class="fas fa-id-card mr-3 text-indigo-600"></i>IDENTIFICATIONS</h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-identifications"></i>
                            </div>
                            <div class="p-8 hidden overflow-x-auto" id="content-identifications">
                                @if($employee->identifications->count() >= 0)
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="{{ $tableHead }}">
                                            <tr>
                                                <th class="px-6 py-3">No.</th>
                                                <th class="px-6 py-3">Identification Type</th>
                                                <th class="px-6 py-3">Identification Number</th>
                                                <th class="px-6 py-3">Expiration Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($employee->identifications as $id)
                                                <tr>
                                                    <td class="{{ $tableCell }}">{{ $id->id }}</td>
                                                    <td class="{{ $tableCell }}">{{ $id->identification_type }}</td>
                                                    <td class="{{ $tableCell }}">{{ $id->identification_number }}</td>
                                                    <td class="{{ $tableCell }}">{{ $id->expiration_date }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-gray-500 italic">No identification documents recorded.</p>
                                @endif
                            </div>
                        </div>

                        <!-- 3. Addresses -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('addresses')">
                                <h3 class="text-lg font-semibold text-gray-800"><i
                                        class="fas fa-home mr-3 text-indigo-600"></i>PERMANENT ADDRESSES</h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-addresses"></i>
                            </div>
                            <div class="p-8 hidden overflow-x-auto" id="content-addresses">
                                @if($employee->addresses->count() >= 0)
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="{{ $tableHead }}">
                                            <tr>
                                                <th class="px-6 py-3">No.</th>
                                                <th class="px-6 py-3">Address</th>
                                                <th class="px-6 py-3">City</th>
                                                <th class="px-6 py-3">Province</th>
                                                <th class="px-6 py-3">Postal Code</th>
                                                <th class="px-6 py-3">Country</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($employee->addresses as $addr)
                                                <tr>
                                                    <td class="{{ $tableCell }}">{{ $addr->id }}</td>
                                                    <td class="{{ $tableCell }}">{{ $addr->address }}</td>
                                                    <td class="{{ $tableCell }}">{{ $addr->city }}</td>
                                                    <td class="{{ $tableCell }}">{{ $addr->province }}</td>
                                                    <td class="{{ $tableCell }}">{{ $addr->state ?? '' }}
                                                        {{ $addr->postal_code }}
                                                    </td>
                                                    <td class="{{ $tableCell }}">{{ $addr->country }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-gray-500 italic">No addresses recorded.</p>
                                @endif
                            </div>
                        </div>

                        <!-- 4. Contact Information -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('contacts')">
                                <h3 class="text-lg font-semibold text-gray-800"><i
                                        class="fas fa-phone mr-3 text-indigo-600"></i>CONTACT INFORMATION</h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-contacts"></i>
                            </div>
                            <div class="p-8 hidden overflow-x-auto" id="content-contacts">
                                @if($employee->contacts->count() >= 0)
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="{{ $tableHead }}">
                                            <tr>
                                                <th class="px-6 py-3">No.</th>
                                                <th class="px-6 py-3">Phone Number</th>
                                                <th class="px-6 py-3">Home Phone Number</th>
                                                <th class="px-6 py-3">Office Phone Number</th>
                                                <th class="px-6 py-3">Email</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($employee->contacts as $c)
                                                <tr>
                                                    <td class="{{ $tableCell }}">{{ $c->id}}</td>
                                                    <td class="{{ $tableCell }}">{{ $c->phone_number}}</td>
                                                    <td class="{{ $tableCell }}">{{ $c->home_phone }}</td>
                                                    <td class="{{ $tableCell }}">{{ $c->office_phone}}</td>
                                                    <td class="{{ $tableCell }}">{{ $c->email }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-gray-500 italic">No contact numbers added.</p>
                                @endif
                            </div>
                        </div>

                        <!-- 5. Emergency Contacts -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('emergency')">
                                <h3 class="text-lg font-semibold text-gray-800"><i
                                        class="fas fa-phone-alt mr-3 text-red-600"></i>EMERGENCY CONTACTS</h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-emergency"></i>
                            </div>
                            <div class="p-8 hidden overflow-x-auto" id="content-emergency">
                                @if($employee->emergencyContacts->count() >= 0)
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="{{ $tableHead }}">
                                            <tr>
                                                <th class="px-6 py-3">No.</th>
                                                <th class="px-6 py-3">Contact Person</th>
                                                <th class="px-6 py-3">Relationship</th>
                                                <th class="px-6 py-3">Phone Number</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($employee->emergencyContacts as $ec)
                                                <tr>
                                                    <td class="{{ $tableCell }}">{{ $ec->id }}</td>
                                                    <td class="{{ $tableCell }}">{{ $ec->contact_person }}</td>
                                                    <td class="{{ $tableCell }}">{{ $ec->relationship }}</td>
                                                    <td class="{{ $tableCell }}">{{ $ec->phone_number }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-gray-500 italic">No emergency contacts registered.</p>
                                @endif
                            </div>
                        </div>

                        <!-- 6. Family Members -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('family')">
                                <h3 class="text-lg font-semibold text-gray-800"><i
                                        class="fas fa-users mr-3 text-indigo-600"></i>FAMILY MEMBERS</h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-family"></i>
                            </div>
                            <div class="p-8 hidden overflow-x-auto" id="content-family">
                                @if($employee->familyMembers->count() >= 0)
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="{{ $tableHead }}">
                                            <tr>
                                                <th class="px-6 py-3">No.</th>
                                                <th class="px-6 py-3">Name</th>
                                                <th class="px-6 py-3">Relationship</th>
                                                <th class="px-6 py-3">Date of Birth</th>
                                                <th class="px-6 py-3">Gender</th>
                                                <th class="px-6 py-3">Nationality</th>
                                                <th class="px-6 py-3">Tax Filling</th>
                                                <th class="px-6 py-3">Phone Number</th>
                                                <th class="px-6 py-3">Remark</th>
                                                <th class="px-6 py-3">Attachment</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($employee->familyMembers as $m)
                                                <tr>
                                                    <td class="{{ $tableCell }}">{{ $m->id }}</td>
                                                    <td class="{{ $tableCell }}">{{ $m->name }}</td>
                                                    <td class="{{ $tableCell }}">{{ $m->relationship }}</td>
                                                    <td class="{{ $tableCell }}">{{ $m->dob }}</td>
                                                    <td class="{{ $tableCell }}">{{ $m->gender }}</td>
                                                    <td class="{{ $tableCell }}">{{ $m->nationality }}</td>
                                                    <td class="{{ $tableCell }}">{{ $m->tax_filling }}</td>
                                                    <td class="{{ $tableCell }}">{{ $m->phone_number }}</td>
                                                    <td class="{{ $tableCell }}">{{ $m->remark }}</td>
                                                    <td class="{{ $tableCell }}">{{ $m->attachment }}</td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-gray-500 italic">No family members added.</p>
                                @endif
                            </div>
                        </div>

                        <!-- 7. Education History -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('education')">
                                <h3 class="text-lg font-semibold text-gray-800"><i
                                        class="fas fa-graduation-cap mr-3 text-indigo-600"></i>EDUCATION HISTORY</h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-education"></i>
                            </div>
                            <div class="p-8 hidden overflow-x-auto" id="content-education">
                                @if($employee->educationHistories->count() >= 0)
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="{{ $tableHead }}">
                                            <tr>
                                                <th class="px-6 py-3">No.</th>
                                                <th class="px-6 py-3">Institutue</th>
                                                <th class="px-6 py-3">Subject</th>
                                                <th class="px-6 py-3">Degree</th>
                                                <th class="px-6 py-3">Start Date</th>
                                                <th class="px-6 py-3">End Date</th>
                                                <th class="px-6 py-3">Remark</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($employee->educationHistories as $edu)
                                                <tr>
                                                    <td class="{{ $tableCell }}">{{ $edu->id }}</td>
                                                    <td class="{{ $tableCell }}">{{ $edu->institute }}</td>
                                                    <td class="{{ $tableCell }}">{{ $edu->subject }}</td>
                                                    <td class="{{ $tableCell }}">{{ $edu->degree }}</td>
                                                    <td class="{{ $tableCell }}">{{ $edu->start_date}}</td>
                                                    <td class="{{ $tableCell }}">{{ $edu->end_date }}</td>
                                                    <td class="{{ $tableCell }}">{{ $edu->remark ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-gray-500 italic">No education history recorded.</p>
                                @endif
                            </div>
                        </div>

                        <!-- 8. Training & Certifications -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('training')">
                                <h3 class="text-lg font-semibold text-gray-800"><i
                                        class="fas fa-certificate mr-3 text-indigo-600"></i>TRAINING HISTORY INFO
                                </h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-training"></i>
                            </div>
                            <div class="p-8 hidden overflow-x-auto" id="content-training">
                                @if($employee->trainingHistories->count() >= 0)
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="{{ $tableHead }}">
                                            <tr>
                                                <th class="px-6 py-3">No.</th>
                                                <th class="px-6 py-3">Institute</th>
                                                <th class="px-6 py-3">Subject</th>
                                                <th class="px-6 py-3">Start Date</th>
                                                <th class="px-6 py-3">End Date</th>
                                                <th class="px-6 py-3">Remark</th>
                                                <th class="px-6 py-3">Start Date</th>
                                                <th class="px-6 py-3">Attachment</th>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($employee->trainingHistories as $train)
                                                <tr>
                                                    <td class="{{ $tableCell }}">{{ $train->id ?? '-' }}</td>
                                                    <td class="{{ $tableCell }}">{{ $train->institute ?? '-' }}</td>
                                                    <td class="{{ $tableCell }}">{{ $train->subject ?? '-'}}</td>
                                                    <td class="{{ $tableCell }}">{{ $train->start_date ?? '-'}}</td>
                                                    <td class="{{ $tableCell }}">{{ $train->end_date ?? '-'}}</td>
                                                    <td class="{{ $tableCell }}">{{ $train->remark ?? '-'}}</td>
                                                    <td class="{{ $tableCell }}">{{ $train->attachment ?? '-'}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-gray-500 italic">No training records found.</p>
                                @endif
                            </div>
                        </div>

                        <!-- 9. EMPLOYMENT HISTORY INFO -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('employment')">
                                <h3 class="text-lg font-semibold text-gray-800"><i
                                        class="fas fa-briefcase mr-3 text-indigo-600"></i>EMPLOYMENT HISTORY INFO</h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-employment"></i>
                            </div>
                            <div class="p-8 hidden overflow-x-auto" id="content-employment">
                                @if($employee->employmentHistories->count() >= 0)
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="{{ $tableHead }}">
                                            <tr>
                                                <th class="px-6 py-3">No.</th>
                                                <th class="px-6 py-3">Company Name</th>
                                                <th class="px-6 py-3">Start Date</th>
                                                <th class="px-6 py-3">End Date</th>
                                                <th class="px-6 py-3">Designation</th>
                                                <th class="px-6 py-3">Supervisor Name</th>
                                                <th class="px-6 py-3">Remark</th>
                                                <th class="px-6 py-3">Rate</th>
                                                <th class="px-6 py-3">Reason for leaving</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($employee->employmentHistories as $job)
                                                <tr>
                                                    <td class="{{ $tableCell }}">{{ $job->id ?? '-' }}</td>
                                                    <td class="{{ $tableCell }}">{{ $job->company_name ?? '-' }}</td>
                                                    <td class="{{ $tableCell }}">{{ $job->start_date ?? '-' }}</td>
                                                    <td class="{{ $tableCell }}">{{ $job->end_date ?? '-' }}</td>
                                                    <td class="{{ $tableCell }}">{{ $job->designation ?? '-' }}</td>
                                                    <td class="{{ $tableCell }}">{{ $job->supervisor_name ?? '-' }}</td>
                                                    <td class="{{ $tableCell }}">{{ $job->remark ?? '-' }}</td>
                                                    <td class="{{ $tableCell }}">{{ $job->rate ?? '-' }}</td>
                                                    <td class="{{ $tableCell }}">{{ $job->resean_for_leaving ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-gray-500 italic">No previous employment recorded.</p>
                                @endif
                            </div>
                        </div>

                        <!-- 10. Achievements & Awards -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('achievements')">
                                <h3 class="text-lg font-semibold text-gray-800"><i
                                        class="fas fa-trophy mr-3 text-indigo-600"></i>ACHIEVEMENTS </h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-achievements"></i>
                            </div>
                            <div class="p-8 hidden overflow-x-auto" id="content-achievements">
                                @if($employee->achievements->count() >= 0)
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="{{ $tableHead }}">
                                            <tr>
                                                <th class="px-6 py-3">No.</th>
                                                <th class="px-6 py-3">Title</th>
                                                <th class="px-6 py-3">Year Awarded</th>
                                                <th class="px-6 py-3">Program Name</th>
                                                <th class="px-6 py-3">Organizer Name</th>
                                                <th class="px-6 py-3">Remark</th>
                                                <th class="px-6 py-3">Attachment</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($employee->achievements as $ach)
                                                <tr>
                                                    <td class="{{ $tableCell }}">{{ $ach->id }}</td>
                                                    <td class="{{ $tableCell }}">{{ $ach->salutation }}</td>
                                                    <td class="{{ $tableCell }}">{{ $ach->year_awarded }}</td>
                                                    <td class="{{ $tableCell }}">{{ $ach->country }}</td>
                                                    <td class="{{ $tableCell }}">{{ $ach->program_name }}</td>
                                                    <td class="{{ $tableCell }}">{{ $ach->organizer_name }}</td>
                                                    <td class="{{ $tableCell }}">{{ $ach->remark }}</td>
                                                    <td class="{{ $tableCell }}">{{ $ach->attachment }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-gray-500 italic">No achievements recorded yet.</p>
                                @endif
                            </div>
                        </div>

                        <!-- 11. Documents & Attachments -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-5 flex justify-between items-center cursor-pointer hover:bg-gray-100"
                                onclick="toggleAcc('attachments')">
                                <h3 class="text-lg font-semibold text-gray-800"><i
                                        class="fas fa-paperclip mr-3 text-indigo-600"></i>ATTACHMENTS</h3>
                                <i class="fas fa-chevron-down transition duration-300" id="icon-attachments"></i>
                            </div>
                            <div class="p-8 hidden overflow-x-auto" id="content-attachments">
                                @if($employee->attachments->count() >= 0)
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="{{ $tableHead }}">
                                            <tr>
                                                <th class="px-6 py-3">No.</th>
                                                <th class="px-6 py-3">Title</th>
                                                <th class="px-6 py-3">File Name</th>
                                                <th class="px-6 py-3">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($employee->attachments as $file)
                                                <tr>
                                                    <td class="{{ $tableCell }}">{{ $file->title }}</td>
                                                    <td class="{{ $tableCell }}">{{ $file->file_name }}</td>
                                                    <td class="{{ $tableCell }}">
                                                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                                            class="text-indigo-600 hover:text-indigo-800 flex items-center">
                                                            <i class="fas fa-download mr-2"></i> Download
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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