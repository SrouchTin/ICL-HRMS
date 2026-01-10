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
    <link href="{{ asset('assets/toast/css.css') }}" rel="stylesheet">
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
    @include('toastify.toast')
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
                         Back
                    </a>
                </div>
            </header>
            <main class="flex-1 overflow-auto p-6 bg-gray-50">
                <div class="max-w-7xl mx-auto">
                    <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-8">
                        @csrf
                        <input type="hidden" name="status" value="active">
                        <script>
                            window.oldData = {
                                emergency_contacts: {!! old('emergency_contacts') ? json_encode(old('emergency_contacts')) : '[]' !!},
                                family_members:     {!! old('family_members') ? json_encode(old('family_members')) : '[]' !!},
                                education_history:  {!! old('education_history') ? json_encode(old('education_history')) : '[]' !!},
                                training_history:   {!! old('training_history') ? json_encode(old('training_history')) : '[]' !!},
                                employment_history: {!! old('employment_history') ? json_encode(old('employment_history')) : '[]' !!},
                                achievements:       {!! old('achievements') ? json_encode(old('achievements')) : '[]' !!},
                                attachments:        {!! old('attachments') ? json_encode(old('attachments')) : '[]' !!}
                            };
                        </script>
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
                                        @error('employee_code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">Department</label>
                                    <select name="department_id" class="w-full border rounded-lg px-4 py-3" required>
                                        <option value="">---- Select ----</option>
                                        @foreach(\App\Models\Department::where('status', 'active')->get() as $dept)
                                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->department_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">Branch</label>
                                    <select name="branch_id" class="w-full border rounded-lg px-4 py-3" required>
                                        <option value="">-- Select --</option>
                                        @foreach(\App\Models\Branch::where('status', 'active')->get() as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('branch_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <!-- NEW: Supervisor Field -->
                                <div>
                                    <label class="block mb-2 font-medium text-gray-700">Supervisor</label>
                                    <select name="supervisor_id" class="w-full border rounded-lg px-4 py-3">
                                        <option value="">-- No Supervisor --</option>
                                        @foreach(\App\Models\Employee::where('status', 'active')
                                            ->with('personalInfo')
                                            ->get() as $emp)
                                            <option value="{{ $emp->id }}" {{ old('supervisor_id') == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->employee_code }} - {{ $emp->personalInfo->full_name_en ?? 'No Name' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supervisor_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div><label class="block mb-2 font-medium text-gray-700 required">Position</label>
                                    <select name="position_id" class="w-full border rounded-lg px-4 py-3" required>
                                        <option value="">-- Select --</option>
                                        @foreach(\App\Models\Position::where('status', 'active')->get() as $pos)
                                            <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>{{ $pos->position_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('position_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
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
                                <div><label class="required">Salutation</label>
                                    <select name="salutation" class="w-full border rounded-lg px-4 py-3" required>
                                        <option value="">Select</option>
                                        <option value="Mr" {{ old('salutation') == 'Mr' ? 'selected' : '' }}>Mr.</option>
                                        <option value="Ms" {{ old('salutation') == 'Ms' ? 'selected' : '' }}>Ms.</option>
                                        <option value="Mrs" {{ old('salutation') == 'Mrs' ? 'selected' : '' }}>Mrs.</option>
                                    </select>
                                    @error('salutation') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div><label class="required">Full Name KH</label><input type="text" name="full_name_kh"
                                        value="{{ old('full_name_kh') }}" class="w-full border rounded-lg px-4 py-3"
                                        required></div>
                                        @error('full_name_kh') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                <div><label class="required">Full Name EN</label><input type="text" name="full_name_en"
                                        value="{{ old('full_name_en') }}" class="w-full border rounded-lg px-4 py-3"
                                        required></div>
                                        @error('full_name_en') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                <div><label class="required">Date of Birth</label><input type="date" name="dob"  value="{{ old('dob') }}" required
                                        class="w-full border rounded-lg px-4 py-3"></div>
                                        @error('dob') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                <div><label class="required">Gender</label>
                                    <select name="gender" class="w-full border rounded-lg px-4 py-3" required>
                                        <option value="">----Select----</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('gender') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div><label>Nationality</label><input type="text" name="nationality"
                                        value="{{ old('nationality', 'Cambodian') }}"
                                        class="w-full border rounded-lg px-4 py-3"></div>
                                <div><label class="required">Marital Status</label>
                                    <select name="marital_status" class="w-full border rounded-lg px-4 py-3" required>
                                        <option value="">----Select----</option>
                                        <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                    </select>
                                   @error('marital_status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div><label>Religion</label><input type="text" name="religion"
                                        value="{{ old('religion') }}" class="w-full border rounded-lg px-4 py-3"></div>
                                <div><label>Blood Group</label>
                                    <select name="blood_group" class="w-full border rounded-lg px-4 py-3">
                                        <option value="">----Select----</option>
                                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                            <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div><label>Bank Account Name</label><input type="text" name="bank_account_name"
                                        value="{{ old('bank_account_name') }}"
                                        class="w-full border rounded-lg px-4 py-3"></div>
                                <div><label>Bank Account Number</label><input type="number" name="bank_account_number"
                                        value="{{ old('bank_account_number') }}"
                                        class="w-full border rounded-lg px-4 py-3"></div>
                                <div><label class="required">Contract Type</label>
                                    <select name="contract_type" class="w-full border rounded-lg px-4 py-3" required>
                                        <option value="">----Select----</option>
                                        <option value="UDC" {{ old('contract_type') == 'UDC' ? 'selected' : '' }}>UDC</option>
                                        <option value="FDC" {{ old('contract_type') == 'FDC' ? 'selected' : '' }}>FDC</option>
                                    </select>
                                    @error('contract_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div><label class="required">Employee Type</label>
                                        <select name="employee_type" class="w-full border rounded-lg px-4 py-3" required>
                                            <option value="">----Select----</option>
                                            <option value="full_time" {{ old('employee_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                            <option value="part_time" {{ old('employee_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                            <option value="probation" {{ old('employee_type') == 'probation' ? 'selected' : '' }}>Probation</option>
                                            <option value="internship" {{ old('employee_type') == 'internship' ? 'selected' : '' }}>Internship</option>
                                            <option value="contract" {{ old('employee_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                                        </select>
                                </div>
                                <div><label class="required">Joining Date</label><input type="date" name="joining_date"
                                        value="{{ old('joining_date') }}" class="w-full border rounded-lg px-4 py-3"
                                        required></div>
                                        @error('joining_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                <div><label>End Date</label><input type="date" name="end_date" 
                                        value="{{ old('end_date') }}" class="w-full border rounded-lg px-4 py-3"></div>
                                <div><label class="required">Effective Date</label><input type="date"
                                        name="effective_date" value="{{ old('effective_date') }}"
                                        class="w-full border rounded-lg px-4 py-3" required></div>
                                        @error('effective_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- 3. Permanent Address -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold mr-3 flex items-center justify-center">
                                        3</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Permanent Address</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div><label >City</label><input type="text" name="city" value="{{ old('city') }}"
                                        class="w-full border rounded-lg px-4 py-3" ></div>
                                        
                                <div><label>Province</label><input type="text" name="province"
                                        value="{{ old('province') }}" class="w-full border rounded-lg px-4 py-3"></div>
                                        
                                <div><label >Country</label><input type="text" name="country"
                                        value="{{ old('country', 'Cambodia') }}"
                                        class="w-full border rounded-lg px-4 py-3"></div>
                                        
                                <div class="md:col-span-2">
                                    <label for="address" class="block font-medium text-gray-700 mb-2">
                                        Full Address <span class="text-red-600">*</span>
                                    </label>
                                    <textarea 
                                        id="address"
                                        name="address" 
                                        rows="3"
                                        title="Full Address"
                                        required
                                        oninvalid="this.setCustomValidity('Please Input FullAddress')"
                                        oninput="this.setCustomValidity('')"
                                        placeholder="ភូមិ ឃុំ ស្រុក ខេត្ត..."
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                    >{{ old('address') }}</textarea>

                                    @error('address')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 4. Contact Information -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold mr-3 flex items-center justify-center">
                                        4</div>
                                    <h2 class="text-xl font-semibold text-gray-800">Contact Information</h2>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div><label class="required">Phone Number</label><input type="tel" name="phone_number"
                                        value="{{ old('phone_number') }}" class="w-full border rounded-lg px-4 py-3" required>
                                </div>
                                @error('phone_number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
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

                        <!-- 5. Identification -->
                        <div class="form-section bg-white rounded-xl shadow-sm p-6 collapsed">
                            <div class="section-header flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold mr-3 flex items-center justify-center">
                                        5</div>
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
                                </button>
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
                               </button>
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
                                </button>
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
                            </button>
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
                               </button>
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
                               </button>
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
                                </button>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end mt-12 pb-10">
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-lg font-bold text-lg shadow-lg transition flex items-center">
                                <i class="fas fa-save mr-3"></i> CREATE
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
<script src="{{ asset('assets/toast/script.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Collapsible sections
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

        // Remove dynamic block
        document.addEventListener('click', e => {
            if (e.target.closest('.remove-item')) {
                e.target.closest('.dynamic-block').remove();
            }
        });

        // Counters - ចាប់តួលេខពី old data
        let counters = {
            emergency:   {{ count(old('emergency_contacts', [])) }},
            family:      {{ count(old('family_members', [])) }},
            education:   {{ count(old('education_history', [])) }},
            training:    {{ count(old('training_history', [])) }},
            employment:  {{ count(old('employment_history', [])) }},
            achievement: {{ count(old('achievements', [])) }},
            attachment:  {{ count(old('attachments', [])) }}
        };

        const addBlock = (containerId, html) => {
            const container = document.getElementById(containerId);
            if (container) container.insertAdjacentHTML('beforeend', html);
        };

        // ==================== RESTORE ALL OLD DATA - NO RESET! ====================

        // 1. Emergency Contact
        @if(old('emergency_contacts'))
            @foreach(old('emergency_contacts') as $i => $item)
                (() => {
                    const i = {{ $i }};
                    addBlock('emergency-contact-container', `
                        <div class="dynamic-block">
                            <div class="remove-item">×</div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Contact Person</label>
                                    <input type="text" name="emergency_contacts[${i}][contact_person]" value="{{ $item['contact_person'] ?? '' }}" class="w-full border rounded-lg px-4 py-3" required>
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Relationship</label>
                                    <input type="text" name="emergency_contacts[${i}][relationship]" value="{{ $item['relationship'] ?? '' }}" class="w-full border rounded-lg px-4 py-3">
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Phone</label>
                                    <input type="tel" name="emergency_contacts[${i}][phone_number]" value="{{ $item['phone_number'] ?? '' }}" class="w-full border rounded-lg px-4 py-3">
                                </div>
                            </div>
                        </div>
                    `);
                    counters.emergency = Math.max(counters.emergency, i + 1);
                })();
            @endforeach
        @endif

        // 2. Family Members
        @if(old('family_members'))
            @foreach(old('family_members') as $i => $item)
                addBlock('family-members-container', `
                    <div class="dynamic-block">
                        <div class="remove-item">×</div>
                        <h3 class="font-semibold text-indigo-700 mb-4">Family Member #{{ $i + 1 }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label>Name</label><input type="text" name="family_members[{{ $i }}][name]" value="{{ old('family_members.'.$i.'.name') }}" class="w-full border rounded px-3 py-2"></div>
                            <div><label>Relationship</label><input type="text" name="family_members[{{ $i }}][relationship]" value="{{ old('family_members.'.$i.'.relationship') }}" class="w-full border rounded px-3 py-2"></div>
                            <div><label>Nationality</label><input type="text" name="family_members[{{ $i }}][nationality]" value="{{ old('family_members.'.$i.'.nationality') }}" class="w-full border rounded px-3 py-2"></div>
                            <div><label>Date of Birth</label><input type="date" name="family_members[{{ $i }}][dob]" value="{{ old('family_members.'.$i.'.dob') }}" class="w-full border rounded px-3 py-2"></div>
                            <div><label>Gender</label>
                                <select name="family_members[{{ $i }}][gender]" class="w-full border rounded px-3 py-2">
                                    <option value="male" {{ old('family_members.'.$i.'.gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('family_members.'.$i.'.gender') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div><label>Tax Filing</label>
                                <select name="family_members[{{ $i }}][tax_filing]" class="w-full border rounded px-3 py-2">
                                    <option value="1" {{ old('family_members.'.$i.'.tax_filing') == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ old('family_members.'.$i.'.tax_filing') == '0' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div><label>Phone</label><input type="tel" name="family_members[{{ $i}}][phone_number]" value="{{ old('family_members.'.$i.'.phone_number') }}" class="w-full border rounded px-3 py-2"></div>
                            <div class="md:col-span-2"><label>Remark</label><input type="text" name="family_members[{{ $i }}][remark]" value="{{ old('family_members.'.$i.'.remark') }}" class="w-full border rounded px-3 py-2"></div>
                            <div class="md:col-span-2"><label>Attachment</label><input type="file" name="family_members[{{ $i }}][attachment]" class="w-full"></div>
                        </div>
                    </div>
                `);
                counters.family = Math.max(counters.family, {{ $i + 1 }});
            @endforeach
        @endif

        // 3. Education History
        @if(old('education_history'))
            @foreach(old('education_history') as $i => $item)
                addBlock('education-container', `
                    <div class="dynamic-block">
                        <div class="remove-item">×</div>
                        <h3 class="font-semibold text-indigo-700 mb-4">Education #{{ $i + 1 }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label>Institute</label><input type="text" name="education_history[{{ $i }}][institute]" value="{{ old('education_history.'.$i.'.institute') }}" class="w-full border rounded px-3 py-2"></div>
                            <div><label>Degree</label><input type="text" name="education_history[{{ $i }}][degree]" value="{{ old('education_history.'.$i.'.degree') }}" class="w-full border rounded px-3 py-2"></div>
                            <div><label>Subject</label><input type="text" name="education_history[{{ $i }}][subject]" value="{{ old('education_history.'.$i.'.subject') }}" class="w-full border rounded px-3 py-2"></div>
                            <div><label>Start Date</label><input type="date" name="education_history[{{ $i }}][start_date]" value="{{ old('education_history.'.$i.'.start_date') }}" class="w-full border rounded px-3 py-2"></div>
                            <div><label>End Date</label><input type="date" name="education_history[{{ $i }}][end_date]" value="{{ old('education_history.'.$i.'.end_date') }}" class="w-full border rounded px-3 py-2"></div>
                            <div class="md:col-span-2"><label>Remark</label><textarea name="education_history[{{ $i }}][remark]" rows="2" class="w-full border rounded px-3 py-2">{{ old('education_history.'.$i.'.remark') }}</textarea></div>
                        </div>
                    </div>
                `);
                counters.education = Math.max(counters.education, {{ $i + 1 }});
            @endforeach
        @endif

        // 4. Training History
        @if(old('training_history'))
            @foreach(old('training_history') as $i => $item)
                addBlock('training-container', `
                    <div class="dynamic-block">
                        <div class="remove-item">×</div>
                        <h3 class="font-semibold text-indigo-700 mb-4">Training #{{ $i + 1 }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label>Institute</label><input type="text" name="training_history[{{ $i }}][institute]" value="{{ old('training_history.'.$i.'.institute') }}" class="w-full border rounded px-3 py-2"></div>
                            <div><label>Subject</label><input type="text" name="training_history[{{ $i }}][subject]" value="{{ old('training_history.'.$i.'.subject') }}" class="w-full border rounded px-3 py-2"></div>
                            <div><label>Start Date</label><input type="date" name="training_history[{{ $i }}][start_date]" value="{{ old('training_history.'.$i.'.start_date') }}" class="w-full border rounded px-3 py-2"></div>
                            <div><label>End Date</label><input type="date" name="training_history[{{ $i }}][end_date]" value="{{ old('training_history.'.$i.'.end_date') }}" class="w-full border rounded px-3 py-2"></div>
                            <div class="md:col-span-2"><label>Remark</label><textarea name="training_history[{{ $i }}][remark]" rows="2" class="w-full border rounded px-3 py-2">{{ old('training_history.'.$i.'.remark') }}</textarea></div>
                            <div class="md:col-span-2"><label>Attachment</label><input type="file" name="training_history[{{ $i }}][attachment]" class="w-full"></div>
                        </div>
                    </div>
                `);
                counters.training = Math.max(counters.training, {{ $i + 1 }});
            @endforeach
        @endif

        // 5. Employment History
        @if(old('employment_history'))
            @foreach(old('employment_history') as $i => $item)
                addBlock('employment-container', `
                    <div class="dynamic-block">
                        <div class="remove-item">×</div>
                        <h3 class="font-semibold text-indigo-700 mb-4">Employment #{{ $i + 1 }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label>Company Name</label><input type="text" name="employment_history[{{ $i }}][company_name]" value="{{ $item['company_name'] ?? '' }}" class="w-full border rounded px-3 py-2" required></div>
                            <div><label>Designation</label><input type="text" name="employment_history[{{ $i }}][designation]" value="{{ $item['designation'] ?? '' }}" class="w-full border rounded px-3 py-2"></div>
                            <div><label>Start Date</label><input type="date" name="employment_history[{{ $i }}][start_date]" value="{{ $item['start_date'] ?? '' }}" class="w-full border rounded px-3 py-2"></div>
                            <div><label>End Date</label><input type="date" name="employment_history[{{ $i }}][end_date]" value="{{ $item['end_date'] ?? '' }}" class="w-full border rounded px-3 py-2"></div>
                            <div><label>Supervisor Name</label><input type="text" name="employment_history[{{ $i }}][supervisor_name]" value="{{ $item['supervisor_name'] ?? '' }}" class="w-full border rounded px-3 py-2"></div>
                            <div><label>Rate / Salary</label><input type="text" name="employment_history[{{ $i }}][rate]" value="{{ $item['rate'] ?? '' }}" placeholder="e.g. $800" class="w-full border rounded px-3 py-2"></div>
                            <div class="md:col-span-2"><label>Remark</label><textarea name="employment_history[{{ $i }}][remark]" rows="2" class="w-full border rounded px-3 py-2">{{ $item['remark'] ?? '' }}</textarea></div>
                            <div class="md:col-span-2"><label>Reason for Leaving</label><textarea name="employment_history[{{ $i }}][reason_for_leaving]" rows="3" class="w-full border rounded px-3 py-2">{{ $item['reason_for_leaving'] ?? '' }}</textarea></div>
                        </div>
                    </div>
                `);
                counters.employment = Math.max(counters.employment, {{ $i + 1 }});
            @endforeach
        @endif

        // 6. Achievements
        @if(old('achievements'))
            @foreach(old('achievements') as $i => $item)
                addBlock('achievement-container', `
                    <div class="dynamic-block">
                        <div class="remove-item">×</div>
                        <h3 class="font-semibold text-indigo-700 mb-4">Achievement / Award #{{ $i + 1 }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                                <input type="text" name="achievements[{{ $i }}][title]" value="{{ old('achievements.'.$i.'.title') }}" class="w-full border rounded-lg px-4 py-3" required>
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Year Awarded</label>
                                <input type="number" name="achievements[{{ $i }}][year_awarded]" value="{{ old('achievements.'.$i.'.year_awarded') }}" min="1900" max="2100" placeholder="2024" class="w-full border rounded-lg px-4 py-3">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Country</label>
                                <input type="text" name="achievements[{{ $i }}][country]" value="{{ old('achievements.'.$i.'.country') }}" placeholder="e.g. Cambodia, Japan" class="w-full border rounded-lg px-4 py-3">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Program Name</label>
                                <input type="text" name="achievements[{{ $i }}][program_name]" value="{{ old('achievements.'.$i.'.program_name') }}" placeholder="e.g. JICA Scholarship" class="w-full border rounded-lg px-4 py-3">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Organizer</label>
                                <input type="text" name="achievements[{{ $i }}][organizer_name]" value="{{ old('achievements.'.$i.'.organizer_name') }}" placeholder="e.g. KOICA, USAID" class="w-full border rounded-lg px-4 py-3">
                            </div>
                            <div class="md:col-span-2 lg:col-span-3">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Remark</label>
                                <textarea name="achievements[{{ $i }}][remark]" rows="2" class="w-full border rounded-lg px-4 py-3" placeholder="Additional information...">{{ old('achievements.'.$i.'.remark') }}</textarea>
                            </div>
                            <div class="md:col-span-2 lg:col-span-3">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Attachment</label>
                                <input type="file" name="achievements[{{ $i }}][attachment]" accept=".pdf,.jpg,.jpeg,.png" class="w-full border rounded-lg px-4 py-3">
                            </div>
                        </div>
                    </div>
                `);
                counters.achievement = Math.max(counters.achievement, {{ $i + 1 }});
            @endforeach
        @endif

        // 7. Attachments
        @if(old('attachments'))
            @foreach(old('attachments') as $i => $item)
                addBlock('attachment-container', `
                    <div class="dynamic-block">
                        <div class="remove-item">×</div>
                        <h3 class="font-semibold text-indigo-700 mb-4">Attachment #{{ $i + 1 }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label>Attachment Name</label><input type="text" name="attachments[{{ $i }}][name]" value="{{ old('attachments.'.$i.'.name') }}" class="w-full border rounded px-3 py-2" required></div>
                            <div><label>File</label><input type="file" name="attachments[{{ $i }}][file]" class="w-full"></div>
                        </div>
                    </div>
                `);
                counters.attachment = Math.max(counters.attachment, {{ $i + 1 }});
            @endforeach
        @endif

        // ==================== ADD NEW BUTTONS - គ្រប់ section ទាំងអស់ ====================

        document.getElementById('add-emergency-contact')?.addEventListener('click', () => {
            const i = counters.emergency++;
            addBlock('emergency-contact-container', `
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

        document.getElementById('add-family-member')?.addEventListener('click', () => {
            const i = counters.family++;
            addBlock('family-members-container', `
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

        document.getElementById('add-education')?.addEventListener('click', () => {
            const i = counters.education++;
            addBlock('education-container', `
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

        document.getElementById('add-training')?.addEventListener('click', () => {
            const i = counters.training++;
            addBlock('training-container', `
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

        document.getElementById('add-employment')?.addEventListener('click', () => {
            const i = counters.employment++;
            addBlock('employment-container', `
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

        document.getElementById('add-achievement')?.addEventListener('click', () => {
            const i = counters.achievement++;
            addBlock('achievement-container', `
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
                            <label class="block mb-1 text-sm font-medium text-gray-700">Attachment</label>
                            <input type="file" name="achievements[${i}][attachment]" accept=".pdf,.jpg,.jpeg,.png" class="w-full border rounded-lg px-4 py-3">
                        </div>
                    </div>
                </div>
            `);
        });

        document.getElementById('add-attachment')?.addEventListener('click', () => {
            const i = counters.attachment++;
            addBlock('attachment-container', `
                <div class="dynamic-block">
                    <div class="remove-item">×</div>
                    <h3 class="font-semibold text-indigo-700 mb-4">Attachment #${i + 1}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label>Attachment Name</label><input type="text" name="attachments[${i}][name]" class="w-full border rounded px-3 py-2" required></div>
                        <div><label>File</label><input type="file" name="attachments[${i}][file]" class="w-full"></div>
                    </div>
                </div>
            `);
        });

        // Date year limit
        const applyDateLimit = () => {
            document.querySelectorAll('input[type="date"]').forEach(input => {
                if (input.dataset.bound) return;
                input.dataset.bound = 'true';
                input.addEventListener('input', function () {
                    let v = this.value;
                    if (v && v.length > 10) this.value = v.substring(0, 10);
                    let parts = v.split('-');
                    if (parts[0]?.length > 4) {
                        parts[0] = parts[0].substring(0, 4);
                        this.value = parts.join('-');
                    }
                });
                input.addEventListener('keydown', function (e) {
                    if (['Backspace','Delete','ArrowLeft','ArrowRight','Tab'].includes(e.key)) return;
                    if ((this.value + e.key).split('-')[0].length > 4) e.preventDefault();
                });
            });
        };

        applyDateLimit();
        new MutationObserver(applyDateLimit).observe(document.body, { childList: true, subtree: true });
    });
</script>
</body>

</html>