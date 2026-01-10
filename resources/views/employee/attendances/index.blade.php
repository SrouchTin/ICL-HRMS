<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendance | Employee Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/toast/css.css') }}" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .btn-disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

<div x-data="attendanceApp()" x-init="init()" class="flex h-screen">
    <!-- Sidebar -->
    @include('layout.employeeSidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        @include('toastify.toast')
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-4 lg:px-8 py-4">
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h1 class="text-xl lg:text-2xl font-semibold text-gray-800">My Attendance</h1>
                        <p class="text-xs text-gray-500 mt-1">Track your daily attendance and working hours</p>
                    </div>
                </div>
                <div class="hidden md:block text-sm text-gray-600 bg-gray-50 px-4 py-2 rounded-lg border border-gray-200">
                    <i class="fas fa-calendar-day mr-2 text-indigo-600"></i>
                    <span x-text="currentDate"></span>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 overflow-y-auto p-4 lg:p-8">
            <!-- Alert Messages -->
            <div x-show="alert.show" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2"
                 :class="alert.type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'"
                 class="mb-6 p-4 rounded-lg border flex items-start justify-between">
                <div class="flex items-center">
                    <i :class="alert.type === 'success' ? 'fa-check-circle text-green-500' : 'fa-exclamation-circle text-red-500'" 
                       class="fas mr-3 text-xl"></i>
                    <span x-text="alert.message"></span>
                </div>
                <button @click="alert.show = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Quick Check In/Out Section -->
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-xl shadow-lg p-6 mb-8 text-white">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="mb-4 md:mb-0">
                        <h3 class="text-2xl font-bold mb-2">Quick Action</h3>
                        <p class="text-indigo-100 text-sm">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span x-show="!todayAttendance.check_in">Start your workday by checking in</span>
                            <span x-show="todayAttendance.check_in && !todayAttendance.check_out">Check out when you finish work</span>
                            <span x-show="todayAttendance.check_in && todayAttendance.check_out">You've completed today's attendance</span>
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div x-show="todayAttendance.check_in" 
                             x-transition
                             class="text-right mr-4">
                            <p class="text-xs text-indigo-200">Checked In</p>
                            <p class="text-xl font-bold" x-text="todayAttendance.check_in || '-'"></p>
                        </div>
                        <button @click="handleCheckInOut()" 
                                :disabled="loading || (todayAttendance.check_in && todayAttendance.check_out)"
                                :class="[
                                    todayAttendance.check_in && todayAttendance.check_out 
                                        ? 'bg-gray-400 cursor-not-allowed' 
                                        : 'bg-white hover:bg-gray-100',
                                    loading ? 'btn-disabled' : ''
                                ]"
                                class="px-8 py-3 rounded-lg font-semibold transition shadow-lg flex items-center space-x-2">
                            <i :class="loading ? 'fa-spinner fa-spin' : (todayAttendance.check_in && !todayAttendance.check_out ? 'fa-sign-out-alt' : (todayAttendance.check_in && todayAttendance.check_out ? 'fa-check' : 'fa-sign-in-alt'))" 
                               class="fas text-indigo-600"></i>
                            <span class="text-indigo-600" x-text="getButtonText()"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Monthly Statistics -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-bar mr-2 text-indigo-600"></i>
                    Monthly Overview - <span x-text="selectedMonthLabel" class="ml-2 text-indigo-600"></span>
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                    <!-- Working Days -->
                    <div class="stat-card bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Working Days</p>
                                <p class="text-3xl font-bold text-blue-600 mt-2" x-text="stats.workingDays"></p>
                                <p class="text-xs text-gray-500 mt-1">Total days with attendance</p>
                            </div>
                            <div class="bg-blue-100 p-4 rounded-full">
                                <i class="fas fa-calendar-check text-3xl text-blue-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Late Arrivals -->
                    <div class="stat-card bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Late Arrivals</p>
                                <p class="text-3xl font-bold text-yellow-600 mt-2" x-text="stats.lateArrivals"></p>
                                <p class="text-xs text-gray-500 mt-1">Times checked in late</p>
                            </div>
                            <div class="bg-yellow-100 p-4 rounded-full">
                                <i class="fas fa-clock text-3xl text-yellow-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Early Departures -->
                    <div class="stat-card bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Early Departures</p>
                                <p class="text-3xl font-bold text-orange-600 mt-2" x-text="stats.earlyDepartures"></p>
                                <p class="text-xs text-gray-500 mt-1">Times left early</p>
                            </div>
                            <div class="bg-orange-100 p-4 rounded-full">
                                <i class="fas fa-sign-out-alt text-3xl text-orange-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- On Leave -->
                    <div class="stat-card bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 font-medium">On Leave</p>
                                <p class="text-3xl font-bold text-purple-600 mt-2" x-text="stats.onLeave"></p>
                                <p class="text-xs text-gray-500 mt-1">Days on approved leave</p>
                            </div>
                            <div class="bg-purple-100 p-4 rounded-full">
                                <i class="fas fa-umbrella-beach text-3xl text-purple-600"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Records Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 lg:px-6 py-4 border-b border-gray-200 flex flex-col lg:flex-row items-start lg:items-center justify-between space-y-3 lg:space-y-0">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-table mr-2 text-indigo-600"></i>
                            Attendance Records
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Detailed view of your daily attendance</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <form method="GET" action="{{ route('employee.attendance.index') }}">
                            <select name="month" 
                                    onchange="this.form.submit()"
                                    class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                @foreach ($months as $m)
                                    <option value="{{ $m['value'] }}" {{ $month == $m['value'] ? 'selected' : '' }}>
                                        {{ $m['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        <button @click="exportAttendance()" 
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition flex items-center space-x-2">
                            <i class="fas fa-download"></i>
                            <span class="hidden sm:inline">Export</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700 uppercase text-xs border-b border-gray-200">
                            <tr>
                                <th class="px-4 lg:px-6 py-4 font-semibold">Date</th>
                                <th class="px-4 lg:px-6 py-4 font-semibold">Day</th>
                                <th class="px-4 lg:px-6 py-4 font-semibold">Shift</th>
                                <th class="px-4 lg:px-6 py-4 font-semibold">Check In</th>
                                <th class="px-4 lg:px-6 py-4 font-semibold">Check Out</th>
                                <th class="px-4 lg:px-6 py-4 font-semibold">Working Hours</th>
                                <th class="px-4 lg:px-6 py-4 font-semibold">Status</th>
                                <th class="px-4 lg:px-6 py-4 font-semibold">Remark / Overtime</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($attendances as $attendance)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 lg:px-6 py-4 font-medium text-gray-900">
                                        {{ $attendance->date->format('Y-m-d') }}
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 text-gray-600">
                                        {{ $attendance->date->format('l') }}
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 text-gray-600">
                                        @if ($attendance->shift)
                                            <div class="text-xs">
                                                <div class="font-medium">{{ $attendance->shift->name }}</div>
                                                <div class="text-gray-500">
                                                    {{ $attendance->shift->start_time }} - {{ $attendance->shift->end_time }}
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 font-medium">
                                        @if ($attendance->check_in)
                                            <div class="flex flex-col">
                                                <span class="{{ $attendance->come_late ? 'text-yellow-600' : ($attendance->come_early ? 'text-blue-600' : 'text-green-600') }}">
                                                    {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}
                                                </span>
                                                @if ($attendance->come_late && $attendance->shift)
                                                    @php
                                                        $shiftStart = \Carbon\Carbon::parse($attendance->shift->start_time);
                                                        $checkInTime = \Carbon\Carbon::parse($attendance->check_in);
                                                        $lateMinutes = $shiftStart->diffInMinutes($checkInTime);
                                                    @endphp
                                                    <span class="text-xs text-yellow-600 mt-1">
                                                        <i class="fas fa-clock mr-1"></i>Late {{ $lateMinutes }}mn
                                                    </span>
                                                @elseif ($attendance->come_early && $attendance->shift)
                                                    @php
                                                        $shiftStart = \Carbon\Carbon::parse($attendance->shift->start_time);
                                                        $checkInTime = \Carbon\Carbon::parse($attendance->check_in);
                                                        $earlyMinutes = $checkInTime->diffInMinutes($shiftStart);
                                                    @endphp
                                                    <span class="text-xs text-blue-600 mt-1">
                                                        <i class="fas fa-arrow-up mr-1"></i>Early {{ $earlyMinutes }}mn
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 font-medium">
                                        @if ($attendance->check_out)
                                            <div class="flex flex-col">
                                                <span class="{{ $attendance->leave_early ? 'text-orange-600' : ($attendance->leave_late ? 'text-indigo-600' : 'text-green-600') }}">
                                                    {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}
                                                </span>
                                                @if ($attendance->leave_early && $attendance->shift)
                                                    @php
                                                        $shiftEnd = \Carbon\Carbon::parse($attendance->shift->end_time);
                                                        $checkOutTime = \Carbon\Carbon::parse($attendance->check_out);
                                                        $earlyMinutes = $checkOutTime->diffInMinutes($shiftEnd);
                                                    @endphp
                                                    <span class="text-xs text-orange-600 mt-1">
                                                        <i class="fas fa-sign-out-alt mr-1"></i>Early {{ $earlyMinutes }}mn
                                                    </span>
                                                @elseif ($attendance->leave_late && $attendance->shift)
                                                    @php
                                                        $shiftEnd = \Carbon\Carbon::parse($attendance->shift->end_time);
                                                        $checkOutTime = \Carbon\Carbon::parse($attendance->check_out);
                                                        $lateMinutes = $shiftEnd->diffInMinutes($checkOutTime);
                                                    @endphp
                                                    <span class="text-xs text-indigo-600 mt-1">
                                                        <i class="fas fa-arrow-down mr-1"></i>Overtime {{ $lateMinutes }}mn
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 text-gray-700 font-medium">
                                        @if ($attendance->check_in && $attendance->check_out)
                                            @php
                                                $start = \Carbon\Carbon::parse($attendance->check_in);
                                                $end = \Carbon\Carbon::parse($attendance->check_out);
                                                $hours = $end->diffInMinutes($start) / 60;
                                            @endphp
                                            {{ number_format($hours, 1) }} hrs
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-6 py-4">
                                        @if ($attendance->holiday_id)
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center bg-blue-100 text-blue-800">
                                                <i class="fas fa-circle text-xs mr-1 text-blue-500"></i>
                                                Holiday
                                            </span>
                                        @elseif ($attendance->leave_id)
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center bg-purple-100 text-purple-800">
                                                <i class="fas fa-umbrella-beach mr-1"></i>
                                                {{ $attendance->leave->leave_type->name ?? 'On Leave' }}
                                            </span>
                                        @elseif (!$attendance->is_working_day)
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center bg-gray-100 text-gray-800">
                                                <i class="fas fa-circle text-xs mr-1 text-gray-500"></i>
                                                Day Off
                                            </span>
                                        @elseif ($attendance->check_in && $attendance->check_out)
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center bg-green-100 text-green-800">
                                                <i class="fas fa-circle text-xs mr-1 text-green-500"></i>
                                                Complete
                                            </span>
                                        @elseif ($attendance->check_in)
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-circle text-xs mr-1 text-yellow-500"></i>
                                                In Progress
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-6 py-4">
                                        @if ($attendance->leave_id)
                                            <span class="text-gray-600 text-xs italic">
                                                {{ $attendance->remark ?? 'On approved leave' }}
                                            </span>
                                        @elseif ($attendance->overtime_minutes > 0)
                                            <span class="text-indigo-600 font-medium">
                                                +{{ $attendance->overtime_minutes }} min
                                            </span>
                                        @elseif ($attendance->remark)
                                            <span class="text-gray-600 text-xs">{{ $attendance->remark }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                                            <p class="text-gray-500 font-medium">No attendance records found for this month</p>
                                            <p class="text-gray-400 text-sm mt-1">Your attendance will appear here once you check in</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer with Summary -->
                @if($attendances->count() > 0)
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col lg:flex-row items-start lg:items-center justify-between space-y-2 lg:space-y-0">
                    <p class="text-sm text-gray-600">
                        Showing <span class="font-semibold text-gray-900">{{ $attendances->count() }}</span> records for 
                        <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($month)->format('F Y') }}</span>
                    </p>
                    <div class="flex items-center space-x-4 text-sm">
                        <div class="flex items-center space-x-2">
                            <span class="text-gray-600">Total Hours:</span>
                            <span class="font-semibold text-indigo-600">
                                @php
                                    $totalMinutes = $attendances->where('check_in', '!=', null)->where('check_out', '!=', null)->sum(function($att) {
                                        $start = \Carbon\Carbon::parse($att->check_in);
                                        $end = \Carbon\Carbon::parse($att->check_out);
                                        return $end->diffInMinutes($start);
                                    });
                                    echo number_format($totalMinutes / 60, 1);
                                @endphp hrs
                            </span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-gray-600">Total Overtime:</span>
                            <span class="font-semibold text-indigo-600">
                                {{ number_format($attendances->sum('overtime_minutes') / 60, 1) }} hrs
                            </span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </main>
    </div>
</div>
<script src="{{ asset('assets/toast/script.js') }}"></script>
<script>
    function attendanceApp() {
        return {
            loading: false,
            sidebarOpen: false,
            currentDate: '',
            selectedMonth: '{{ $month }}',
            selectedMonthLabel: '',
            todayAttendance: @json($attendances->firstWhere('date', \Carbon\Carbon::today()) ?? ['check_in' => null, 'check_out' => null]),
            stats: {
                workingDays: {{ $attendances->where('check_in', '!=', null)->count() }},
                lateArrivals: {{ $lateArrivals }},
                earlyDepartures: {{ $earlyDepartures }},
                onLeave: {{ $attendances->where('leave_id', '!=', null)->count() }}
            },
            alert: {
                show: false,
                type: 'success',
                message: ''
            },

            init() {
                this.currentDate = dayjs().format('dddd, MMMM D, YYYY');
                this.selectedMonthLabel = dayjs(this.selectedMonth).format('MMMM YYYY');
                this.checkSessionAlert();
            },

            checkSessionAlert() {
                @if(session('success'))
                    this.showAlert('success', {!! json_encode(session('success')) !!});
                @endif
                @if(session('error'))
                    this.showAlert('error', {!! json_encode(session('error')) !!});
                @endif
            },

            getButtonText() {
                if (this.loading) {
                    return 'Processing...';
                }
                
                if (this.todayAttendance.check_in && this.todayAttendance.check_out) {
                    return 'Completed';
                }
                
                if (this.todayAttendance.check_in && !this.todayAttendance.check_out) {
                    return 'Check Out';
                }
                
                return 'Check In';
            },

            async handleCheckInOut() {
                if (this.loading) {
                    return;
                }

                this.loading = true;

                try {
                    const endpoint = !this.todayAttendance.check_in 
                        ? '{{ route("employee.attendance.checkIn") }}'
                        : '{{ route("employee.attendance.checkOut") }}';

                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        if (data.check_in) {
                            this.todayAttendance.check_in = data.check_in;
                            this.showAlert('success', `Successfully checked in at ${data.check_in}`);
                        } else if (data.check_out) {
                            this.todayAttendance.check_out = data.check_out;
                            let message = `Successfully checked out at ${data.check_out}`;
                            if (data.overtime_minutes && data.overtime_minutes > 0) {
                                message += ` (Overtime: ${data.overtime_minutes} minutes)`;
                            }
                            this.showAlert('success', message);
                        }
                        
                        // Reload page after 1.5 seconds to refresh data
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        // Show the actual error message from the server
                        this.showAlert('error', data.error || data.message || 'An error occurred');
                        console.error('Server response:', data);
                    }
                } catch (error) {
                    console.error('Request error:', error);
                    this.showAlert('error', 'Network error. Please check your connection and try again.');
                } finally {
                    this.loading = false;
                }
            },

            showAlert(type, message) {
                this.alert = { show: true, type, message };
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    this.alert.show = false;
                }, 5000);
            },

            exportAttendance() {
                const url = '{{ route("employee.attendance.export") }}?month=' + this.selectedMonth;
                window.location.href = url;
            }
        }
    }
</script>

</body>
</html>