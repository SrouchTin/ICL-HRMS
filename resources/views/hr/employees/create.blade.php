{{-- resources/views/hr/employees/create.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Employee - HR System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .form-section {
            transition: all 0.4s ease;
        }

        .form-section.collapsed {
            max-height: 80px;
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
        }

        .remove-item {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #ef4444;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.2s;
        }

        .remove-item:hover {
            background: #dc2626;
            transform: scale(1.1);
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div x-data="{ sidebarOpen: false }" class="flex h-screen">
        {{-- Sidebar --}}
            @include('layout.hrSidebar')
        {{-- Sidebar --}}
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-bold text-gray-800">Add New Employee</h1>
                    <a href="{{ route('hr.employees.index') }}"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg px-6 py-2.5 shadow transition">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
            </header>

            <main class="flex-1 overflow-auto p-6 bg-gray-50">
                <div class="max-w-7xl mx-auto">
                    <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-8">
                        @csrf
                        <input type="hidden" name="status" value="active">

                        <!-- 1. Employee Information -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 expanded">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold mr-3 flex items-center justify-center">
                                        1</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Employee Information</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div><label class="block mb-2 font-medium text-gray-700 required">Employee Code</label>
                                    <input type="text" name="employee_code" value="{{ old('employee_code') }}"
                                        class="w-full border rounded-lg px-4 py-3" required>
                                </div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">User ID</label>
                                    <input type="number" name="user_id" value="{{ old('user_id') }}"
                                        class="w-full border rounded-lg px-4 py-3" required>
                                </div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">Department</label>
                                    <select name="department_id" class="w-full border rounded-lg px-4 py-3" required>
                                        <option value="">-- Select --</option>
                                        @foreach(\App\Models\Department::where('status', 'active')->get() as $dept)
                                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->department_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">Branch</label>
                                    <select name="branch_id" class="w-full border rounded-lg px-4 py-3" required>
                                        <option value="">-- Select --</option>
                                        @foreach(\App\Models\Branch::where('status', 'active')->get() as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">Position</label>
                                    <select name="position_id" class="w-full border rounded-lg px-4 py-3" required>
                                        <option value="">-- Select --</option>
                                        @foreach(\App\Models\Position::where('status', 'active')->get() as $pos)
                                            <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>{{ $pos->position_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div><label class="block mb-2 font-medium text-gray-700">Profile Image</label>
                                    <input type="file" name="image" accept="image/*"
                                        class="w-full border rounded-lg px-4 py-3">
                                </div>
                            </div>
                        </div>

                        <!-- 2. Personal Information -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold mr-3 flex items-center justify-center">
                                        2</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Personal Information</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div><label>Salutation</label>
                                    <select name="salutation" class="w-full border rounded-lg px-4 py-3">
                                        <option value="">Select</option>
                                        <option value="Mr">Mr.</option>
                                        <option value="Ms">Ms.</option>
                                        <option value="Mrs">Mrs.</option>
                                    </select>
                                </div>
                                <div><label class="required">Full Name KH</label><input type="text" name="full_name_kh"
                                        value="{{ old('full_name_kh') }}" class="w-full border rounded-lg px-4 py-3"
                                        required></div>
                                <div><label class="required">Full Name EN</label><input type="text" name="full_name_en"
                                        value="{{ old('full_name_en') }}" class="w-full border rounded-lg px-4 py-3"
                                        required></div>
                                <div><label>Date of Birth</label><input type="date" name="dob" value="{{ old('dob') }}"
                                        class="w-full border rounded-lg px-4 py-3"></div>
                                <div><label class="required">Gender</label>
                                    <select name="gender" class="w-full border rounded-lg px-4 py-3" required>
                                        <option value="">Select</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div><label>Nationality</label><input type="text" name="nationality"
                                        value="{{ old('nationality', 'Cambodian') }}"
                                        class="w-full border rounded-lg px-4 py-3"></div>
                                <div><label>Marital Status</label>
                                    <select name="marital_status" class="w-full border rounded-lg px-4 py-3">
                                        <option value="">Select</option>
                                        <option value="single">Single</option>
                                        <option value="married">Married</option>
                                        <option value="divorced">Divorced</option>
                                        <option value="widowed">Widowed</option>
                                    </select>
                                </div>
                                <div><label>Religion</label><input type="text" name="religion"
                                        value="{{ old('religion') }}" class="w-full border rounded-lg px-4 py-3"></div>
                                <div><label>Blood Group</label>
                                    <select name="blood_group" class="w-full border rounded-lg px-4 py-3">
                                        <option value="">Select</option>
                                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                            <option value="{{ $bg }}">{{ $bg }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div><label>Bank Account</label><input type="text" name="bank_account_number"
                                        value="{{ old('bank_account_number') }}"
                                        class="w-full border rounded-lg px-4 py-3"></div>
                                <div><label class="required">Contract Type</label>
                                    <select name="contract_type" class="w-full border rounded-lg px-4 py-3" required>
                                        <option value="">Select</option>
                                        <option value="UDC">UDC</option>
                                        <option value="FDC">FDC</option>
                                    </select>
                                </div>
                                <div><label class="required">Employee Type</label>
                                    <select name="employee_type" class="w-full border rounded-lg px-4 py-3" required>
                                        <option value="">Select</option>
                                        <option value="full_time">Full Time</option>
                                        <option value="part_time">Part Time</option>
                                        <option value="probation">Probation</option>
                                        <option value="internship">Internship</option>
                                        <option value="contract">Contract</option>
                                    </select>
                                </div>
                                <div><label class="required">Joining Date</label><input type="date" name="joining_date"
                                        value="{{ old('joining_date') }}" class="w-full border rounded-lg px-4 py-3"
                                        required></div>
                                <div><label>End Date</label><input type="date" name="end_date"
                                        value="{{ old('end_date') }}" class="w-full border rounded-lg px-4 py-3"></div>
                                <div><label class="required">Effective Date</label><input type="date"
                                        name="effective_date" value="{{ old('effective_date') }}"
                                        class="w-full border rounded-lg px-4 py-3" required></div>
                            </div>
                        </div>

                        <!-- 3. Identification -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold mr-3 flex items-center justify-center">
                                        3</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Identification</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div><label>Identification Type</label><input type="text" name="identification_type"
                                        value="{{ old('identification_type') }}"
                                        class="w-full border rounded-lg px-4 py-3"></div>
                                <div><label>Identification Number</label><input type="text" name="identification_number"
                                        value="{{ old('identification_number') }}"
                                        class="w-full border rounded-lg px-4 py-3"></div>
                                <div><label>Expiration Date</label><input type="date" name="expiration_date"
                                        value="{{ old('expiration_date') }}" class="w-full border rounded-lg px-4 py-3">
                                </div>
                            </div>
                        </div>

                        <!-- 4. Permanent Address -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold mr-3 flex items-center justify-center">
                                        4</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Permanent Address</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div><label>City</label><input type="text" name="city" value="{{ old('city') }}"
                                        class="w-full border rounded-lg px-4 py-3"></div>
                                <div><label>Province</label><input type="text" name="province"
                                        value="{{ old('province') }}" class="w-full border rounded-lg px-4 py-3"></div>
                                <div><label>Country</label><input type="text" name="country"
                                        value="{{ old('country', 'Cambodia') }}"
                                        class="w-full border rounded-lg px-4 py-3"></div>
                                <div class="md:col-span-2"><label>Full Address</label><textarea name="address" rows="3"
                                        class="w-full border rounded-lg px-4 py-3">{{ old('address') }}</textarea></div>
                            </div>
                        </div>

                        <!-- 5. Contact Information -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold mr-3 flex items-center justify-center">
                                        5</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Contact Information</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div><label>Phone Number</label><input type="tel" name="phone_number"
                                        value="{{ old('phone_number') }}" class="w-full border rounded-lg px-4 py-3">
                                </div>
                                <div><label>Home Phone</label><input type="tel" name="home_phone"
                                        value="{{ old('home_phone') }}" class="w-full border rounded-lg px-4 py-3">
                                </div>
                                <div><label>Office Phone</label><input type="tel" name="office_phone"
                                        value="{{ old('office_phone') }}" class="w-full border rounded-lg px-4 py-3">
                                </div>
                                <div><label>Email</label><input type="email" name="email" value="{{ old('email') }}"
                                        class="w-full border rounded-lg px-4 py-3"></div>
                            </div>
                        </div>

                        <!-- 6. Emergency Contact -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold mr-3 flex items-center justify-center">
                                        6</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Emergency Contact</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div id="emergency-contact-container"></div>
                            <button type="button" id="add-emergency-contact"
                                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">+ Add
                                Emergency Contact</button>
                        </div>

                        <!-- 7. Family Members -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold mr-3 flex items-center justify-center">
                                        7</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Family Members</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div id="family-members-container"></div>
                            <button type="button" id="add-family-member"
                                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">+ Add
                                Family Member</button>
                        </div>

                        <!-- 8. Education History -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold mr-3 flex items-center justify-center">
                                        8</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Education History</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div id="education-container"></div>
                            <button type="button" id="add-education"
                                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg">+ Add
                                Education</button>
                        </div>

                        <!-- 9. Training History -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold mr-3 flex items-center justify-center">
                                        9</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Training History</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div id="training-container"></div>
                            <button type="button" id="add-training"
                                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg">+ Add
                                Training</button>
                        </div>

                        <!-- 10. Employment History -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold mr-3 flex items-center justify-center">
                                        10</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Employment History</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div id="employment-container"></div>
                            <button type="button" id="add-employment"
                                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg">+ Add
                                Employment</button>
                        </div>

                        <!-- 11. Achievements -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold mr-3 flex items-center justify-center">
                                        11</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Achievements</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div id="achievement-container"></div>
                            <button type="button" id="add-achievement"
                                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg">+ Add
                                Achievement</button>
                        </div>

                        <!-- 12. Other Documents -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold mr-3 flex items-center justify-center">
                                        12</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Attachment</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div id="attachment-container"></div>
                            <button type="button" id="add-attachment"
                                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg">+ Add
                                Attachment</button>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end mt-12 pb-10">
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-lg font-bold text-lg shadow-lg transition">
                                <i class="fas fa-save mr-3"></i> SAVE EMPLOYEE
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Coll5apsible sections (ដើម)
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

            // Remove dynamic block - ប្រើ delegation (ដើរគ្រប់ block)
            document.addEventListener('click', e => {
                if (e.target.closest('.remove-item')) {
                    e.target.closest('.dynamic-block').remove();
                }
            });

            // Counters - ចាប់ពី old() ដើម្បីកុំឲ្យបាត់ fields ពេល validation fail
            const counters = {
                emergency:   {{ old('emergency_contacts') ? count(old('emergency_contacts')) : 0 }},
                family:      {{ old('family_members') ? count(old('family_members')) : 0 }},
                education:   {{ old('education_history') ? count(old('education_history')) : 0 }},
                training:    {{ old('training_history') ? count(old('training_history')) : 0 }},
                employment:  {{ old('employment_history') ? count(old('employment_history')) : 0 }},
                achievement: {{ old('achievements') ? count(old('achievements')) : 0 }},
                attachment:  {{ old('attachments') ? count(old('attachments')) : 0 }}
    };

            // 1. Emergency Contact (ដូចដើម)
            document.getElementById('add-emergency-contact')?.addEventListener('click', () => {
                const i = counters.emergency++;
                document.getElementById('emergency-contact-container').insertAdjacentHTML('beforeend', `
            <div class="dynamic-block">
                <div class="remove-item">×</div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Contact Person</label>
                        <input type="text" name="emergency_contacts[${i}][contact_person]" class="w-full border rounded-lg px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Relationship</label>
                        <input type="text" name="emergency_contacts[${i}][relationship]" class="w-full border rounded-lg px-4 py-3">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Phone</label>
                        <input type="tel" name="emergency_contacts[${i}][phone_number]" class="w-full border rounded-lg px-4 py-3">
                    </div>
                </div>
            </div>
        `);
            });

            // 2. Family Members (ដូចដើម)
            document.getElementById('add-family-member')?.addEventListener('click', () => {
                const i = counters.family++;
                document.getElementById('family-members-container').insertAdjacentHTML('beforeend', `
            <div class="dynamic-block">
                <div class="remove-item">×</div>
                <h3 class="font-semibold text-indigo-700 mb-4">Family Member #${i + 1}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label>Name</label><input type="text" name="family_members[${i}][name]" class="w-full border rounded px-3 py-2"></div>
                    <div><label>Relationship</label><input type="text" name="family_members[${i}][relationship]" class="w-full border rounded px-3 py-2"></div>
                    <div><label>Nationality</label><input type="text" name="family_members[${i}][nationality]" class="w-full border rounded px-3 py-2"></div>
                    <div><label>Date of Birth</label><input type="date" name="family_members[${i}][dob]" class="w-full border rounded px-3 py-2"></div>
                    <div><label>Gender</label>
                        <select name="family_members[${i}][gender]" class="w-full border rounded px-3 py-2">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div><label>Tax Filing</label>
                        <select name="family_members[${i}][tax_filing]" class="w-full border rounded px-3 py-2">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div><label>Phone</label><input type="tel" name="family_members[${i}][phone_number]" class="w-full border rounded px-3 py-2"></div>
                    <div class="md:col-span-2"><label>Remark</label><input type="text" name="family_members[${i}][remark]" class="w-full border rounded px-3 py-2"></div>
                    <div class="md:col-span-2"><label>Attachment</label><input type="file" name="family_members[${i}][attachment]" class="w-full"></div>
                </div>
            </div>
        `);
            });

            // 3. Education (ដូចដើម)
            document.getElementById('add-education')?.addEventListener('click', () => {
                const i = counters.education++;
                document.getElementById('education-container').insertAdjacentHTML('beforeend', `
            <div class="dynamic-block">
                <div class="remove-item">×</div>
                <h3 class="font-semibold text-indigo-700 mb-4">Education #${i + 1}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label>Institute</label><input type="text" name="education_history[${i}][institute]" class="w-full border rounded px-3 py-2"></div>
                    <div><label>Degree</label><input type="text" name="education_history[${i}][degree]" class="w-full border rounded px-3 py-2"></div>
                    <div><label>Subject</label><input type="text" name="education_history[${i}][subject]" class="w-full border rounded px-3 py-2"></div>
                    <div><label>Start Date</label><input type="date" name="education_history[${i}][start_date]" class="w-full border rounded px-3 py-2"></div>
                    <div><label>End Date</label><input type="date" name="education_history[${i}][end_date]" class="w-full border rounded px-3 py-2"></div>
                    <div class="md:col-span-2"><label>Remark</label><textarea name="education_history[${i}][remark]" rows="2" class="w-full border rounded px-3 py-2"></textarea></div>
                </div>
            </div>
        `);
            });

            // 4. Training (ដូចដើម)
            document.getElementById('add-training')?.addEventListener('click', () => {
                const i = counters.training++;
                document.getElementById('training-container').insertAdjacentHTML('beforeend', `
            <div class="dynamic-block">
                <div class="remove-item">×</div>
                <h3 class="font-semibold text-indigo-700 mb-4">Training #${i + 1}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label>Institute</label><input type="text" name="training_history[${i}][institute]" class="w-full border rounded px-3 py-2"></div>
                    <div><label>Subject</label><input type="text" name="training_history[${i}][subject]" class="w-full border rounded px-3 py-2"></div>
                    <div><label>Start Date</label><input type="date" name="training_history[${i}][start_date]" class="w-full border rounded px-3 py-2"></div>
                    <div><label>End Date</label><input type="date" name="training_history[${i}][end_date]" class="w-full border rounded px-3 py-2"></div>
                    <div class="md:col-span-2"><label>Remark</label><textarea name="training_history[${i}][remark]" rows="2" class="w-full border rounded px-3 py-2"></textarea></div>
                    <div class="md:col-span-2"><label>Attachment</label><input type="file" name="training_history[${i}][attachment]" class="w-full"></div>
                </div>
            </div>
        `);
            });

            // 5. Employment History (ដូចដើម)
            document.getElementById('add-employment')?.addEventListener('click', () => {
                const i = counters.employment++;
                document.getElementById('employment-container').insertAdjacentHTML('beforeend', `
            <div class="dynamic-block">
                <div class="remove-item">×</div>
                <h3 class="font-semibold text-indigo-700 mb-4">Employment #${i + 1}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label>Company Name</label><input type="text" name="employment_history[${i}][company_name]" class="w-full border rounded px-3 py-2" required></div>
                    <div><label>Designation</label><input type="text" name="employment_history[${i}][designation]" class="w-full border rounded px-3 py-2"></div>
                    <div><label>Start Date</label><input type="date" name="employment_history[${i}][start_date]" class="w-full border rounded px-3 py-2"></div>
                    <div><label>End Date</label><input type="date" name="employment_history[${i}][end_date]" class="w-full border rounded px-3 py-2"></div>
                    <div><label>Supervisor Name</label><input type="text" name="employment_history[${i}][supervisor_name]" class="w-full border rounded px-3 py-2"></div>
                    <div><label>Rate / Salary</label><input type="text" name="employment_history[${i}][rate]" placeholder="e.g. $800" class="w-full border rounded px-3 py-2"></div>
                    <div class="md:col-span-2"><label>Remark</label><textarea name="employment_history[${i}][remark]" rows="2" class="w-full border rounded px-3 py-2"></textarea></div>
                    <div class="md:col-span-2"><label>Reason for Leaving</label><textarea name="employment_history[${i}][reason_for_leaving]" rows="3" class="w-full border rounded px-3 py-2"></textarea></div>
                </div>
            </div>
        `);
            });

            // 6. Achievement (ដូចដើម)
            document.getElementById('add-achievement')?.addEventListener('click', () => {
                const i = counters.achievement++;
                document.getElementById('achievement-container').insertAdjacentHTML('beforeend', `
            <div class="dynamic-block">
                <div class="remove-item">×</div>
                <h3 class="font-semibold text-indigo-700 mb-4">Achievement / Award #${i + 1}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="achievements[${i}][title]" class="w-full border rounded-lg px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Year Awarded</label>
                        <input type="number" name="achievements[${i}][year_awarded]" min="1900" max="2100" placeholder="2024" class="w-full border rounded-lg px-4 py-3">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Country</label>
                        <input type="text" name="achievements[${i}][country]" placeholder="e.g. Cambodia, Japan" class="w-full border rounded-lg px-4 py-3">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Program Name</label>
                        <input type="text" name="achievements[${i}][program_name]" placeholder="e.g. JICA Scholarship" class="w-full border rounded-lg px-4 py-3">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Organizer</label>
                        <input type="text" name="achievements[${i}][organizer_name]" placeholder="e.g. KOICA, USAID" class="w-full border rounded-lg px-4 py-3">
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block mb-1 text-sm font-medium text-gray-700">Remark</label>
                        <textarea name="achievements[${i}][remark]" rows="2" class="w-full border rounded-lg px-4 py-3" placeholder="Additional information..."></textarea>
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block mb-1 text-sm font-medium text-gray-700">Certificate / Attachment</label>
                        <input type="file" name="achievements[${i}][attachment]" accept=".pdf,.jpg,.jpeg,.png" class="w-full border rounded-lg px-4 py-3">
                    </div>
                </div>
            </div>
        `);
            });

            // 7. Attachment - ដូចដើម តែកែ counter + បន្ថែម rebuild old data
            document.getElementById('add-attachment')?.addEventListener('click', () => {
                const i = counters.attachment++;
                document.getElementById('attachment-container').insertAdjacentHTML('beforeend', `
            <div class="dynamic-block">
                <div class="remove-item">×</div>
                <h3 class="font-semibold text-indigo-700 mb-4">Attachment #${i + 1}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label>Attachment Name</label><input type="text" name="attachments[${i}][name]" class="w-full border rounded px-3 py-2" required></div>
                    <div><label>File</label><input type="file" name="attachments[${i}][file]" class="w-full" required></div>
                </div>
            </div>
        `);
            });

            // សំខាន់បំផុត: បង្កើត old attachments ឡើងវិញពេល validation fail
            @if(old('attachments'))
                @foreach(old('attachments') as $index => $attach)
                                < script >
                                (function () {
                                    const i = {{ $index }};
                                    counters.attachment = Math.max(counters.attachment, i + 1);
                                    document.getElementById('attachment-container').insertAdjacentHTML('beforeend', `
                                        <div class="dynamic-block">
                                            <div class="remove-item">×</div>
                                            <h3 class="font-semibold text-indigo-700 mb-4">Attachment #${i + 1}</h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div><label>Attachment Name</label><input type="text" name="attachments[${i}][name]" value="{{ old("attachments.${index}.name") }}" class="w-full border rounded px-3 py-2" required></div>
                                                <div><label>File</label><input type="file" name="attachments[${i}][file]" class="w-full"></div>
                                            </div>
                                        </div>
                                    `);
                                })();
                    </script>
                @endforeach
            @endif
    });
    </script>
</body>

</html>