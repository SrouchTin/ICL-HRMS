<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Leave • {{ Auth::user()->username }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>[x-cloak]{display:none!important}</style>
</head>

<body class="bg-gray-100">

<div
    x-data="{
        sidebarOpen: false,
        ...leaveForm()
    }"
    x-init="init()"
    class="flex h-screen overflow-hidden"
>

    {{-- Responsive Sidebar --}}
    @include('layout.employeeSidebar')

    <div class="flex-1 flex flex-col">

        <!-- Header -->
        <header class="bg-white border-b shadow-sm z-10">
            <div class="px-4 md:px-8 py-4 flex justify-between items-center">
                <h1 class="text-xl md:text-2xl font-bold text-gray-800">
                    Create Leave Request
                </h1>
                <a href="{{ route('employee.leaves.index') }}"
                   class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-4 md:p-8 bg-gray-50">
            <div class="max-w-4xl mx-auto">

                <!-- Success / Error Messages -->
                @if (session('success'))
                    <div class="bg-green-100 border border-green-300 text-green-700 p-4 rounded-lg mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-lg mb-6">
                        <ul class="list-disc pl-6 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Balance Cards - ON TOP -->
                <div x-show="employee_id && leave_type_id" x-cloak
                     class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                    <template x-if="isLoadingBalance">
                        <div class="col-span-3 text-center py-10 bg-white rounded-xl shadow">
                            <i class="fas fa-spinner fa-spin text-indigo-600 text-4xl"></i>
                            <p class="text-gray-600 mt-4 text-lg">Loading leave balance...</p>
                        </div>
                    </template>

                    <template x-if="!isLoadingBalance">
                        <div class="bg-white p-6 rounded-xl shadow-md text-center">
                            <p class="text-sm text-gray-600 uppercase tracking-wider">Total Leave</p>
                            <p class="text-5xl font-bold text-gray-800 mt-3" x-text="totalBalance"></p>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-md text-center">
                            <p class="text-sm text-gray-600 uppercase tracking-wider">Used Up To</p>
                            <p class="text-5xl font-bold text-orange-600 mt-3" x-text="usedBalance"></p>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-md text-center">
                            <p class="text-sm text-gray-600 uppercase tracking-wider">Remaining</p>
                            <p class="text-5xl font-bold text-green-600 mt-3" x-text="remainingBalance"></p>
                        </div>
                    </template>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
                    <form method="POST" action="{{ route('employee.leaves.store') }}" class="space-y-8">
                        @csrf

                        <!-- Employee -->
                        <div>
                            <label class="block font-medium text-gray-700 mb-2">
                                Employee <span class="text-red-600">*</span>
                            </label>
                            <select name="employee_id"
                                    x-model="employee_id"
                                    @change="fetchBalance(); calculate()"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Select Employee --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->employee_code }} - {{ $emp->personalInfo?->full_name_en ?? 'No Name' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Leave Type -->
                        <div>
                            <label class="block font-medium text-gray-700 mb-2">
                                Leave Type <span class="text-red-600">*</span>
                            </label>
                            <select name="leave_type_id"
                                    x-model="leave_type_id"
                                    @change="fetchBalance(); calculate()"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Select Leave Type --</option>
                                @foreach($leaveTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Leave For -->
                        <div>
                            <label class="block font-medium text-gray-700 mb-3">
                                Leave For <span class="text-red-600">*</span>
                            </label>
                            <div class="flex gap-10">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="leave_for" value="full_day"
                                           x-model="leave_for" @change="calculate()"
                                           class="w-5 h-5 text-indigo-600">
                                    <span class="font-medium text-gray-700">Full Day</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="leave_for" value="half_day"
                                           x-model="leave_for" @change="calculate()"
                                           class="w-5 h-5 text-indigo-600">
                                    <span class="font-medium text-gray-700">Half Day</span>
                                </label>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-medium text-gray-700 mb-2">
                                    From Date <span class="text-red-600">*</span>
                                </label>
                                <input type="date" name="from_date" x-model="from_date"
                                       @change="calculate()" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block font-medium text-gray-700 mb-2">
                                    To Date
                                    <span x-show="leave_for === 'full_day'" class="text-red-600">*</span>
                                </label>
                                <input type="date" x-model="to_date" @change="calculate()"
                                       :disabled="leave_for === 'half_day'"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 disabled:bg-gray-100 focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <!-- Always send to_date (for half_day) -->
                        <input type="hidden" name="to_date" :value="to_date">

                        <!-- Leave Period -->
                        <div>
                            <label class="block font-medium text-gray-700 mb-2">Leave Period</label>
                            <input type="text" x-model="leavePeriod" readonly
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-100 font-medium">
                        </div>

                        <!-- Reason -->
                        <div>
                            <label class="block font-medium text-gray-700 mb-2">
                                Reason <span class="text-red-600">*</span>
                            </label>
                            <textarea name="reason" rows="4" required placeholder="Enter reason for leave..."
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500">{{ old('reason') }}</textarea>
                        </div>

                        <!-- Remark -->
                        <div>
                            <label class="block font-medium text-gray-700 mb-2">Remark (Optional)</label>
                            <textarea name="remark" rows="3" placeholder="Any additional notes..."
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500">{{ old('remark') }}</textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end gap-4 pt-8 border-t">
                            <a href="{{ route('employee.leaves.index') }}"
                               class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                                Cancel
                            </a>
                            <button type="submit"
                                    :disabled="!employee_id || !leave_type_id || requestedDays > remainingBalance || requestedDays <= 0 || isLoadingBalance"
                                    class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg transition font-semibold shadow-md">
                                Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Alpine.js Script - Professional & Bug-Free -->
<script>
    // Use correct route name - make sure this matches your routes/web.php
    const balanceUrl = '{{ route('employee.leave.balance') }}';

    function leaveForm() {
        return {
            employee_id: '{{ old('employee_id') }}',
            leave_type_id: '{{ old('leave_type_id') }}',
            leave_for: '{{ old('leave_for', 'full_day') }}',
            from_date: '{{ old('from_date') }}',
            to_date: '{{ old('to_date') }}',

            leavePeriod: '0 day',
            requestedDays: 0,
            totalBalance: 0,
            usedBalance: 0,
            remainingBalance: 0,
            isLoadingBalance: false,

            init() {
                if (this.employee_id && this.leave_type_id) {
                    this.fetchBalance();
                }
                this.calculate();
            },

            async fetchBalance() {
                if (!this.employee_id || !this.leave_type_id) {
                    this.resetBalance();
                    return;
                }

                this.isLoadingBalance = true;

                try {
                    const url = `${balanceUrl}?employee_id=${this.employee_id}&leave_type_id=${this.leave_type_id}`;
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const data = await response.json();

                    this.totalBalance = parseFloat(data.total) || 0;
                    this.usedBalance = parseFloat(data.used) || 0;
                    this.remainingBalance = parseFloat(data.remaining) || 0;
                } catch (error) {
                    console.error('Failed to load balance:', error);
                    this.resetBalance();
                } finally {
                    this.isLoadingBalance = false;
                    this.calculate();
                }
            },

            resetBalance() {
                this.totalBalance = this.usedBalance = this.remainingBalance = 0;
            },

            calculate() {
                if (!this.from_date) {
                    this.leavePeriod = '0 day';
                    this.requestedDays = 0;
                    return;
                }

                if (this.leave_for === 'half_day') {
                    this.to_date = this.from_date;
                    this.leavePeriod = '0.5 day';
                    this.requestedDays = 0.5;
                    return;
                }

                if (!this.to_date) {
                    this.leavePeriod = 'Select to date';
                    this.requestedDays = 0;
                    return;
                }

                if (this.to_date < this.from_date) {
                    this.leavePeriod = 'Invalid date range';
                    this.requestedDays = 0;
                    return;
                }

                const from = new Date(this.from_date);
                const to = new Date(this.to_date);
                const days = Math.floor((to - from) / (86400000)) + 1;

                this.leavePeriod = days === 1 ? '1 day' : `${days} days`;
                this.requestedDays = days;
            }
        };
    }
</script>

</body>
</html>