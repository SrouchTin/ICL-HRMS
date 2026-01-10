<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Leave Request • {{ Auth::user()->username }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="{{ asset('assets/toast/css.css') }}" rel="stylesheet">
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-100">
    @include('toastify.toast')

    <div x-data="appData()" class="flex h-screen overflow-hidden">
        @include('layout.hrSidebar')

        <div class="flex-1 flex flex-col">
            <header class="bg-white border-b shadow-sm z-10">
                <div class="px-4 md:px-8 py-4 flex justify-between items-center">
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">
                        Create Leave Request
                        <span x-show="selectedEmployeeName && employee_id != '{{ Auth::user()->employee->id }}'"
                              x-text="'for ' + selectedEmployeeName"
                              class="text-lg font-normal text-gray-600"></span>
                    </h1>
                    <a href="{{ route('hr.own-leave.index') }}"
                       class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 md:p-8 bg-gray-50">
                <div class="max-w-4xl mx-auto">
                    <!-- Flash Messages -->
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-300 text-green-700 p-4 rounded-lg mb-6 flex items-center">
                            <i class="fas fa-check-circle mr-3"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-lg mb-6 flex items-center">
                            <i class="fas fa-exclamation-circle mr-3"></i>{{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-lg mb-6">
                            <strong class="block mb-2 font-semibold">Please correct the following errors:</strong>
                            <ul class="list-disc pl-6 space-y-1">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Leave Balance Cards -->
                    <div x-show="showBalance && employee_id" x-cloak x-transition class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-xl shadow-md text-center">
                            <p class="text-sm text-gray-600 uppercase tracking-wider">Total Entitlement</p>
                            <p class="text-5xl font-bold text-gray-800 mt-3" x-text="totalBalance"></p>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-md text-center">
                            <p class="text-sm text-gray-600 uppercase tracking-wider">Used</p>
                            <p class="text-5xl font-bold text-orange-600 mt-3" x-text="usedBalance"></p>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-md text-center">
                            <p class="text-sm text-gray-600 uppercase tracking-wider">Remaining</p>
                            <p class="text-5xl font-bold text-green-600 mt-3" x-text="remainingBalance"></p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
                        <form method="POST" action="{{ route('hr.own-leave.store') }}" class="space-y-8" @submit.prevent="onSubmit">
                            @csrf

                            <!-- Select HR Employee (including self) -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Request For <span class="text-red-600">*</span></label>
                                <select name="employee_id" x-model="employee_id" @change="onEmployeeChange" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Select HR Employee --</option>
                                    @foreach($hrEmployees as $hr)
                                        <option value="{{ $hr->id }}"
                                            {{ old('employee_id', Auth::user()->employee->id) == $hr->id ? 'selected' : '' }}>
                                            {{ $hr->employee_code }} - {{ $hr->personalInfo?->full_name_en ?? 'No Name' }}
                                            {{ $hr->id == Auth::user()->employee->id ? ' (Me)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Leave Type -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Leave Type <span class="text-red-600">*</span></label>
                                <select name="leave_type_id" x-model="leave_type_id" @change="onLeaveTypeChange" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Select Leave Type --</option>
                                    @foreach($leaveTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Approval Flow -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-3">Approval Flow <span class="text-red-600">*</span></label>
                                <div class="space-y-4">
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="radio" name="flow_type" value="supervisor" x-model="flow_type"
                                            @change="onFlowTypeChange" class="w-5 h-5 text-indigo-600"
                                            {{ old('flow_type', 'supervisor') === 'supervisor' ? 'checked' : '' }}>
                                        <span class="font-medium text-gray-700">Supervisor Approval</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="radio" name="flow_type" value="hr" x-model="flow_type"
                                            @change="onFlowTypeChange" class="w-5 h-5 text-indigo-600"
                                            {{ old('flow_type') === 'hr' ? 'checked' : '' }}>
                                        <span class="font-medium text-gray-700">HR Approval</span>
                                    </label>
                                </div>
                            </div>

                            <div x-show="flow_type === 'hr'" x-transition>
                                <label class="block font-medium text-gray-700 mb-2">Select Approving HR <span class="text-red-600">*</span></label>
                                <select name="hr_id" x-model="hr_id" :required="flow_type === 'hr'"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Select HR --</option>
                                    @foreach($hrEmployees as $hr)
                                        <option value="{{ $hr->id }}" {{ old('hr_id') == $hr->id ? 'selected' : '' }}>
                                            {{ $hr->employee_code }} - {{ $hr->personalInfo?->full_name_en ?? 'No Name' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Duration -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-3">Leave For <span class="text-red-600">*</span></label>
                                <div class="space-y-4">
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="radio" name="leave_for" value="full_day" x-model="leave_for"
                                            @change="calculate" class="w-5 h-5 text-indigo-600" checked>
                                        <span class="font-medium text-gray-700">Full Day</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="radio" name="leave_for" value="half_day" x-model="leave_for"
                                            @change="calculate" class="w-5 h-5 text-indigo-600">
                                        <span class="font-medium text-gray-700">Half Day</span>
                                    </label>
                                    <div x-show="leave_for === 'half_day'" x-transition class="ml-8 space-y-3">
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input type="radio" name="half_day_type" value="morning" x-model="half_day_type" class="w-5 h-5 text-indigo-600">
                                            <span class="text-gray-700">Morning</span>
                                        </label>
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input type="radio" name="half_day_type" value="afternoon" x-model="half_day_type" class="w-5 h-5 text-indigo-600">
                                            <span class="text-gray-700">Afternoon</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block font-medium text-gray-700 mb-2">From Date <span class="text-red-600">*</span></label>
                                    <input type="date" name="from_date" x-model="from_date" @change="onDateChange" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block font-medium text-gray-700 mb-2">To Date <span class="text-red-600">*</span></label>
                                    <input type="date" name="to_date" x-model="to_date" @change="onDateChange" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                            <!-- Total Days -->
                            <div x-show="leavePeriod" x-transition class="mt-4">
                                <label class="block font-medium text-gray-700 mb-2">Total Leave Days</label>
                                <div class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-50">
                                    <span class="font-medium text-gray-800 text-lg" x-text="leavePeriod"></span>
                                </div>
                            </div>

                            <!-- Insufficient Balance -->
                            <div x-show="showBalance && requestedDays > remainingBalance && requestedDays > 0" x-transition
                                class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg flex items-start gap-3">
                                <i class="fas fa-exclamation-triangle text-xl mt-0.5"></i>
                                <div>
                                    <strong class="font-semibold">Insufficient Leave Balance</strong>
                                    <p class="mt-1 text-sm">Requesting <strong x-text="requestedDays"></strong> day(s), but only <strong x-text="remainingBalance"></strong> remaining.</p>
                                </div>
                            </div>

                            <!-- Subject, Reason, Remark -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Subject <span class="text-red-600">*</span></label>
                                <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="e.g. Annual Leave"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Reason <span class="text-red-600">*</span></label>
                                <textarea name="reason" rows="4" required placeholder="Detailed reason..."
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('reason') }}</textarea>
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Remark</label>
                                <textarea name="remark" rows="3" placeholder="Optional..."
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('remark') }}</textarea>
                            </div>

                            <!-- Person In Charge -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Person In Charge <span class="text-red-600">*</span></label>
                                <select name="person_incharge_id" x-model="person_incharge_id" required
                                    :disabled="loadingPIC || !employee_id || !from_date || !to_date"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100">
                                    <option value="">-- Select Person In Charge --</option>
                                    <template x-if="loadingPIC">
                                        <option disabled>Loading available employees...</option>
                                    </template>
                                    <template x-if="!loadingPIC && !employee_id">
                                        <option disabled>Please select employee first</option>
                                    </template>
                                    <template x-if="!loadingPIC && employee_id && (!from_date || !to_date)">
                                        <option disabled>Please select dates first</option>
                                    </template>
                                    <template x-if="!loadingPIC && availablePIC.length === 0">
                                        <option disabled>No available employee during this period</option>
                                    </template>
                                    <template x-for="emp in availablePIC" :key="emp.id">
                                        <option :value="emp.id" x-text="emp.text"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Submit -->
                            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-8 border-t">
                                <a href="{{ route('hr.own-leave.index') }}"
                                   class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition text-center">
                                    Cancel
                                </a>
                                <button type="submit" :disabled="!isFormValid()"
                                    class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white rounded-lg font-semibold shadow-md">
                                    Submit Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="{{ asset('assets/toast/script.js') }}"></script>
<script>
    function leaveForm() {
        return {
            // Initial data from Laravel
            employee_id: '{{ old('employee_id', Auth::user()->employee->id ?? '') }}',
            selectedEmployeeName: '',
            person_incharge_id: '{{ old('person_incharge_id') ?? '' }}',
            leave_type_id: '{{ old('leave_type_id') ?? '' }}',
            leave_for: '{{ old('leave_for', 'full_day') }}',
            half_day_type: '{{ old('half_day_type') ?? '' }}',
            from_date: '{{ old('from_date') ?? '' }}',
            to_date: '{{ old('to_date') ?? '' }}',
            flow_type: '{{ old('flow_type', 'supervisor') }}',
            hr_id: '{{ old('hr_id') ?? '' }}',

            // Computed / UI state
            leavePeriod: '',
            requestedDays: 0,
            totalBalance: 0,
            usedBalance: 0,
            remainingBalance: 0,
            showBalance: false,

            availablePIC: [],
            loadingPIC: false,

            // URLs
            balanceUrl: '{{ route('employee.leave.balance') }}',
            picUrl: '{{ route('employee.person.incharge.available') }}',

            // ==================== LIFECYCLE ====================
            init() {
                this.updateEmployeeName();
                this.calculate();

                // Wait for DOM to be fully ready, then load data if possible
                this.$nextTick(() => {
                    if (this.employee_id && this.leave_type_id) {
                        this.loadBalance();
                    }
                    if (this.employee_id && this.from_date && this.to_date) {
                        this.loadPersonInCharge();
                    }
                });
            },

            // ==================== HELPERS ====================
            updateEmployeeName() {
                const select = document.querySelector('select[name="employee_id"]');
                if (!select) return;
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption) {
                    this.selectedEmployeeName = selectedOption.text.replace(' (Me)', '').trim();
                }
            },

            calculate() {
                this.leavePeriod = '';
                this.requestedDays = 0;

                if (!this.from_date || !this.to_date) return;

                const from = new Date(this.from_date);
                const to = new Date(this.to_date);

                if (isNaN(from) || isNaN(to) || to < from) {
                    this.leavePeriod = 'Invalid date range';
                    return;
                }

                const days = Math.floor((to - from) / 86400000) + 1;

                if (this.leave_for === 'full_day') {
                    this.requestedDays = days;
                } else {
                    // Half day logic
                    this.requestedDays = days === 1 ? 0.5 : (days - 1) + 0.5;
                }

                this.leavePeriod = `${this.requestedDays} day${this.requestedDays !== 1 ? 's' : ''}`;
            },

            // ==================== EVENT HANDLERS ====================
            onEmployeeChange() {
                this.updateEmployeeName();
                this.person_incharge_id = '';
                this.showBalance = false;
                this.availablePIC = [];
                this.loadingPIC = false;

                // Reload balance if leave type is selected
                if (this.leave_type_id) {
                    this.loadBalance();
                }

                // Reload PIC if dates are filled
                if (this.from_date && this.to_date) {
                    this.loadPersonInCharge();
                }
            },

            onLeaveTypeChange() {
                this.showBalance = false;
                if (this.employee_id && this.leave_type_id) {
                    this.loadBalance();
                }
            },

            onDateChange() {
                this.person_incharge_id = '';
                this.calculate();

                if (this.employee_id && this.from_date && this.to_date) {
                    this.loadPersonInCharge();
                }
            },

            onFlowTypeChange() {
                if (this.flow_type !== 'hr') {
                    this.hr_id = '';
                }
            },

            // ==================== API CALLS ====================
            async loadBalance() {
                if (!this.employee_id || !this.leave_type_id) {
                    this.showBalance = false;
                    return;
                }

                try {
                    const url = `${this.balanceUrl}?employee_id=${this.employee_id}&leave_type_id=${this.leave_type_id}`;
                    console.log('Fetching balance:', url);

                    const response = await fetch(url);

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const data = await response.json();

                    this.totalBalance = parseFloat(data.total) || 0;
                    this.usedBalance = parseFloat(data.used) || 0;
                    this.remainingBalance = parseFloat(data.remaining) || 0;
                    this.showBalance = true;

                    console.log('Balance loaded:', data);
                } catch (error) {
                    console.error('Failed to load leave balance:', error);
                    this.showBalance = false;
                }
            },

            async loadPersonInCharge() {
                if (!this.employee_id || !this.from_date || !this.to_date) {
                    this.availablePIC = [];
                    this.loadingPIC = false;
                    return;
                }

                this.loadingPIC = true;
                this.availablePIC = [];

                try {
                    const params = new URLSearchParams({
                        exclude_employee_id: this.employee_id,
                        from_date: this.from_date,
                        to_date: this.to_date
                    });

                    const url = `${this.picUrl}?${params}`;
                    console.log('Fetching PIC:', url);

                    const response = await fetch(url);

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const data = await response.json();

                    this.availablePIC = data;
                    console.log('PIC loaded:', data);
                } catch (error) {
                    console.error('Failed to load Person In Charge:', error);
                    this.availablePIC = [];
                } finally {
                    this.loadingPIC = false;
                }
            },

            // ==================== FORM VALIDATION & SUBMIT ====================
            isFormValid() {
                const hasEmployee = !!this.employee_id;
                const hasLeaveType = !!this.leave_type_id;
                const hasPIC = !!this.person_incharge_id;
                const hasValidDates = this.requestedDays > 0 && this.leavePeriod !== 'Invalid date range';
                const hasHalfDayType = this.leave_for !== 'half_day' || !!this.half_day_type;
                const hasHRIfNeeded = this.flow_type !== 'hr' || !!this.hr_id;

                return hasEmployee && hasLeaveType && hasPIC && hasValidDates && hasHalfDayType && hasHRIfNeeded;
            },

            onSubmit() {
                if (!this.isFormValid()) {
                    alert('Please complete all required fields correctly.');
                    return false;
                }

                // Submit the actual form
                this.$el.submit();
            }
        };
    }

    // Main Alpine component
    function appData() {
        return {
            sidebarOpen: false,
            ...leaveForm(),
            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
            }
        };
    }
</script>
</body>
</html>