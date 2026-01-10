<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Attendance Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/toast/css.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body, input, select, button, table, th, td {
            font-family: 'Noto Sans Khmer', 'Khmer OS', sans-serif !important;
        }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-6px); box-shadow: 0 20px 30px rgba(0,0,0,0.08); }
        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e5e7eb;
            flex-shrink: 0;
        }
        .avatar-placeholder {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
            border: 2px solid #e5e7eb;
            flex-shrink: 0;
        }
        .no-wrap {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .text-wrap {
            word-break: break-word;
        }
    </style>
</head>
<body class="bg-gray-100 antialiased" x-data="hrAttendanceApp()" x-init="init()">

<div class="flex h-screen overflow-hidden bg-gray-50">
    @include('layout.hrSidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('toastify.toast')

        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-6 py-5 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Daily Attendance Management</h1>
                    <p class="text-sm text-gray-500 mt-1">Monitor and manage employee attendance</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-semibold text-indigo-600" x-text="formattedDate"></p>
                    <p class="text-xs text-gray-500">Phnom Penh, Cambodia</p>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 lg:p-6">
            <!-- Alert -->
            <div x-show="alert.show" x-cloak x-transition
                 :class="alert.type === 'success' ? 'bg-green-50 border-green-300 text-green-800' : 'bg-red-50 border-red-300 text-red-800'"
                 class="mb-6 p-4 rounded-xl border flex items-center justify-between shadow-sm">
                <div class="flex items-center">
                    <i :class="alert.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'" class="fas mr-3 text-xl"></i>
                    <span x-text="alert.message" class="font-medium"></span>
                </div>
                <button @click="alert.show = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Day Off Notice -->
            @if(isset($isDayOff) && $isDayOff)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-indigo-500 p-5 mb-6 rounded-xl shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="bg-indigo-100 p-3 rounded-full">
                            <i class="fas fa-calendar-times text-indigo-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-indigo-900">
                            Day Off - {{ $dayName }}
                            @if(isset($holiday))
                                <span class="text-sm font-normal text-indigo-700">({{ $holiday->holiday_name }})</span>
                            @endif
                        </h3>
                        <p class="text-sm text-indigo-700 mt-1">
                            @if(isset($holiday))
                                Public Holiday: {{ $holiday->holiday_name }}. Only employees on approved leave are shown below.
                            @else
                                This is a non-working day. Only employees on approved leave are shown below.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Filters</h3>
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" name="date" value="{{ $selectedDate }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                        <select name="department" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Status</option>
                            <option value="present" {{ request('status')=='present'?'selected':'' }}>Present</option>
                            <option value="absent" {{ request('status')=='absent'?'selected':'' }}>Absent</option>
                            <option value="late" {{ request('status')=='late'?'selected':'' }}>Late</option>
                            <option value="on_leave" {{ request('status')=='on_leave'?'selected':'' }}>On Leave</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name / Code"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 text-sm font-medium flex items-center gap-2 transition">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('hr.attendance.export', ['date' => $selectedDate]) }}"
                           class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 text-sm font-medium flex items-center gap-2 transition">
                            <i class="fas fa-download"></i> Export
                        </a>
                    </div>
                </form>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
                <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Employees</p>
                            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $allEmployees->count() }}</p>
                        </div>
                        <div class="bg-blue-100 p-4 rounded-full">
                            <i class="fas fa-users text-2xl text-blue-600"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Present</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">{{ $todayStats['present'] }}</p>
                        </div>
                        <div class="bg-green-100 p-4 rounded-full">
                            <i class="fas fa-check-circle text-2xl text-green-600"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Absent</p>
                            <p class="text-3xl font-bold text-red-600 mt-2">{{ $todayStats['absent'] }}</p>
                        </div>
                        <div class="bg-red-100 p-4 rounded-full">
                            <i class="fas fa-times-circle text-2xl text-red-600"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Late</p>
                            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $todayStats['late'] }}</p>
                        </div>
                        <div class="bg-yellow-100 p-4 rounded-full">
                            <i class="fas fa-clock text-2xl text-yellow-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">Attendance Records</h2>
                    <span class="text-sm text-gray-600">Total: <strong>{{ $attendances->count() }}</strong> records</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700 uppercase text-xs border-b">
                            <tr>
                                <th class="px-4 py-4 text-left" style="width: 60px;">No</th>
                                <th class="px-4 py-4 text-left" style="min-width: 220px;">Employee</th>
                                <th class="px-4 py-4 text-left" style="min-width: 140px;">Department</th>
                                <th class="px-4 py-4 text-left" style="min-width: 150px;">Shift</th>
                                <th class="px-4 py-4 text-left" style="width: 100px;">Check In</th>
                                <th class="px-4 py-4 text-left" style="width: 100px;">Check Out</th>
                                <th class="px-4 py-4 text-left" style="width: 130px;">Status</th>
                                <th class="px-4 py-4 text-left" style="min-width: 180px;">Remark</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($attendances as $index => $att)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-4 text-gray-600 text-center">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($att->employee->image && file_exists(public_path('storage/' . $att->employee->image)))
                                                <img src="{{ asset('storage/' . $att->employee->image) }}" alt="Profile" class="avatar">
                                            @else
                                                <div class="avatar-placeholder">
                                                    {{ strtoupper(substr($att->employee->personalInfo?->full_name_en ?? $att->employee->employee_code ?? 'NA', 0, 2)) }}
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <div class="font-semibold text-gray-900 truncate">
                                                    {{ $att->employee->personalInfo?->full_name_en ?? 'Unknown Employee' }}
                                                </div>
                                                <div class="text-xs text-gray-500 no-wrap">ID: {{ $att->employee->employee_code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-gray-600">
                                        <span class="no-wrap">{{ $att->employee->department?->department_name ?? '-' }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-gray-600 text-xs">
                                        @if($att->shift)
                                            <div class="font-medium no-wrap">{{ $att->shift->name }}</div>
                                            <div class="text-gray-500 no-wrap">{{ $att->shift->start_time }} - {{ $att->shift->end_time }}</div>
                                        @else
                                            <span class="text-gray-400">No Shift</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($att->leave_id)
                                            <span class="text-gray-400 italic text-xs">On Leave</span>
                                        @elseif($att->check_in)
                                            <div class="font-medium no-wrap {{ $att->come_late ? 'text-red-600' : 'text-green-600' }}">
                                                {{ \Carbon\Carbon::parse($att->check_in)->format('H:i') }}
                                            </div>
                                            @if($att->come_late)
                                                <span class="text-xs text-red-600">Late</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($att->leave_id)
                                            <span class="text-gray-400 italic text-xs">On Leave</span>
                                        @elseif($att->check_out)
                                            <div class="font-medium text-green-600 no-wrap">
                                                {{ \Carbon\Carbon::parse($att->check_out)->format('H:i') }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($att->leave_id)
                                            <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 no-wrap inline-block">On Leave</span>
                                        @elseif(!$att->check_in)
                                            <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-red-100 text-red-800 no-wrap inline-block">Absent</span>
                                        @elseif($att->check_in && !$att->check_out)
                                            <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 no-wrap inline-block">In Progress</span>
                                        @else
                                            <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800 no-wrap inline-block">
                                                {{ $att->come_late ? 'Present (Late)' : 'Present' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($att->leave_id && $att->leave)
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded text-xs font-semibold no-wrap
                                                        @switch($att->leave->leave_type_id)
                                                            @case(1) bg-red-100 text-red-700 @break
                                                            @case(2) bg-blue-100 text-blue-700 @break
                                                            @case(3) bg-purple-100 text-purple-700 @break
                                                            @case(4) bg-orange-100 text-orange-700 @break
                                                            @case(5) bg-amber-100 text-amber-700 @break
                                                            @case(6) bg-pink-100 text-pink-700 @break
                                                            @default bg-gray-100 text-gray-700
                                                        @endswitch"
                                                        title="{{ $att->leave->leaveType?->name ?? 'Unknown' }}">
                                                        @php
                                                            $leaveTypes = [
                                                                1 => 'SL',  2 => 'AL',  3 => 'SPL',
                                                                4 => 'UL',  5 => 'CL',  6 => 'ML',
                                                            ];
                                                            echo $leaveTypes[$att->leave->leave_type_id] ?? 'N/A';
                                                        @endphp
                                                    </span>
                                                    <span class="text-xs text-gray-700 no-wrap">
                                                        {{ ucfirst(str_replace('_', ' ', $att->leave->duration_type ?? 'Full Day')) }}
                                                    </span>
                                                </div>
                                                @if($att->leave->reason)
                                                    <div class="text-xs text-gray-600" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $att->leave->reason }}">
                                                        {{ Str::limit($att->leave->reason, 40) }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-16 text-gray-500">
                                        <i class="fas fa-users-slash text-6xl mb-4 text-gray-300"></i>
                                        <p class="text-lg font-medium">No records found</p>
                                        @if(isset($isDayOff) && $isDayOff)
                                            <p class="text-sm mt-2">This is a day off. No attendance records to display.</p>
                                        @else
                                            <p class="text-sm mt-2">Try adjusting your filters</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="{{ asset('assets/toast/script.js') }}"></script>
<script>
function hrAttendanceApp() {
    return {
        formattedDate: dayjs('{{ $selectedDate }}').format('dddd, DD MMMM YYYY'),
        todayStats: @json($todayStats),
        alert: { show: false, type: '', message: '' },

        init() {
            this.checkSessionAlert();
        },

        checkSessionAlert() {
            @if(session('success'))
                this.showAlert('success', '{{ session('success') }}');
            @endif
            @if(session('error'))
                this.showAlert('error', '{{ session('error') }}');
            @endif
        },

        showAlert(type, message) {
            this.alert = { show: true, type, message };
            setTimeout(() => this.alert.show = false, 5000);
        }
    }
}
</script>
</body>
</html>