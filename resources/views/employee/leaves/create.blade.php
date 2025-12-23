<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Leave Request • {{ Auth::user()->username }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-100">

    <div x-data="appData()" class="flex h-screen overflow-hidden">
        @include('layout.employeeSidebar')

        <div class="flex-1 flex flex-col">
            <header class="bg-white border-b shadow-sm z-10">
                <div class="px-4 md:px-8 py-4 flex justify-between items-center">
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">Create Leave Request</h1>
                    <a href="{{ route('employee.leaves.index') }}"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 md:p-8 bg-gray-50">
                <div class="max-w-4xl mx-auto">

                    <!-- Flash Messages -->
                    @if (session('success'))
                        <div
                            class="bg-green-100 border border-green-300 text-green-700 p-4 rounded-lg mb-6 flex items-center">
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
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Leave Balance Cards -->
                    <div x-show="showBalance" x-cloak x-transition class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
                        <form method="POST" action="{{ route('employee.leaves.store') }}" class="space-y-8"
                            @submit="onSubmit">
                            @csrf

                            <!-- Employee -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Employee <span
                                        class="text-red-600">*</span></label>
                                <select name="employee_id" x-model="employee_id" @change="onEmployeeOrDateChange"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Select Employee --</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->employee_code }} - {{ $emp->personalInfo?->full_name_en ?? 'No Name' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Leave Type -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Leave Type <span
                                        class="text-red-600">*</span></label>
                                <select name="leave_type_id" x-model="leave_type_id" @change="loadBalance" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Select Leave Type --</option>
                                    @foreach($leaveTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('leave_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Leave Duration -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-3">Leave Duration <span
                                        class="text-red-600">*</span></label>
                                <div class="space-y-4">
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="radio" name="leave_for" value="full_day" x-model="leave_for"
                                            @change="onDurationChange" class="w-5 h-5 text-indigo-600" checked>
                                        <span class="font-medium text-gray-700">Full Day</span>
                                    </label>

                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="radio" name="leave_for" value="half_day" x-model="leave_for"
                                            @change="onDurationChange" class="w-5 h-5 text-indigo-600">
                                        <span class="font-medium text-gray-700">Half Day</span>
                                    </label>

                                    <!-- Half Day Type Selection (only show when half_day selected) -->
                                    <div x-show="leave_for === 'half_day'" x-transition class="ml-8 space-y-2">
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input type="radio" name="half_day_type" value="morning" x-model="half_day_type" required
                                                class="w-5 h-5 text-indigo-600">
                                            <span class="text-gray-700">Morning</span>
                                        </label>
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input type="radio" name="half_day_type" value="afternoon" x-model="half_day_type" required
                                                class="w-5 h-5 text-indigo-600">
                                            <span class="text-gray-700">Afternoon</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block font-medium text-gray-700 mb-2">From Date <span
                                            class="text-red-600">*</span></label>
                                    <input type="date" name="from_date" x-model="from_date"
                                        @change="onEmployeeOrDateChange" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('from_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block font-medium text-gray-700 mb-2">
                                        To Date <span x-show="leave_for === 'full_day'" class="text-red-600">*</span>
                                    </label>
                                    <input type="date" name="to_date" x-model="to_date" @change="onEmployeeOrDateChange"
                                        :disabled="leave_for === 'half_day'"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 disabled:bg-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('to_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Leave Period Display --}}
                            <div x-show="leavePeriod" x-transition>
                                <label class="block font-medium text-gray-700 mb-2">Leave Period</label>
                                <div class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-50">
                                    <span class="font-medium text-gray-800" x-text="leavePeriod"></span>
                                </div>
                            </div>

                            <!-- Insufficient Balance Warning -->
                            <div x-show="showBalance && requestedDays > remainingBalance && requestedDays > 0"
                                x-transition
                                class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg flex items-start gap-3">
                                <i class="fas fa-exclamation-triangle text-xl mt-0.5"></i>
                                <div>
                                    <strong class="font-semibold">Insufficient Leave Balance</strong>
                                    <p class="mt-1 text-sm">
                                        You are requesting <strong x-text="requestedDays"></strong> day(s)
                                        but only have <strong x-text="remainingBalance"></strong> remaining.
                                    </p>
                                </div>
                            </div>

                            <!-- Subject -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Subject <span
                                        class="text-red-600">*</span></label>
                                <input type="text" name="subject" value="{{ old('subject') }}" required
                                    placeholder="e.g. Annual Leave - Family Trip"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Reason -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Reason <span
                                        class="text-red-600">*</span></label>
                                <textarea name="reason" rows="4" required
                                    placeholder="Please provide detailed reason for leave..."
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('reason') }}</textarea>
                                @error('reason') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Remark -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Remark</label>
                                <textarea name="remark" rows="3" placeholder="Any additional notes..."
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('remark') }}</textarea>
                            </div>
                           
                            <!-- Person In Charge -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Person In Charge <span
                                        class="text-red-600">*</span></label>
                                <select name="person_incharge_id" x-model="person_incharge_id" required
                                    :disabled="loadingPIC || !employee_id || !from_date || availablePIC.length === 0"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100">
                                    <option value="">-- Select Person In Charge --</option>

                                    <template x-if="loadingPIC">
                                        <option disabled>⏳ Loading available employees...</option>
                                    </template>

                                    <template x-if="!loadingPIC && !employee_id">
                                        <option disabled>Please select employee first</option>
                                    </template>

                                    <template x-if="!loadingPIC && employee_id && !from_date">
                                        <option disabled>Please select From Date</option>
                                    </template>

                                    <template
                                        x-if="!loadingPIC && employee_id && from_date && availablePIC.length === 0">
                                        <option disabled>No one available during this period</option>
                                    </template>

                                    <template x-for="emp in availablePIC" :key="emp.id">
                                        <option :value="emp.id" x-text="emp.text"></option>
                                    </template>
                                </select>
                                @error('person_incharge_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <!-- Submit Buttons -->
                            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-8 border-t">
                                <a href="{{ route('employee.leaves.index') }}"
                                    class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition text-center">
                                    <i class="fas fa-times mr-2"></i>Cancel
                                </a>
                                <button type="submit" :disabled="!person_incharge_id || loadingPIC"
                                    class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg transition font-semibold shadow-md">
                                    <i class="fas fa-paper-plane mr-2"></i>Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function leaveForm() {
            return {
                // Form data
                employee_id: '{{ old('employee_id') ?? '' }}',
                person_incharge_id: '{{ old('person_incharge_id') ?? '' }}',
                leave_type_id: '{{ old('leave_type_id') ?? '' }}',
                leave_for: '{{ old('leave_for', 'full_day') }}',
                from_date: '{{ old('from_date') ?? '' }}',
                to_date: '{{ old('to_date') ?? '' }}',

                // Display
                leavePeriod: '',
                requestedDays: 0,
                totalBalance: 0,
                usedBalance: 0,
                remainingBalance: 0,
                showBalance: false,

                // PIC
                availablePIC: [],
                loadingPIC: false,

                // URLs
                balanceUrl: '{{ route('employee.leave.balance') }}',
                picUrl: '{{ route('employee.person.incharge.available') }}',

                init() {
                    this.calculate();
                    if (this.employee_id && this.from_date) {
                        this.loadPersonInCharge();
                    }
                    if (this.employee_id && this.leave_type_id) {
                        this.loadBalance();
                    }
                },

                onEmployeeOrDateChange() {
                    this.person_incharge_id = '';
                    this.loadPersonInCharge();
                    this.calculate();
                    if (this.employee_id && this.leave_type_id) {
                        this.loadBalance();
                    }
                },

                onDurationChange() {
                    if (this.leave_for === 'half_day') {
                        this.to_date = '';
                    }
                    this.onEmployeeOrDateChange();
                },

                async loadPersonInCharge() {
                    if (!this.employee_id || !this.from_date) {
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
                            to_date: this.to_date || this.from_date
                        });

                        const response = await fetch(`${this.picUrl}?${params}`);

                        if (!response.ok) {
                            const error = await response.text();
                            console.error('PIC API Error:', response.status, error);
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const data = await response.json();
                        this.availablePIC = data;
                    } catch (err) {
                        console.error('Failed to load Person In Charge:', err);
                        this.availablePIC = [];

                    } finally {
                        this.loadingPIC = false;
                    }
                },

                async loadBalance() {
                    if (!this.employee_id || !this.leave_type_id) {
                        this.showBalance = false;
                        return;
                    }

                    try {
                        const response = await fetch(`${this.balanceUrl}?employee_id=${this.employee_id}&leave_type_id=${this.leave_type_id}`);
                        if (!response.ok) throw new Error();
                        const data = await response.json();

                        this.totalBalance = parseFloat(data.total) || 0;
                        this.usedBalance = parseFloat(data.used) || 0;
                        this.remainingBalance = parseFloat(data.remaining) || 0;
                        this.showBalance = true;
                    } catch (err) {
                        console.error('Balance load error:', err);
                        this.showBalance = false;
                    }
                },

                calculate() {
                    this.leavePeriod = '';
                    this.requestedDays = 0;

                    if (!this.from_date) return;

                    if (this.leave_for === 'half_day') {
                        let typeText = '';
                        if (this.half_day_type === 'morning') {
                            typeText = ' (Morning)';
                        } else if (this.half_day_type === 'afternoon') {
                            typeText = ' (Afternoon)';
                        }
                        this.leavePeriod = '0.5 day' + typeText;
                        this.requestedDays = 0.5;
                        return;
                    }

                    if (!this.to_date) return;

                    if (this.to_date < this.from_date) {
                        this.leavePeriod = 'Invalid date range';
                        this.requestedDays = 0;
                        return;
                    }

                    const from = new Date(this.from_date);
                    const to = new Date(this.to_date);
                    const days = Math.floor((to - from) / 86400000) + 1;

                    this.requestedDays = days;
                    this.leavePeriod = days === 1 ? '1 day' : `${days} days`;
                },

                onSubmit(e) {
                    if (!this.person_incharge_id) {
                        e.preventDefault();
                        alert('Please select a Person In Charge.');
                    }
                }
            }
        }

        function appData() {
            return {
                sidebarOpen: false,
                ...leaveForm(),
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                }
            }
        }
    </script>
</body>

</html>