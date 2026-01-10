<!-- resources/views/employee/leaves/edit.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Leave Request • {{ Auth::user()->username }}</title>
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
        @include('layout.employeeSidebar')

        <div class="flex-1 flex flex-col">
            <header class="bg-white border-b shadow-sm z-10">
                <div class="px-4 md:px-8 py-4 flex justify-between items-center">
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">Edit Leave Request</h1>
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
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Balance Cards -->
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
                        <form method="POST" action="{{ route('employee.leaves.update', $leave->id) }}" @submit.prevent="onSubmit" class="space-y-8">
                            @csrf
                            @method('PUT')

                            <!-- Hidden employee_id -->
                            <input type="hidden" name="employee_id" value="{{ $leave->employee_id }}" x-model="employee_id">

                            <!-- Employee (readonly) -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Employee</label>
                                <input type="text" disabled value="{{ $leave->employee->employee_code }} - {{ $leave->employee->personalInfo?->full_name_en ?? 'N/A' }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-100">
                            </div>

                            <!-- Leave Type -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Leave Type <span class="text-red-600">*</span></label>
                                <select name="leave_type_id" x-model="leave_type_id" @change="onChange" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500">
                                    <option value="">-- Select Leave Type --</option>
                                    @foreach($leaveTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('leave_type_id', $leave->leave_type_id) == $type->id ? 'selected' : '' }}>
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
                                               @change="onFlowChange" class="w-5 h-5 text-indigo-600"
                                               {{ old('flow_type', $leave->flow_type ?? 'supervisor') === 'supervisor' ? 'checked' : '' }}>
                                        <span class="font-medium text-gray-700">Supervisor Approval</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="radio" name="flow_type" value="hr" x-model="flow_type"
                                               @change="onFlowChange" class="w-5 h-5 text-indigo-600"
                                               {{ old('flow_type', $leave->flow_type ?? 'supervisor') === 'hr' ? 'checked' : '' }}>
                                        <span class="font-medium text-gray-700">HR Approval</span>
                                    </label>
                                </div>
                            </div>

                            <!-- HR Selection -->
                            <div x-show="flow_type === 'hr'" x-transition>
                                <label class="block font-medium text-gray-700 mb-2">Select HR <span class="text-red-600">*</span></label>
                                <select name="hr_id" x-model="hr_id" :required="flow_type === 'hr'"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500">
                                    <option value="">-- Select HR --</option>
                                    @foreach($hrEmployees as $hr)
                                        <option value="{{ $hr->id }}"
                                            {{ old('hr_id', $leave->flow_type === 'hr' ? $leave->approver_id : '') == $hr->id ? 'selected' : '' }}>
                                            {{ $hr->employee_code }} - {{ $hr->personalInfo?->full_name_en ?? 'No Name' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Leave Duration -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-3">Leave For <span class="text-red-600">*</span></label>
                                <div class="space-y-4">
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="radio" name="leave_for" value="full_day" x-model="leave_for"
                                               @change="onChange" class="w-5 h-5 text-indigo-600"
                                               {{ old('leave_for', $leave->leave_for) === 'full_day' ? 'checked' : '' }}>
                                        <span class="font-medium text-gray-700">Full Day</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="radio" name="leave_for" value="half_day" x-model="leave_for"
                                               @change="onChange" class="w-5 h-5 text-indigo-600"
                                               {{ old('leave_for', $leave->leave_for) === 'half_day' ? 'checked' : '' }}>
                                        <span class="font-medium text-gray-700">Half Day (Last day half)</span>
                                    </label>

                                    <div x-show="leave_for === 'half_day'" x-transition class="ml-8 space-y-3">
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input type="radio" name="half_day_type" value="morning" x-model="half_day_type"
                                                   class="w-5 h-5 text-indigo-600"
                                                   {{ old('half_day_type', $leave->half_day_type) === 'morning' ? 'checked' : '' }}>
                                            <span class="text-gray-700">Morning</span>
                                        </label>
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input type="radio" name="half_day_type" value="afternoon" x-model="half_day_type"
                                                   class="w-5 h-5 text-indigo-600"
                                                   {{ old('half_day_type', $leave->half_day_type) === 'afternoon' ? 'checked' : '' }}>
                                            <span class="text-gray-700">Afternoon</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block font-medium text-gray-700 mb-2">From Date <span class="text-red-600">*</span></label>
                                    <input type="date" name="from_date" x-model="from_date" @change="onChange" required
                                           value="{{ old('from_date', $leave->from_date->format('Y-m-d')) }}">
                                </div>
                                <div>
                                    <label class="block font-medium text-gray-700 mb-2">To Date <span class="text-red-600">*</span></label>
                                    <input type="date" name="to_date" x-model="to_date" @change="onChange" required
                                           value="{{ old('to_date', $leave->to_date->format('Y-m-d')) }}">
                                </div>
                            </div>

                            <!-- Leave Days Summary -->
                            <div x-show="leavePeriod" x-transition class="mt-4">
                                <label class="block font-medium text-gray-700 mb-2">Leave Days</label>
                                <div class="bg-gray-50 border rounded-lg px-4 py-3">
                                    <span class="font-bold text-lg" x-text="leavePeriod"></span>
                                </div>
                            </div>

                            <!-- Balance Warning -->
                            <div x-show="showBalance && requestedDays > remainingBalance" x-transition
                                 class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
                                <strong>Insufficient Balance:</strong> Requesting <span x-text="requestedDays"></span> days,
                                but only <span x-text="remainingBalance"></span> remaining.
                            </div>

                            <!-- Subject, Reason, Remark -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Subject <span class="text-red-600">*</span></label>
                                <input type="text" name="subject" value="{{ old('subject', $leave->subject) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Reason <span class="text-red-600">*</span></label>
                                <textarea name="reason" rows="4" required
                                          class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500">{{ old('reason', $leave->reason) }}</textarea>
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Remark</label>
                                <textarea name="remark" rows="3"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500">{{ old('remark', $leave->remark) }}</textarea>
                            </div>

                            <!-- Person In Charge -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">Person In Charge <span class="text-red-600">*</span></label>
                                <select name="person_incharge_id" 
                                        x-model="person_incharge_id" 
                                        x-ref="picSelect"
                                        required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500">
                                    
                                    <option value="" disabled :hidden="person_incharge_id">
                                        -- Select Person In Charge --
                                    </option>

                                    <template x-for="emp in availablePIC" :key="emp.id">
                                        <option :value="emp.id" x-text="emp.text"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="flex justify-end gap-4 pt-8 border-t">
                                <a href="{{ route('employee.leaves.index') }}" class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                                    Cancel 
                                </a>
                                <button type="submit" 
                                        :disabled="!person_incharge_id || requestedDays <= 0"
                                        class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg transition font-semibold shadow-md">
                                    Update Request
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
                employee_id: '{{ $leave->employee_id }}',
                leave_type_id: '{{ old('leave_type_id', $leave->leave_type_id) }}',
                leave_for: '{{ old('leave_for', $leave->leave_for) }}',
                half_day_type: '{{ old('half_day_type', $leave->half_day_type ?? "") }}',
                from_date: '{{ old('from_date', $leave->from_date->format("Y-m-d")) }}',
                to_date: '{{ old('to_date', $leave->to_date->format("Y-m-d")) }}',

                flow_type: '{{ old("flow_type", $leave->flow_type ?? "supervisor") }}',
                hr_id: '{{ old("hr_id", $leave->flow_type === "hr" ? $leave->approver_id : "") }}',

                person_incharge_id: '{{ $leave->person_incharge_id }}',

                totalBalance: 0,
                usedBalance: 0,
                remainingBalance: 0,
                showBalance: false,
                requestedDays: 0,
                leavePeriod: '',
                availablePIC: [],

                balanceUrl: '{{ route("employee.leave.balance") }}',

                init() {
                    this.calculate();
                    this.loadBalance();

                    // Load and sort employees: current PIC first
                    this.availablePIC = @json(
                        $employees
                            ->sortBy(function ($emp) use ($leave) {
                                return $emp->id == $leave->person_incharge_id ? -1 : $emp->employee_code;
                            })
                            ->map(function ($emp) use ($leave) {
                                return [
                                    'id'   => $emp->id,
                                    'text' => $emp->employee_code . ' - ' .
                                             ($emp->personalInfo?->full_name_en 
                                              ?? $emp->personalInfo?->full_name_kh 
                                              ?? 'No Name') .
                                             ($emp->id == $leave->person_incharge_id ? ' (currently selected)' : '')
                                ];
                            })
                            ->values()
                            ->toArray()
                    );

                    // Set current value
                    this.person_incharge_id = '{{ $leave->person_incharge_id }}';

                    // Force native select to reflect value (important for dynamic options)
                    this.$nextTick(() => {
                        if (this.$refs.picSelect) {
                            this.$refs.picSelect.value = this.person_incharge_id;
                        }
                    });
                },

                onFlowChange() {
                    if (this.flow_type !== 'hr') {
                        this.hr_id = '';
                    }
                },

                onChange() {
                    this.calculate();
                    this.loadBalance();
                },

                async loadBalance() {
                    if (!this.employee_id || !this.leave_type_id) {
                        this.showBalance = false;
                        return;
                    }
                    try {
                        const res = await fetch(`${this.balanceUrl}?employee_id=${this.employee_id}&leave_type_id=${this.leave_type_id}`);
                        if (!res.ok) throw new Error();
                        const data = await res.json();
                        this.totalBalance = parseFloat(data.total) || 0;
                        this.usedBalance = parseFloat(data.used) || 0;
                        this.remainingBalance = parseFloat(data.remaining) || 0;
                        this.showBalance = true;
                    } catch (err) {
                        this.showBalance = false;
                    }
                },

                calculate() {
                    this.leavePeriod = '';
                    this.requestedDays = 0;
                    if (!this.from_date || !this.to_date) return;

                    const from = new Date(this.from_date);
                    const to = new Date(this.to_date);
                    if (to < from) {
                        this.leavePeriod = 'Invalid date range';
                        return;
                    }

                    const days = Math.floor((to - from) / 86400000) + 1;
                    if (this.leave_for === 'full_day') {
                        this.requestedDays = days;
                    } else {
                        this.requestedDays = days === 1 ? 0.5 : (days - 1) + 0.5;
                    }
                    this.leavePeriod = `${this.requestedDays} day${this.requestedDays !== 1 ? 's' : ''}`;
                },

                onSubmit() {
                    if (!this.person_incharge_id) {
                        alert('Please select Person In Charge.');
                        return false;
                    }
                    if (this.requestedDays <= 0) {
                        alert('Please select valid dates.');
                        return false;
                    }
                    if (this.flow_type === 'hr' && !this.hr_id) {
                        alert('Please select HR for HR approval flow.');
                        return false;
                    }
                    if (this.leave_for === 'half_day' && !this.half_day_type) {
                        alert('Please select Morning or Afternoon for half day.');
                        return false;
                    }

                    this.$el.submit();
                },
            }
        }

        function appData() {
            return {
                sidebarOpen: false,
                ...leaveForm(),
                toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; }
            }
        }
    </script>
</body>
</html>