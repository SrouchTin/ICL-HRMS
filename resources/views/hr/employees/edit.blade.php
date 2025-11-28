<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee - HR Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .form-section.collapsed {
            max-height: none !important;
            overflow: visible !important;
            height: auto !important;
        }

        .form-section {
            max-height: none !important;
        }

        .form-section {
            transition: all 0.4s ease;
        }

        .form-section.collapsed {
            max-height: 90px;
            overflow: hidden;
        }

        .form-section.expanded {
            max-height: 50000px;
        }

        .section-header {
            cursor: pointer;
            user-select: none;
        }

        .required:after {
            content: " *";
            color: #ef4444;
        }

        .dynamic-block {
            position: relative;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 5px solid #4f46e5;
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .remove-item {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #ef4444;
            color: white;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.4rem;
            transition: all 0.2s;
            z-index: 10;
        }

        .remove-item:hover {
            background: #dc2626;
            transform: scale(1.1);
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">
        @include('layout.hrSidebar')

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-bold text-gray-800">
                        Edit Employee: {{ $employee->employee_code }}
                        <span class="text-lg font-normal text-gray-600">
                            ({{ $employee->personalInfo?->full_name_en ?? 'N/A' }})
                        </span>
                    </h1>
                    <a href="{{ route('hr.employees.index') }}"
                        class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back 
                    </a>
                </div>
            </header>

            <main class="flex-1 overflow-auto p-6 bg-gray-50">
                <div class="max-w-7xl mx-auto">
                    <form action="{{ route('hr.employees.update', $employee) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        
                        <!-- 1. Employee Information -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 expanded mb-6">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center mr-3">
                                        1
                                    </div>
                                    <h2 class="text-xl font-semibold text-gray-800">Employee Information</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <label class="block mb-2 font-medium text-gray-700 required">Employee Code</label>
                                    <input type="text" name="employee_code"
                                        value="{{ old('employee_code', $employee->employee_code) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                                </div>

                                <div>
                                    <label class="block mb-2 font-medium text-gray-700 required">User ID</label>
                                    <input type="number" name="user_id" value="{{ old('user_id', $employee->user_id) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                                </div>

                                <div>
                                    <label class="block mb-2 font-medium text-gray-700 required">Department</label>
                                    <select name="department_id"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                                        <option value="">-- Select Department --</option>
                                        @foreach($departments as $d)
                                            <option value="{{ $d->id }}" {{ old('department_id', $employee->department_id) == $d->id ? 'selected' : '' }}>
                                                {{ $d->department_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block mb-2 font-medium text-gray-700 required">Branch</label>
                                    <select name="branch_id" class="w-full border border-gray-300 rounded-lg px-4 py-3"
                                        required>
                                        <option value="">-- Select Branch --</option>
                                        @foreach($branches as $b)
                                            <option value="{{ $b->id }}" {{ old('branch_id', $employee->branch_id) == $b->id ? 'selected' : '' }}>
                                                {{ $b->branch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block mb-2 font-medium text-gray-700 required">Position</label>
                                    <select name="position_id"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                                        <option value="">-- Select Position --</option>
                                        @foreach($positions as $p)
                                            <option value="{{ $p->id }}" {{ old('position_id', $employee->position_id) == $p->id ? 'selected' : '' }}>
                                                {{ $p->position_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- ADD THIS: Status Dropdown -->
                                <div>
                                    <label class="block mb-2 font-medium text-gray-700 required">Status</label>
                                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-3"
                                        required>
                                        <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block mb-2 font-medium text-gray-700">Profile Image</label>
                                    @if($employee->image)
                                        <div class="mb-3">
                                            <img src="{{ Storage::url($employee->image) }}"
                                                class="h-24 w-24 object-cover rounded-full border-4 border-indigo-100 shadow">
                                        </div>
                                    @endif
                                    <input type="file" name="image" accept="image/*"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 file:mr-4 file:py-2 file:px-6 file:rounded file:bg-indigo-600 file:text-white">
                                    <small class="text-gray-500">Leave blank to keep current</small>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Personal Information -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed mb-6">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center mr-3">
                                        2</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Personal Information</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            @php $pi = $employee->personalInfo; @endphp
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div><label class="block mb-2 font-medium text-gray-700 required">Join
                                        Date</label><input type="date" name="joining_date"
                                        value="{{ old('joining_date', $pi?->joining_date) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3" required></div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">Contract Type</label>
                                    <select name="contract_type"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                                        <option value="UDC" {{ old('contract_type', $pi?->contract_type) == 'UDC' ? 'selected' : '' }}>UDC</option>
                                        <option value="FDC" {{ old('contract_type', $pi?->contract_type) == 'FDC' ? 'selected' : '' }}>FDC</option>
                                    </select>
                                </div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">Employee Type</label>
                                    <select name="employee_type"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                                        <option value="full_time" {{ old('employee_type', $pi?->employee_type) == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                        <option value="part_time" {{ old('employee_type', $pi?->employee_type) == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                        <option value="probation" {{ old('employee_type', $pi?->employee_type) == 'probation' ? 'selected' : '' }}>Probation</option>
                                        <option value="internship" {{ old('employee_type', $pi?->employee_type) == 'internship' ? 'selected' : '' }}>Internship</option>
                                        <option value="contract" {{ old('employee_type', $pi?->employee_type) == 'contract' ? 'selected' : '' }}>Contract</option>
                                    </select>
                                </div>
                                <div><label class="block mb-2 font-medium text-gray-700">Effective Date</label><input
                                        type="date" name="effective_date"
                                        value="{{ old('effective_date', $pi?->effective_date) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3"></div>
                                <div><label class="block mb-2 font-medium text-gray-700">End Date (FDC)</label><input
                                        type="date" name="end_date" value="{{ old('end_date', $pi?->end_date) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3"></div>
                                <div><label class="block mb-2 font-medium text-gray-700">Salutation</label>
                                    <select name="salutation"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3">
                                        <option value="Mr" {{ old('salutation', $pi?->salutation) == 'Mr' ? 'selected' : '' }}>Mr.</option>
                                        <option value="Ms" {{ old('salutation', $pi?->salutation) == 'Ms' ? 'selected' : '' }}>Ms.</option>
                                        <option value="Mrs" {{ old('salutation', $pi?->salutation) == 'Mrs' ? 'selected' : '' }}>Mrs.</option>
                                        <option value="Dr" {{ old('salutation', $pi?->salutation) == 'Dr' ? 'selected' : '' }}>Dr.</option>
                                    </select>
                                </div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">Full Name
                                        KH</label><input type="text" name="full_name_kh"
                                        value="{{ old('full_name_kh', $pi?->full_name_kh) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3" required></div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">Full Name
                                        EN</label><input type="text" name="full_name_en"
                                        value="{{ old('full_name_en', $pi?->full_name_en) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3" required></div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">Gender</label>
                                    <select name="gender" class="w-full border border-gray-300 rounded-lg px-4 py-3"
                                        required>
                                        <option value="male" {{ old('gender', $pi?->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $pi?->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                                <div><label class="block mb-2 font-medium text-gray-700">Date of Birth</label><input
                                        type="date" name="dob" value="{{ old('dob', $pi?->dob) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3"></div>
                                <div><label class="block mb-2 font-medium text-gray-700">Nationality</label><input
                                        type="text" name="nationality"
                                        value="{{ old('nationality', $pi?->nationality ?? 'Cambodian') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3"></div>
                                <div><label class="block mb-2 font-medium text-gray-700">Religion</label><input
                                        type="text" name="religion" value="{{ old('religion', $pi?->religion) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3"></div>
                                <div><label class="block mb-2 font-medium text-gray-700">Blood Group</label>
                                    <select name="blood_group"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3">
                                        <option value="">Select</option>
                                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                            <option value="{{ $bg }}" {{ old('blood_group', $pi?->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div><label class="block mb-2 font-medium text-gray-700">Marital Status</label>
                                    <select name="marital_status"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3">
                                        <option value="single" {{ old('marital_status', $pi?->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ old('marital_status', $pi?->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="divorced" {{ old('marital_status', $pi?->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="widowed" {{ old('marital_status', $pi?->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                    </select>
                                </div>
                                <div class="lg:col-span-2"><label class="block mb-2 font-medium text-gray-700">Bank
                                        Account Number</label><input type="text" name="bank_account_number"
                                        value="{{ old('bank_account_number', $pi?->bank_account_number) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3"></div>
                            </div>
                        </div>

                        <!-- 3. Contact Information -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed mb-6">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center mr-3">
                                        3</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Contact Information</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            @php $c = $employee->contact; @endphp
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div><label class="block mb-2 font-medium text-gray-700 required">Phone
                                        Number</label><input type="text" name="phone_number"
                                        value="{{ old('phone_number', $c?->phone_number) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3" required></div>
                                <div><label class="block mb-2 font-medium text-gray-700">Home Phone</label><input
                                        type="text" name="home_phone" value="{{ old('home_phone', $c?->home_phone) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3"></div>
                                <div><label class="block mb-2 font-medium text-gray-700">Office Phone</label><input
                                        type="text" name="office_phone"
                                        value="{{ old('office_phone', $c?->office_phone) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3"></div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">Email</label><input
                                        type="email" name="email" value="{{ old('email', $c?->email) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3" required></div>
                            </div>
                        </div>

                        <!-- 4. Permanent Address -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed mb-6">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center mr-3">
                                        4</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Permanent Address</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            @php $a = $employee->address; @endphp
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div><label class="block mb-2 font-medium text-gray-700 required">Address
                                        Line</label><input type="text" name="address"
                                        value="{{ old('address', $a?->address) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3" required></div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">City</label><input
                                        type="text" name="city" value="{{ old('city', $a?->city) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3" required></div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">Province</label><input
                                        type="text" name="province" value="{{ old('province', $a?->province) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3" required></div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">Country</label><input
                                        type="text" name="country"
                                        value="{{ old('country', $a?->country ?? 'Cambodia') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3" required></div>
                            </div>
                        </div>

                        <!-- 5. Identification -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed mb-6">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center mr-3">
                                        5</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Identification</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            @php $id = $employee->identification; @endphp
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div><label class="block mb-2 font-medium text-gray-700">Type</label><input type="text"
                                        name="identification_type"
                                        value="{{ old('identification_type', $id?->identification_type) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3"></div>
                                <div><label class="block mb-2 font-medium text-gray-700">Number</label><input
                                        type="text" name="identification_number"
                                        value="{{ old('identification_number', $id?->identification_number) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3"></div>
                                <div><label class="block mb-2 font-medium text-gray-700">Expiration Date</label><input
                                        type="date" name="expiration_date"
                                        value="{{ old('expiration_date', $id?->expiration_date) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3"></div>
                            </div>
                        </div>

                        <!-- 6. Emergency Contacts -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed mb-6">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center mr-3">
                                        6</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Emergency Contacts</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div id="emergency-container">
                                @foreach($employee->emergencyContacts as $index => $ec)
                                    <div class="dynamic-block">
                                        <div class="remove-item">×</div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <input type="hidden" name="emergency_contacts[{{ $index }}][id]"
                                                value="{{ $ec->id }}">
                                            <div><label class="block mb-1 text-sm">Name</label><input type="text"
                                                    name="emergency_contacts[{{ $index }}][contact_person]"
                                                    value="{{ $ec->contact_person }}"
                                                    class="w-full border rounded px-3 py-2"></div>
                                            <div><label class="block mb-1 text-sm">Relationship</label><input type="text"
                                                    name="emergency_contacts[{{ $index }}][relationship]"
                                                    value="{{ $ec->relationship }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                            <div><label class="block mb-1 text-sm">Phone</label><input type="text"
                                                    name="emergency_contacts[{{ $index }}][phone_number]"
                                                    value="{{ $ec->phone_number }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-emergency"
                                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg">+ Add
                                Emergency Contact</button>
                        </div>

                        <!-- 7. Family Members -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed mb-6">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center mr-3">
                                        7</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Family Members</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div id="family-members-container">
                                @foreach($employee->familyMembers as $index => $fm)
                                    <div class="dynamic-block">
                                        <div class="remove-item">×</div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <input type="hidden" name="family_members[{{ $index }}][id]"
                                                value="{{ $fm->id }}">
                                            <div><label class="block mb-1 text-sm">Name</label><input type="text"
                                                    name="family_members[{{ $index }}][name]" value="{{ $fm->name }}"
                                                    class="w-full border rounded px-3 py-2"></div>
                                            <div><label class="block mb-1 text-sm">Relationship</label><input type="text"
                                                    name="family_members[{{ $index }}][relationship]"
                                                    value="{{ $fm->relationship }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                            <div><label class="block mb-1 text-sm">Nationality</label><input type="text"
                                                    name="family_members[{{ $index }}][nationality]"
                                                    value="{{ $fm->nationality ?? '' }}"
                                                    class="w-full border rounded px-3 py-2"></div>
                                            <div><label class="block mb-1 text-sm">DOB</label><input type="date"
                                                    name="family_members[{{ $index }}][dob]" value="{{ $fm->dob }}"
                                                    class="w-full border rounded px-3 py-2"></div>
                                            <div><label class="block mb-1 text-sm">Gender</label>
                                                <select name="family_members[{{ $index }}][gender]"
                                                    class="w-full border rounded px-3 py-2">
                                                    <option value="male" {{ $fm->gender == 'male' ? 'selected' : '' }}>Male
                                                    </option>
                                                    <option value="female" {{ $fm->gender == 'female' ? 'selected' : '' }}>
                                                        Female</option>
                                                </select>
                                            </div>
                                            <div><label class="block mb-1 text-sm">Tax Filing</label>
                                                <select name="family_members[{{ $index }}][tax_filing]"
                                                    class="w-full border rounded px-3 py-2">
                                                    <option value="1" {{ $fm->tax_filing == 1 ? 'selected' : '' }}>Yes
                                                    </option>
                                                    <option value="0" {{ $fm->tax_filing == 0 ? 'selected' : '' }}>No</option>
                                                </select>
                                            </div>
                                            <div><label class="block mb-1 text-sm">Phone</label><input type="text"
                                                    name="family_members[{{ $index }}][phone_number]"
                                                    value="{{ $fm->phone_number }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                            <div class="md:col-span-2"><label
                                                    class="block mb-1 text-sm">Remark</label><input type="text"
                                                    name="family_members[{{ $index }}][remark]" value="{{ $fm->remark }}"
                                                    class="w-full border rounded px-3 py-2"></div>
                                            <div class="md:col-span-2">
                                                <label class="block mb-1 text-sm">Attachment</label>
                                                @if($fm->attachment)<a href="{{ Storage::url($fm->attachment) }}"
                                                    target="_blank" class="text-sm text-blue-600 mr-3">View
                                                Current</a>@endif
                                                <input type="file" name="family_members[{{ $index }}][attachment]"
                                                    class="w-full mt-1">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-family-member"
                                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg">+ Add
                                Family Member</button>
                        </div>

                        <!-- 8. Education History -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed mb-6">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center mr-3">
                                        8</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Education History</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div id="education-container">
                                @foreach($employee->educationHistories as $index => $edu)
                                    <div class="dynamic-block">
                                        <div class="remove-item">×</div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <input type="hidden" name="education_history[{{ $index }}][id]"
                                                value="{{ $edu->id }}">
                                            <div><label class="block mb-1 text-sm">Institute</label><input type="text"
                                                    name="education_history[{{ $index }}][institute]"
                                                    value="{{ $edu->institute }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                            <div><label class="block mb-1 text-sm">Degree</label><input type="text"
                                                    name="education_history[{{ $index }}][degree]"
                                                    value="{{ $edu->degree }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                            <div><label class="block mb-1 text-sm">Major/Subject</label><input type="text"
                                                    name="education_history[{{ $index }}][subject]"
                                                    value="{{ $edu->subject }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                            <div><label class="block mb-1 text-sm">Start Date</label><input type="date"
                                                    name="education_history[{{ $index }}][start_date]"
                                                    value="{{ $edu->start_date }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                            <div><label class="block mb-1 text-sm">End Date</label><input type="date"
                                                    name="education_history[{{ $index }}][end_date]"
                                                    value="{{ $edu->end_date }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                            <div class="md:col-span-2"><label
                                                    class="block mb-1 text-sm">Remark</label><textarea
                                                    name="education_history[{{ $index }}][remark]" rows="2"
                                                    class="w-full border rounded px-3 py-2">{{ $edu->remark }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-education"
                                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg">+ Add
                                Education</button>
                        </div>

                        <!-- 9. Training History -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed mb-6">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center mr-3">
                                        9</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Training History</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div id="training-container">
                                @foreach($employee->trainingHistories as $index => $tr)
                                    <div class="dynamic-block">
                                        <div class="remove-item">×</div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <input type="hidden" name="training_history[{{ $index }}][id]"
                                                value="{{ $tr->id }}">
                                            <div><label class="block mb-1 text-sm">Institute</label><input type="text"
                                                    name="training_history[{{ $index }}][institute]"
                                                    value="{{ $tr->institute }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                            <div><label class="block mb-1 text-sm">Subject</label><input type="text"
                                                    name="training_history[{{ $index }}][subject]"
                                                    value="{{ $tr->subject }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                            <div><label class="block mb-1 text-sm">Start Date</label><input type="date"
                                                    name="training_history[{{ $index }}][start_date]"
                                                    value="{{ $tr->start_date }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                            <div><label class="block mb-1 text-sm">End Date</label><input type="date"
                                                    name="training_history[{{ $index }}][end_date]"
                                                    value="{{ $tr->end_date }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                            <div class="md:col-span-2"><label
                                                    class="block mb-1 text-sm">Remark</label><textarea
                                                    name="training_history[{{ $index }}][remark]" rows="2"
                                                    class="w-full border rounded px-3 py-2">{{ $tr->remark }}</textarea>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block mb-1 text-sm">Attachment</label>
                                                @if($tr->attachment)<a href="{{ Storage::url($tr->attachment) }}"
                                                target="_blank" class="text-sm text-blue-600 mr-3">View File</a>@endif
                                                <input type="file" name="training_history[{{ $index }}][attachment]"
                                                    class="w-full mt-1">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-training"
                                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg">+ Add
                                Training</button>
                        </div>

                        <!-- 10. Previous Employment -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed mb-6">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center mr-3">
                                        10</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Previous Employment</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div id="employment-container">
                                @foreach($employee->employmentHistories as $index => $emp)
                                    <div class="dynamic-block">
                                        <div class="remove-item">×</div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <input type="hidden" name="employment_history[{{ $index }}][id]"
                                                value="{{ $emp->id }}">
                                            <div><label class="block mb-1 text-sm">Company</label><input type="text"
                                                    name="employment_history[{{ $index }}][company_name]"
                                                    value="{{ $emp->company_name }}"
                                                    class="w-full border rounded px-3 py-2"></div>
                                            <div><label class="block mb-1 text-sm">Position</label><input type="text"
                                                    name="employment_history[{{ $index }}][designation]"
                                                    value="{{ $emp->designation }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                            <div><label class="block mb-1 text-sm">Start Date</label><input type="date"
                                                    name="employment_history[{{ $index }}][start_date]"
                                                    value="{{ $emp->start_date }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                            <div><label class="block mb-1 text-sm">End Date</label><input type="date"
                                                    name="employment_history[{{ $index }}][end_date]"
                                                    value="{{ $emp->end_date }}" class="w-full border rounded px-3 py-2">
                                            </div>
                                            <div><label class="block mb-1 text-sm">Supervisor</label><input type="text"
                                                    name="employment_history[{{ $index }}][supervisor_name]"
                                                    value="{{ $emp->supervisor_name }}"
                                                    class="w-full border rounded px-3 py-2"></div>
                                            <div><label class="block mb-1 text-sm">Rate</label><input type="text"
                                                    name="employment_history[{{ $index }}][rate]" value="{{ $emp->rate }}"
                                                    class="w-full border rounded px-3 py-2"></div>
                                            <div class="md:col-span-2"><label
                                                    class="block mb-1 text-sm">Remark</label><textarea
                                                    name="employment_history[{{ $index }}][remark]" rows="2"
                                                    class="w-full border rounded px-3 py-2">{{ $emp->remark }}</textarea>
                                            </div>
                                            <div class="md:col-span-2"><label class="block mb-1 text-sm">Reason for
                                                    Leaving</label><textarea
                                                    name="employment_history[{{ $index }}][reason_for_leaving]" rows="3"
                                                    class="w-full border rounded px-3 py-2">{{ $emp->reason_for_leaving }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-employment"
                                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg">+ Add
                                Previous Job</button>
                        </div>

                        <!-- 11. Achievements & Awards -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed mb-6">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center mr-3">
                                        11</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Achievements & Awards</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div id="achievement-container">
                                @foreach($employee->achievements as $index => $ach)
                                    <div class="dynamic-block">
                                        <div class="remove-item">×</div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <input type="hidden" name="achievements[{{ $index }}][id]"
                                                value="{{ $ach->id }}">
                                            <div><label class="block mb-1 text-sm">Title</label><input type="text"
                                                    name="achievements[{{ $index }}][title]" value="{{ $ach->title }}"
                                                    class="w-full border rounded px-3 py-2"></div>
                                            <div><label class="block mb-1 text-sm">Year</label><input type="text"
                                                    name="achievements[{{ $index }}][year_awarded]"
                                                    value="{{ $ach->year_awarded }}"
                                                    class="w-full border rounded px-3 py-2"></div>
                                            <div><label class="block mb-1 text-sm">Country</label><input type="text"
                                                    name="achievements[{{ $index }}][country]" value="{{ $ach->country }}"
                                                    class="w-full border rounded px-3 py-2"></div>
                                            <div><label class="block mb-1 text-sm">Program</label><input type="text"
                                                    name="achievements[{{ $index }}][program_name]"
                                                    value="{{ $ach->program_name }}"
                                                    class="w-full border rounded px-3 py-2"></div>
                                            <div><label class="block mb-1 text-sm">Organizer</label><input type="text"
                                                    name="achievements[{{ $index }}][organizer_name]"
                                                    value="{{ $ach->organizer_name }}"
                                                    class="w-full border rounded px-3 py-2"></div>
                                            <div class="md:col-span-2"><label
                                                    class="block mb-1 text-sm">Remark</label><textarea
                                                    name="achievements[{{ $index }}][remark]" rows="2"
                                                    class="w-full border rounded px-3 py-2">{{ $ach->remark }}</textarea>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block mb-1 text-sm">Certificate</label>
                                                @if($ach->attachment)<a href="{{ Storage::url($ach->attachment) }}"
                                                target="_blank" class="text-sm text-blue-600 mr-3">View</a>@endif
                                                <input type="file" name="achievements[{{ $index }}][attachment]"
                                                    class="w-full mt-1">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-achievement"
                                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg">+ Add
                                Achievement</button>
                        </div>

                        <!-- 12. Other Documents -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed mb-6">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center mr-3">
                                        12</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Other Documents</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div id="attachment-container">
                                @foreach($employee->attachments as $index => $att)
                                    <div class="dynamic-block">
                                        <div class="remove-item">×</div>
                                        <input type="hidden" name="attachments[{{ $index }}][id]" value="{{ $att->id }}">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div><label class="block mb-1 text-sm">Document Name</label><input type="text"
                                                    name="attachments[{{ $index }}][name]"
                                                    value="{{ $att->attachment_name }}"
                                                    class="w-full border rounded px-3 py-2"></div>
                                            <div>
                                                <label class="block mb-1 text-sm">File</label>
                                                @if($att->file_path)<a href="{{ Storage::url($att->file_path) }}"
                                                    target="_blank"
                                                class="text-sm text-blue-600 mr-3">{{ $att->attachment_name }}</a><br>@endif
                                                <input type="file" name="attachments[{{ $index }}][file]"
                                                    class="w-full mt-1">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-attachment"
                                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg">+ Add
                                Document</button>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4 mt-12 pb-20">
                            <a href="{{ route('hr.employees.index') }}"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-8 py-3 rounded-lg shadow transition">Cancel</a>
                            <button type="submit" id="submit"
                                class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition flex items-center">
                                <i class="fas fa-save mr-3"></i><span x-data="{ loading: false }"
                                    @click="loading = true" x-text="loading ? 'Saving...' : 'UPDATE EMPLOYEE'">
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <!-- 100% WORKING JAVASCRIPT — FIXED FOREVER -->
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            // 1. បង្ខំបើក section ទាំងអស់នៅពេល load ទំព័រ (សំខាន់បំផុត!)
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.remove('collapsed');
                section.classList.add('expanded');
            });

            // 2. Collapsible sections (click header to collapse/expand)
            document.querySelectorAll('.section-header').forEach(header => {
                header.addEventListener('click', () => {
                    const section = header.closest('.form-section');
                    const icon = header.querySelector('i');
                    section.classList.toggle('collapsed');
                    section.classList.toggle('expanded');
                    icon.classList.toggle('fa-chevron-down');
                    icon.classList.toggle('fa-chevron-up');
                });
            });

            // 3. Remove dynamic block
            document.addEventListener('click', e => {
                if (e.target.closest('.remove-item')) {
                    e.target.closest('.dynamic-block').remove();
                }
            });

            // 4. Counters for dynamic fields
            const counters = {
                emergency: {{ $employee->emergencyContacts->count() ?? 0 }},
                family: {{ $employee->familyMembers->count() ?? 0 }},
                education: {{ $employee->educationHistories->count() ?? 0 }},
                training: {{ $employee->trainingHistories->count() ?? 0 }},
                employment: {{ $employee->employmentHistories->count() ?? 0 }},
                achievement: {{ $employee->achievements->count() ?? 0 }},
                attachment: {{ $employee->attachments->count() ?? 0 }}
};

            const nextIndex = type => counters[type]++;

            // 5. Templates for dynamic blocks
            const templates = {
                emergency: () => {
                    const i = nextIndex('emergency');
                    return `<div class="dynamic-block">
                        <div class="remove-item">×</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="hidden" name="emergency_contacts[${i}][id]" value="">
                            <div><label class="block mb-1 text-sm">Name</label><input type="text" name="emergency_contacts[${i}][contact_person]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Relationship</label><input type="text" name="emergency_contacts[${i}][relationship]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Phone</label><input type="text" name="emergency_contacts[${i}][phone_number]" class="w-full border rounded px-3 py-2"></div>
                        </div>
                    </div>`;
                },
                family: () => {
                    const i = nextIndex('family');
                    return `<div class="dynamic-block">
                        <div class="remove-item">×</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="hidden" name="family_members[${i}][id]" value="">
                            <div><label class="block mb-1 text-sm">Name</label><input type="text" name="family_members[${i}][name]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Relationship</label><input type="text" name="family_members[${i}][relationship]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">DOB</label><input type="date" name="family_members[${i}][dob]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Gender</label>
                                <select name="family_members[${i}][gender]" class="w-full border rounded px-3 py-2">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div><label class="block mb-1 text-sm">Tax Filing</label>
                                <select name="family_members[${i}][tax_filing]" class="w-full border rounded px-3 py-2">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div><label class="block mb-1 text-sm">Phone</label><input type="text" name="family_members[${i}][phone_number]" class="w-full border rounded px-3 py-2"></div>
                            <div class="md:col-span-2"><label class="block mb-1 text-sm">Remark</label><input type="text" name="family_members[${i}][remark]" class="w-full border rounded px-3 py-2"></div>
                            <div class="md:col-span-2"><label class="block mb-1 text-sm">Attachment</label><input type="file" name="family_members[${i}][attachment]" class="w-full mt-1"></div>
                        </div>
                    </div>`;
                },
                education: () => {
                    const i = nextIndex('education');
                    return `<div class="dynamic-block">
                        <div class="remove-item">×</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="hidden" name="education_history[${i}][id]" value="">
                            <div><label class="block mb-1 text-sm">Institute</label><input type="text" name="education_history[${i}][institute]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Degree</label><input type="text" name="education_history[${i}][degree]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Major/Subject</label><input type="text" name="education_history[${i}][subject]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Start Date</label><input type="date" name="education_history[${i}][start_date]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">End Date</label><input type="date" name="education_history[${i}][end_date]" class="w-full border rounded px-3 py-2"></div>
                            <div class="md:col-span-2"><label class="block mb-1 text-sm">Remark</label><textarea name="education_history[${i}][remark]" rows="2" class="w-full border rounded px-3 py-2"></textarea></div>
                        </div>
                    </div>`;
                },
                training: () => {
                    const i = nextIndex('training');
                    return `<div class="dynamic-block">
                        <div class="remove-item">×</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="hidden" name="training_history[${i}][id]" value="">
                            <div><label class="block mb-1 text-sm">Institute</label><input type="text" name="training_history[${i}][institute]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Subject</label><input type="text" name="training_history[${i}][subject]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Start Date</label><input type="date" name="training_history[${i}][start_date]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">End Date</label><input type="date" name="training_history[${i}][end_date]" class="w-full border rounded px-3 py-2"></div>
                            <div class="md:col-span-2"><label class="block mb-1 text-sm">Remark</label><textarea name="training_history[${i}][remark]" rows="2" class="w-full border rounded px-3 py-2"></textarea></div>
                            <div class="md:col-span-2"><label class="block mb-1 text-sm">Attachment</label><input type="file" name="training_history[${i}][attachment]" class="w-full mt-1"></div>
                        </div>
                    </div>`;
                },
                employment: () => {
                    const i = nextIndex('employment');
                    return `<div class="dynamic-block">
                        <div class="remove-item">×</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="hidden" name="employment_history[${i}][id]" value="">
                            <div><label class="block mb-1 text-sm">Company</label><input type="text" name="employment_history[${i}][company_name]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Position</label><input type="text" name="employment_history[${i}][designation]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Start Date</label><input type="date" name="employment_history[${i}][start_date]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">End Date</label><input type="date" name="employment_history[${i}][end_date]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Supervisor</label><input type="text" name="employment_history[${i}][supervisor_name]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Rate</label><input type="text" name="employment_history[${i}][rate]" class="w-full border rounded px-3 py-2"></div>
                            <div class="md:col-span-2"><label class="block mb-1 text-sm">Remark</label><textarea name="employment_history[${i}][remark]" rows="2" class="w-full border rounded px-3 py-2"></textarea></div>
                            <div class="md:col-span-2"><label class="block mb-1 text-sm">Reason for Leaving</label><textarea name="employment_history[${i}][reason_for_leaving]" rows="3" class="w-full border rounded px-3 py-2"></textarea></div>
                        </div>
                    </div>`;
                },
                achievement: () => {
                    const i = nextIndex('achievement');
                    return `<div class="dynamic-block">
                        <div class="remove-item">×</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="hidden" name="achievements[${i}][id]" value="">
                            <div><label class="block mb-1 text-sm">Title</label><input type="text" name="achievements[${i}][title]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Year</label><input type="text" name="achievements[${i}][year_awarded]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Country</label><input type="text" name="achievements[${i}][country]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Program</label><input type="text" name="achievements[${i}][program_name]" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">Organizer</label><input type="text" name="achievements[${i}][organizer_name]" class="w-full border rounded px-3 py-2"></div>
                            <div class="md:col-span-2"><label class="block mb-1 text-sm">Remark</label><textarea name="achievements[${i}][remark]" rows="2" class="w-full border rounded px-3 py-2"></textarea></div>
                            <div class="md:col-span-2"><label class="block mb-1 text-sm">Certificate</label><input type="file" name="achievements[${i}][attachment]" class="w-full mt-1"></div>
                        </div>
                    </div>`;
                },
                attachment: () => {
                    const i = nextIndex('attachment');
                    return `<div class="dynamic-block">
                        <div class="remove-item">×</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="hidden" name="attachments[${i}][id]" value="">
                            <div><label class="block mb-1 text-sm">Document Name</label><input type="text" name="attachments[${i}][name]" placeholder="e.g. Resume, Contract" class="w-full border rounded px-3 py-2"></div>
                            <div><label class="block mb-1 text-sm">File</label><input type="file" name="attachments[${i}][file]" class="w-full mt-1"></div>
                        </div>
                    </div>`;
                }
            };

            // 6. Add new items
            document.getElementById('add-emergency')?.addEventListener('click', () => {
                document.getElementById('emergency-container').insertAdjacentHTML('beforeend', templates.emergency());
            });
            document.getElementById('add-family-member')?.addEventListener('click', () => {
                document.getElementById('family-members-container').insertAdjacentHTML('beforeend', templates.family());
            });
            document.getElementById('add-education')?.addEventListener('click', () => {
                document.getElementById('education-container').insertAdjacentHTML('beforeend', templates.education());
            });
            document.getElementById('add-training')?.addEventListener('click', () => {
                document.getElementById('training-container').insertAdjacentHTML('beforeend', templates.training());
            });
            document.getElementById('add-employment')?.addEventListener('click', () => {
                document.getElementById('employment-container').insertAdjacentHTML('beforeend', templates.employment());
            });
            document.getElementById('add-achievement')?.addEventListener('click', () => {
                document.getElementById('achievement-container').insertAdjacentHTML('beforeend', templates.achievement());
            });
            document.getElementById('add-attachment')?.addEventListener('click', () => {
                document.getElementById('attachment-container').insertAdjacentHTML('beforeend', templates.attachment());
            });

        });
    </script>
</body>

</html>