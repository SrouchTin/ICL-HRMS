{{-- resources/views/employee/my-profile.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile • {{ Auth::user()->username }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .card-header {
            padding: 1.5rem 2rem;
            cursor: pointer;
            transition: background 0.2s;
            border-bottom: 1px solid #e2e8f0;
        }

        .card-header:hover {
            background-color: #f9fafb;
        }

        .info-label {
            font-weight: 600;
            color: #4b5563;
            font-size: 0.875rem;
        }

        .info-value {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.875rem 1rem;
            font-weight: 500;
        }
    </style>
</head>

<body class="bg-gray-100">

    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
        @include('layout.employeeSidebar')

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-8 py-5 flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-800">My Profile</h1>
                    <a href="{{ route('employee.dashboard') }}"
                        class="text-white bg-indigo-600 hover:bg-indigo-600 hover:text-white rounded-md p-2 font-medium flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-6 md:p-8">
                <div class="max-w-7xl mx-auto space-y-8">

                    <!-- Profile Header -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 md:p-10">
                        <div class="flex flex-col md:flex-row items-center gap-10">
                            <div class="shrink-0">
                                <div
                                    class="w-64 h-64 rounded-full overflow-hidden border-8 border-white shadow-2xl ring-4 ring-indigo-100 bg-gray-200">
                                    <img src="{{ Auth::user()->employee?->image
    ? asset('storage/' . Auth::user()->employee->image)
    : asset('images/default-avatar.png') }}" alt="Profile Photo" class="w-full h-full object-cover object-top">
                                </div>
                            </div>
                            <div class="text-center md:text-left flex-1">
                                <h2 class="text-4xl font-extrabold text-gray-900">
                                    {{ Auth::user()->employee?->personalInfo?->full_name_en
    ?? Auth::user()->employee?->personalInfo?->full_name_kh
    ?? Auth::user()->username }}
                                </h2>
                                <p class="text-2xl text-indigo-600 font-medium mt-2">
                                    {{ Auth::user()->employee?->position?->position_name ?? 'No Position Assigned' }}
                                </p>
                                <div
                                    class="flex flex-wrap gap-8 mt-6 text-gray-600 text-lg items-center justify-center md:justify-start">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-building text-indigo-600"></i>
                                        {{ Auth::user()->employee?->branch?->branch_name ?? '—' }}
                                    </span>
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-envelope text-indigo-600"></i>
                                        {{ Auth::user()->employee?->contact?->email ?? Auth::user()->email ?? '—' }}
                                    </span>
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-id-badge text-indigo-600"></i>
                                        #{{ str_pad(Auth::user()->employee?->id ?? 0, 6, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- All 11 Sections - VIEW ONLY -->
                    <div class="space-y-6">

                        <!-- 1. Personal Information -->
                        <div x-data="{ open: true }" class="card">
                            <div @click="open = !open"
                                class="card-header bg-gradient-to-r from-indigo-50 to-blue-50 flex justify-between items-center">
                                <h3 class="text-xl font-bold text-indigo-900"><i
                                        class="fas fa-user mr-3 text-indigo-600"></i>Personal Information</h3>
                                <i x-show="open" class="fas fa-chevron-up text-indigo-600"></i>
                                <i x-show="!open" class="fas fa-chevron-down text-indigo-600"></i>
                            </div>
                            <div x-show="open" x-transition class="p-8 bg-gray-50">
                                @php $info = Auth::user()->employee?->personalInfo; @endphp
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div><span class="info-label">Employee Code</span>
                                        <div class="info-value mt-1">{{ Auth::user()->employee?->employee_code ?? '—' }}
                                        </div>
                                    </div>
                                    <div><span class="info-label">Salutation</span>
                                        <div class="info-value mt-1">{{ $info?->salutation ?? '—' }}</div>
                                    </div>
                                    <div><span class="info-label">Full Name (EN)</span>
                                        <div class="info-value mt-1">{{ $info?->full_name_en ?? '—' }}</div>
                                    </div>
                                    <div><span class="info-label">Full Name (KH)</span>
                                        <div class="info-value mt-1">{{ $info?->full_name_kh ?? '—' }}</div>
                                    </div>
                                    <div><span class="info-label">Gender</span>
                                        <div class="info-value mt-1">{{ ucfirst($info?->gender ?? '—') }}</div>
                                    </div>
                                    <div><span class="info-label">Date of Birth</span>
                                        <div class="info-value mt-1">
                                            {{ $info?->dob ? \Carbon\Carbon::parse($info->dob)->format('d F Y') : '—' }}
                                        </div>
                                    </div>
                                    <div><span class="info-label">Nationality</span>
                                        <div class="info-value mt-1">{{ $info?->nationality ?? '—' }}</div>
                                    </div>
                                    <div><span class="info-label">Marital Status</span>
                                        <div class="info-value mt-1">{{ ucfirst($info?->marital_status ?? '—') }}</div>
                                    </div>
                                    <div><span class="info-label">Religion</span>
                                        <div class="info-value mt-1">{{ $info?->religion ?? '—' }}</div>
                                    </div>
                                    <div><span class="info-label">Blood Group</span>
                                        <div class="info-value mt-1">{{ $info?->blood_group ?? '—' }}</div>
                                    </div>
                                    <div><span class="info-label">Bank Account Name</span>
                                        <div class="info-value mt-1">{{ $info?->bank_account_name ?? '—' }}</div>
                                    </div>
                                    <div><span class="info-label">Bank Account No.</span>
                                        <div class="info-value mt-1">{{ $info?->bank_account_number ?? '—' }}</div>
                                    </div>
                                    <div><span class="info-label">Joining Date</span>
                                        <div class="info-value mt-1">
                                            {{ $info?->joining_date ? \Carbon\Carbon::parse($info->joining_date)->format('d F Y') : '—' }}
                                        </div>
                                    </div>
                                    <div><span class="info-label">Effective Date</span>
                                        <div class="info-value mt-1">
                                            {{ $info?->effective_date ? \Carbon\Carbon::parse($info->effective_date)->format('d F Y') : '—' }}
                                        </div>
                                    </div>
                                    <div><span class="info-label">End Date</span>
                                        <div class="info-value mt-1">
                                            {{ $info?->end_date ? \Carbon\Carbon::parse($info->end_date)->format('d F Y') : '—' }}
                                        </div>
                                    </div>
                                    <div><span class="info-label">Contract Type</span>
                                        <div class="info-value mt-1">{{ $info?->contract_type ?? '—' }}</div>
                                    </div>
                                    <div><span class="info-label">Employee Type</span>
                                        <div class="info-value mt-1">{{ $info?->employee_type ?? '—' }}</div>
                                    </div>
                                </div>
                                @if(!$info)
                                    <p class="text-gray-500 italic text-center py-8">No personal information recorded yet.
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- 2. Permanent Address -->
                        <div x-data="{ open: false }" class="card">
                            <div @click="open = !open"
                                class="card-header bg-gradient-to-r from-indigo-50 to-blue-50 flex justify-between items-center">
                                <h3 class="text-xl font-bold text-indigo-900"><i
                                        class="fas fa-home mr-3 text-indigo-600"></i>Permanent Address</h3>
                                <i x-show="open" class="fas fa-chevron-up text-amber-600"></i>
                                <i x-show="!open" class="fas fa-chevron-down text-amber-600"></i>
                            </div>
                            <div x-show="open" x-transition class="p-8 bg-gray-50">
                                @php $addr = Auth::user()->employee?->address @endphp
                                @if($addr)
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div><span class="info-label">Address</span>
                                            <div class="info-value mt-1">{{ $addr->address ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">City</span>
                                            <div class="info-value mt-1">{{ $addr->city ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Province</span>
                                            <div class="info-value mt-1">{{ $addr->province ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Postal Code</span>
                                            <div class="info-value mt-1">{{ $addr->postal_code ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Country</span>
                                            <div class="info-value mt-1">{{ $addr->country ?? '—' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-gray-500 italic text-center py-8">No address recorded.</p>
                                @endif
                            </div>
                        </div>

                        <!-- 3. Contact Information -->
                        <div x-data="{ open: false }" class="card">
                            <div @click="open = !open"
                                class="card-header bg-gradient-to-r from-indigo-50 to-blue-50 flex justify-between items-center">
                                <h3 class="text-xl font-bold text-indigo-900"><i
                                        class="fas fa-phone mr-3 text-indigo-600"></i>Contact Information</h3>
                                <i x-show="open" class="fas fa-chevron-up text-cyan-600"></i>
                                <i x-show="!open" class="fas fa-chevron-down text-cyan-600"></i>
                            </div>
                            <div x-show="open" x-transition class="p-8 bg-gray-50">
                                @php $c = Auth::user()->employee?->contact @endphp
                                @if($c)
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div><span class="info-label">Phone Number</span>
                                            <div class="info-value mt-1">{{ $c->phone_number ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Home Phone</span>
                                            <div class="info-value mt-1">{{ $c->home_phone ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Office Phone</span>
                                            <div class="info-value mt-1">{{ $c->office_phone ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Email</span>
                                            <div class="info-value mt-1">{{ $c->email ?? '—' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-gray-500 italic text-center py-8">No contact information recorded.</p>
                                @endif
                            </div>
                        </div>

                        <!-- 4. Identification -->
                        <div x-data="{ open: false }" class="card">
                            <div @click="open = !open"
                                class="card-header bg-gradient-to-r from-indigo-50 to-blue-50 flex justify-between items-center">
                                <h3 class="text-xl font-bold text-indigo-900"><i
                                        class="fas fa-id-card mr-3 text-indigo-600"></i>Identification</h3>
                                <i x-show="open" class="fas fa-chevron-up text-teal-600"></i>
                                <i x-show="!open" class="fas fa-chevron-down text-teal-600"></i>
                            </div>
                            <div x-show="open" x-transition class="p-8 bg-gray-50">
                                @php $id = Auth::user()->employee?->identification @endphp
                                @if($id)
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div><span class="info-label">Type</span>
                                            <div class="info-value mt-1">{{ $id->identification_type ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Number</span>
                                            <div class="info-value mt-1">{{ $id->identification_number ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Expiration Date</span>
                                            <div class="info-value mt-1">
                                                {{ $id->expiration_date ? \Carbon\Carbon::parse($id->expiration_date)->format('d F Y') : '—' }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-gray-500 italic text-center py-8">No identification recorded.</p>
                                @endif
                            </div>
                        </div>

                        <!-- 5. Emergency Contacts -->
                        <div x-data="{ open: false }" class="card">
                            <div @click="open = !open"
                                class="card-header bg-gradient-to-r from-indigo-50 to-blue-50 flex justify-between items-center">
                                <h3 class="text-xl font-bold text-indigo-900"><i
                                        class="fas fa-phone-alt mr-3 text-red-600"></i>Emergency Contacts</h3>
                                <i x-show="open" class="fas fa-chevron-up text-red-600"></i>
                                <i x-show="!open" class="fas fa-chevron-down text-red-600"></i>
                            </div>
                            <div x-show="open" x-transition class="p-8 bg-gray-50">
                                @forelse(Auth::user()->employee?->emergencyContacts as $ec)
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 pb-8 border-b last:border-0">
                                        <div><span class="info-label">Contact Person</span>
                                            <div class="info-value mt-1">{{ $ec->contact_person ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Relationship</span>
                                            <div class="info-value mt-1">{{ $ec->relationship ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Phone Number</span>
                                            <div class="info-value mt-1">{{ $ec->phone_number ?? '—' }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 italic text-center py-8">No emergency contacts added.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- 6. Family Members -->
                        <div x-data="{ open: false }" class="card">
                            <div @click="open = !open"
                                class="card-header bg-gradient-to-r from-indigo-50 to-blue-50 flex justify-between items-center">
                                <h3 class="text-xl font-bold text-indigo-900"><i
                                        class="fas fa-users mr-3 text-pink-600"></i>Family Members</h3>
                                <i x-show="open" class="fas fa-chevron-up text-pink-600"></i>
                                <i x-show="!open" class="fas fa-chevron-down text-pink-600"></i>
                            </div>
                            <div x-show="open" x-transition class="p-8 bg-gray-50">
                                @forelse(Auth::user()->employee?->familyMembers as $fm)
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 pb-8 border-b last:border-0">
                                        <div><span class="info-label">Name</span>
                                            <div class="info-value mt-1">{{ $fm->name ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Relationship</span>
                                            <div class="info-value mt-1">{{ $fm->relationship ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Date of Birth</span>
                                            <div class="info-value mt-1">
                                                {{ $fm->dob ? \Carbon\Carbon::parse($fm->dob)->format('d F Y') : '—' }}
                                            </div>
                                        </div>
                                        <div><span class="info-label">Gender</span>
                                            <div class="info-value mt-1">{{ ucfirst($fm->gender ?? '—') }}</div>
                                        </div>
                                        <div><span class="info-label">Phone</span>
                                            <div class="info-value mt-1">{{ $fm->phone_number ?? '—' }}</div>
                                        </div>
                                        @if($fm->attachment)
                                            <div class="col-span-full"><a href="{{ asset('storage/' . $fm->attachment) }}"
                                                    target="_blank" class="text-indigo-600 hover:underline">View Attachment</a>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-gray-500 italic text-center py-8">No family members recorded.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- 7. Education History -->
                        <div x-data="{ open: false }" class="card">
                            <div @click="open = !open"
                                class="card-header bg-gradient-to-r from-indigo-50 to-blue-50 flex justify-between items-center">
                                <h3 class="text-xl font-bold text-indigo-900"><i
                                        class="fas fa-graduation-cap mr-3 text-emerald-600"></i>Education History</h3>
                                <i x-show="open" class="fas fa-chevron-up text-emerald-600"></i>
                                <i x-show="!open" class="fas fa-chevron-down text-emerald-600"></i>
                            </div>
                            <div x-show="open" x-transition class="p-8 bg-gray-50">
                                @forelse(Auth::user()->employee?->educationHistories as $edu)
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 pb-8 border-b last:border-0">
                                        <div><span class="info-label">Institute</span>
                                            <div class="info-value mt-1">{{ $edu->institute ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Subject</span>
                                            <div class="info-value mt-1">{{ $edu->subject ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Degree</span>
                                            <div class="info-value mt-1">{{ $edu->degree ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Start Date</span>
                                            <div class="info-value mt-1">
                                                {{ $edu->start_date ? \Carbon\Carbon::parse($edu->start_date)->format('M Y') : '—' }}
                                            </div>
                                        </div>
                                        <div><span class="info-label">End Date</span>
                                            <div class="info-value mt-1">
                                                {{ $edu->end_date ? \Carbon\Carbon::parse($edu->end_date)->format('M Y') : 'Present' }}
                                            </div>
                                        </div>
                                        <div><span class="info-label">Remark</span>
                                            <div class="info-value mt-1">{{ $edu->remark ?? '—' }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 italic text-center py-8">No education history recorded.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- 8. Training & Certifications -->
                        <div x-data="{ open: false }" class="card">
                            <div @click="open = !open"
                                class="card-header bg-gradient-to-r from-indigo-50 to-blue-50 flex justify-between items-center">
                                <h3 class="text-xl font-bold text-indigo-900"><i
                                        class="fas fa-certificate mr-3 text-indigo-600"></i>Training & Certifications
                                </h3>
                                <i x-show="open" class="fas fa-chevron-up text-orange-600"></i>
                                <i x-show="!open" class="fas fa-chevron-down text-orange-600"></i>
                            </div>
                            <div x-show="open" x-transition class="p-8 bg-gray-50">
                                @forelse(Auth::user()->employee?->trainingHistories as $tr)
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 pb-8 border-b last:border-0">
                                        <div><span class="info-label">Institute</span>
                                            <div class="info-value mt-1">{{ $tr->institute ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Subject</span>
                                            <div class="info-value mt-1">{{ $tr->subject ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Start Date</span>
                                            <div class="info-value mt-1">
                                                {{ $tr->start_date ? \Carbon\Carbon::parse($tr->start_date)->format('M Y') : '—' }}
                                            </div>
                                        </div>
                                        <div><span class="info-label">End Date</span>
                                            <div class="info-value mt-1">
                                                {{ $tr->end_date ? \Carbon\Carbon::parse($tr->end_date)->format('M Y') : 'Present' }}
                                            </div>
                                        </div>
                                        <div><span class="info-label">Remark</span>
                                            <div class="info-value mt-1">{{ $tr->remark ?? '—' }}</div>
                                        </div>
                                        <div>
                                            <span class="info-label">Attachment</span>
                                            <div class="info-value mt-1">
                                                @if($tr->attachment)
                                                    <a href="{{ asset('storage/' . $tr->attachment) }}" target="_blank"
                                                        class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-2">
                                                        <i class="fas fa-external-link-alt"></i> View Attachment
                                                    </a>
                                                @else
                                                    —
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 italic text-center py-8">No training records.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- 9. Previous Employment -->
                        <div x-data="{ open: false }" class="card">
                            <div @click="open = !open"
                                class="card-header bg-gradient-to-r from-indigo-50 to-blue-50 flex justify-between items-center">
                                <h3 class="text-xl font-bold text-indigo-900"><i
                                        class="fas fa-briefcase mr-3 text-indigo-600"></i>Employment History</h3>
                                <i x-show="open" class="fas fa-chevron-up text-lime-600"></i>
                                <i x-show="!open" class="fas fa-chevron-down text-lime-600"></i>
                            </div>
                            <div x-show="open" x-transition class="p-8 bg-gray-50">
                                @forelse(Auth::user()->employee?->employmentHistories as $job)
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 pb-8 border-b last:border-0">
                                        <div><span class="info-label">Company Name</span>
                                            <div class="info-value mt-1">{{ $job->company_name ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Start Date</span>
                                            <div class="info-value mt-1">
                                                {{ $job->start_date ? \Carbon\Carbon::parse($job->start_date)->format('M Y') : '—' }}
                                            </div>
                                        </div>
                                        <div><span class="info-label">End Date</span>
                                            <div class="info-value mt-1">
                                                {{ $job->end_date ? \Carbon\Carbon::parse($job->end_date)->format('M Y') : 'Present' }}
                                            </div>
                                        </div>
                                        <div><span class="info-label">Designation</span>
                                            <div class="info-value mt-1">{{ $job->designation ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Supervisor Name</span>
                                            <div class="info-value mt-1">{{ $job->supervisor_name ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Remark</span>
                                            <div class="info-value mt-1">{{ $job->remark ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Rate</span>
                                            <div class="info-value mt-1">{{ $job->rate ?? '—' }}</div>
                                        </div>
                                        <div><span class="info-label">Reason for Leaving</span>
                                            <div class="info-value mt-1">{{ $job->reason_for_leaving ?? '—' }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 italic text-center py-8">No previous employment recorded.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- 10. Achievements & Awards -->
                        <div x-data="{ open: false }" class="card">
                            <div @click="open = !open"
                                class="card-header bg-gradient-to-r from-indigo-50 to-blue-50 flex justify-between items-center cursor-pointer select-none">
                                <h3 class="text-xl font-bold text-indigo-900">
                                    <i class="fas fa-trophy mr-3 text-rose-600"></i>Achievements & Awards
                                </h3>
                                <i x-show="open" class="fas fa-chevron-up text-rose-600"></i>
                                <i x-show="!open" class="fas fa-chevron-down text-rose-600"></i>
                            </div>

                            <div x-show="open" x-transition class="p-8 bg-gray-50">
                                @forelse(Auth::user()->employee?->achievements as $ach)
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 pb-8 border-b last:border-0 last:mb-0 last:pb-0">
                                        <div>
                                            <span class="info-label">Title</span>
                                            <div class="info-value mt-1">{{ $ach->title ?? '—' }}</div>
                                        </div>
                                        <div>
                                            <span class="info-label">Year Awarded</span>
                                            <div class="info-value mt-1">{{ $ach->year_awarded ?? '—' }}</div>
                                        </div>
                                        <div>
                                            <span class="info-label">Country</span>
                                            <div class="info-value mt-1">{{ $ach->country ?? '—' }}</div>
                                        </div>
                                        <div>
                                            <span class="info-label">Program Name</span>
                                            <div class="info-value mt-1">{{ $ach->program_name ?? '—' }}</div>
                                        </div>
                                        <div>
                                            <span class="info-label">Organizer Name</span>
                                            <div class="info-value mt-1">{{ $ach->organizer_name ?? '—' }}</div>
                                        </div>
                                        <div>
                                            <span class="info-label">Remark</span>
                                            <div class="info-value mt-1">{{ $ach->remark ?? '—' }}</div>
                                        </div>
                                        <div>
                                            <span class="info-label">Attachment</span>
                                            <div class="info-value mt-1">
                                                @if($ach->attachment)
                                                    <a href="{{ asset('storage/' . $ach->attachment) }}" target="_blank"
                                                        class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-2">
                                                        <i class="fas fa-external-link-alt"></i> View Attachment
                                                    </a>
                                                @else
                                                    —
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 italic text-center py-8">No achievements recorded yet.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- 11. Documents & Attachments -->
                        <div x-data="{ open: false }" class="card">
                            <div @click="open = !open"
                                class="card-header bg-gradient-to-r from-indigo-50 to-blue-50 flex justify-between items-center cursor-pointer select-none">
                                <h3 class="text-xl font-bold text-indigo-900">
                                    <i class="fas fa-paperclip mr-3 text-violet-600"></i>Documents & Attachments
                                </h3>
                                <i x-show="open" class="fas fa-chevron-up text-violet-600"></i>
                                <i x-show="!open" class="fas fa-chevron-down text-violet-600"></i>
                            </div>

                            <div x-show="open" x-transition class="p-8 bg-gray-50">
                                @forelse(Auth::user()->employee?->attachments as $file)
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 pb-10 border-b last:border-0 last:mb-0 last:pb-0">

                                        <!-- Attachment Name -->
                                        <div>
                                            <span class="info-label">Attachment Name</span>
                                            <div class="info-value mt-1 font-medium text-gray-800">
                                                {{ $file->attachment_name ?? '—' }}
                                            </div>
                                        </div>

                                        <!-- Attachment Preview / View Link -->
                                        <div>
                                            <span class="info-label">Attachment</span>
                                            <div class="info-value mt-1">
                                                @php
                                                    $path = $file->file_path;
                                                    $url = $path ? asset('storage/' . $path) : null;
                                                    $ext = $path ? strtolower(pathinfo($path, PATHINFO_EXTENSION)) : '';
                                                    $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
                                                    $isImage = in_array($ext, $imageExts);
                                                @endphp

                                                @if($url)
                                                    @if($isImage)
                                                        <a href="{{ $url }}" target="_blank"
                                                            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium mt-2 inline-block">
                                                            <i class="fas fa-external-link-alt"></i> View Attachment
                                                        </a>
                                                    @else

                                                        <a href="{{ $url }}" target="_blank"
                                                            class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-2">
                                                            <i class="fas fa-external-link-alt"></i> View Attachment
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-16">
                                        <i class="fas fa-folder-open text-7xl text-gray-200 mb-4"></i>
                                        <p class="text-gray-500 text-lg italic">No documents uploaded yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>

</body>

</html>