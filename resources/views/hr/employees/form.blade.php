{{-- resources/views/hr/employees/form.blade.php --}}
{{-- Used by both create.blade.php and edit.blade.php --}}

<div class="max-w-5xl mx-auto">
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-6">
            <h1 class="text-2xl font-bold">
                {{ isset($employee) ? 'Edit Employee' : 'Add New Employee' }}
            </h1>
        </div>

        <form action="{{ isset($employee) ? route('hr.employees.update', $employee) : route('hr.employees.store') }}" 
              method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            @if(isset($employee)) @method('PUT') @endif

            <!-- Tabs Navigation -->
            <div class="flex border-b border-gray-200 mb-8 overflow-x-auto">
                <button type="button" class="tab-btn py-3 px-6 font-medium text-sm border-b-2 border-indigo-500 text-indigo-600" data-tab="tab-basic">
                    Basic Info
                </button>
                <button type="button" class="tab-btn py-3 px-6 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-tab="tab-personal">
                    Personal
                </button>
                <button type="button" class="tab-btn py-3 px-6 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-tab="tab-contact">
                    Contact & Address
                </button>
                <button type="button" class="tab-btn py-3 px-6 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-tab="tab-family">
                    Family & Emergency
                </button>
                <button type="button" class="tab-btn py-3 px-6 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-tab="tab-education">
                    Education & Training
                </button>
                <button type="button" class="tab-btn py-3 px-6 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-tab="tab-docs">
                    Documents
                </button>
            </div>

            <!-- Tab: Basic Info -->
            <div id="tab-basic" class="tab-content">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee Photo</label>
                        <div class="mt-2 flex items-center">
                            @if(isset($employee) && $employee->image)
                                <img src="{{ asset('storage/'.$employee->image) }}" class="h-20 w-20 rounded-full object-cover mr-4">
                            @endif
                            <input type="file" name="image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee Code <span class="text-red-500">*</span></label>
                        <input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code ?? '') }}" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department <span class="text-red-500">*</span></label>
                        <select name="department_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Department</option>
                            @foreach(\App\Models\Department::where('status', 'active')->get() as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id ?? '') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Branch <span class="text-red-500">*</span></label>
                        <select name="branch_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Branch</option>
                            @foreach(\App\Models\Branch::where('status', 'active')->get() as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $employee->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->branch_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $employee->start_date ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active" {{ old('status', $employee->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $employee->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="terminated" {{ old('status', $employee->status ?? '') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tab: Personal Info -->
            <div id="tab-personal" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label>Full Name (English)</label><input type="text" name="personal[full_name_en]" value="{{ old('personal.full_name_en', $employee->personalInfo->full_name_en ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div><label>Full Name (Khmer)</label><input type="text" name="personal[full_name_kh]" value="{{ old('personal.full_name_kh', $employee->personalInfo->full_name_kh ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div><label>Date of Birth</label><input type="date" name="personal[dob]" value="{{ old('personal.dob', $employee->personalInfo->dob ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div>
                        <label>Gender</label>
                        <select name="personal[gender]" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="male" {{ old('personal.gender', $employee->personalInfo->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('personal.gender', $employee->personalInfo->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div><label>Nationality</label><input type="text" name="personal[nationality]" value="{{ old('personal.nationality', $employee->personalInfo->nationality ?? 'Cambodian') }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div><label>Blood Group</label><input type="text" name="personal[blood_group]" value="{{ old('personal.blood_group', $employee->personalInfo->blood_group ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div><label>Marital Status</label>
                        <select name="personal[marital_status]" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="single" {{ old('personal.marital_status', $employee->personalInfo->marital_status ?? '') == 'single' ? 'selected' : '' }}>Single</option>
                            <option value="married">Married</option>
                            <option value="divorced">Divorced</option>
                            <option value="widowed">Widowed</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tab: Contact & Address -->
            <div id="tab-contact" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label>Phone Number</label><input type="text" name="contact[phone_number]" value="{{ old('contact.phone_number', $employee->contact->phone_number ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div><label>Email</label><input type="email" name="contact[email]" value="{{ old('contact.email', $employee->contact->email ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div><label>Address</label><textarea name="address[address]" rows="3" class="mt-1 block w-full rounded-md border-gray-300">{{ old('address.address', $employee->address->address ?? '') }}</textarea></div>
                    <div><label>City / Province</label><input type="text" name="address[province]" value="{{ old('address.province', $employee->address->province ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                </div>
            </div>

            <!-- Tab: Family & Emergency -->
            <div id="tab-family" class="tab-content hidden">
                <h3 class="text-lg font-semibold mb-4">Emergency Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div><label>Contact Person</label><input type="text" name="emergency[contact_person]" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div><label>Relationship</label><input type="text" name="emergency[relationship]" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div><label>Phone</label><input type="text" name="emergency[phone_number]" class="mt-1 block w-full rounded-md border-gray-300"></div>
                </div>
            </div>

            <!-- Tab: Education & Documents -->
            <div id="tab-education" class="tab-content hidden">
                <p class="text-gray-600">Education, Training & Employment History will be added dynamically with "Add More" buttons in the next version.</p>
            </div>

            <div id="tab-docs" class="tab-content hidden">
                <label>Upload Documents (ID, Certificate, etc.)</label>
                <input type="file" name="attachments[]" multiple class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700">
            </div>

            <!-- Submit Buttons -->
            <div class="mt-10 flex justify-end space-x-4">
                <a href="{{ route('hr.employees.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow-lg">
                    {{ isset($employee) ? 'Update Employee' : 'Create Employee' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('border-indigo-500', 'text-indigo-600');
                b.classList.add('border-transparent', 'text-gray-500');
            });
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            
            this.classList.add('border-indigo-500', 'text-indigo-600');
            document.getElementById(this.dataset.tab).classList.remove('hidden');
        });
    });
</script>