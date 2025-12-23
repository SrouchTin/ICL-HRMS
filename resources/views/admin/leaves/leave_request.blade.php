<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Requests - HR Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-gray-100 font-sans antialiased">
<div x-data="{ sidebarOpen: false, notificationOpen: false }" class="flex h-screen overflow-hidden">

    @include('layout.adminSidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-8 py-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Leave Requests</h1>
                    <p class="text-gray-600 text-sm">Review and manage employee leave applications</p>
                </div>

                <!-- Notification Bell -->
                <div class="relative">
                    <button @click="notificationOpen = !notificationOpen; $event.stopPropagation()"
                            class="relative p-3 text-gray-600 hover:text-indigo-600 transition hover:bg-gray-100 rounded-full">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            {{ $pendingCount }}
                        </span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="notificationOpen" @click.away="notificationOpen = false" x-cloak
                         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="px-4 py-3 border-b border-gray-200 font-semibold text-gray-700">
                            Recent Pending Requests <span class="text-red-500">({{ $pendingCount }} new)</span>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            @forelse($recentLeaves as $leave)
                                <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                    <div class="flex items-start gap-3">
                                        <i class="fas fa-calendar-plus text-indigo-500 mt-1"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $leave->employee->personalInfo?->full_name_en ?? $leave->employee->username }}
                                            </p>
                                            <p class="text-xs text-gray-600">
                                                {{ $leave->leaveType?->name }} • {{ number_format($leave->leave_days, 1) }} day{{ $leave->leave_days != 1 ? 's' : '' }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $leave->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 py-8 text-sm">No pending requests</p>
                            @endforelse
                        </div>
                        <a href="{{ route('hr.leave.requests') }}" class="block text-center py-3 text-indigo-600 hover:bg-gray-50 text-sm font-medium">
                            View all requests
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-y-auto">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pending Requests</p>
                            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $pendingCount }}</p>
                        </div>
                        <i class="fas fa-clock text-4xl text-yellow-500 opacity-20"></i>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Approved This Month</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">{{ $approvedThisMonth }}</p>
                        </div>
                        <i class="fas fa-check-circle text-4xl text-green-500 opacity-20"></i>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Rejected This Month</p>
                            <p class="text-3xl font-bold text-red-600 mt-2">{{ $rejectedThisMonth }}</p>
                        </div>
                        <i class="fas fa-times-circle text-4xl text-red-500 opacity-20"></i>
                    </div>
                </div>
            </div>

            <!-- Filters & Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <form method="GET" action="{{ route('hr.leave.requests') }}" class="flex flex-col sm:flex-row gap-4">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search by username or employee code..."
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">

                        <select name="leave_type" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Leave Types</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ request('leave_type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            <i class="fas fa-search mr-2"></i> Search
                        </button>

                        @if(request()->hasAny(['search', 'leave_type']))
                            <a href="{{ route('hr.leave.requests') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                                Clear Filters
                            </a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($leaves as $leave)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="bg-gray-200 border-2 border-dashed rounded-full w-12 h-12 flex-shrink-0 overflow-hidden">
                                                <img src="{{ $leave->employee->image ? asset('storage/' . $leave->employee->image) : asset('images/default-avatar.png') }}" 
                                                    alt="Employee Avatar" 
                                                    class="w-full h-full object-cover">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $leave->employee->personalInfo?->full_name_en ?? $leave->employee->username }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $leave->employee->employee_code ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-medium rounded-full
                                            @switch($leave->leave_type_id)
                                                @case(1) bg-red-100 text-red-800 @break    <!-- Sick -->
                                                @case(2) bg-blue-100 text-blue-800 @break   <!-- Annual -->
                                                @case(3) bg-purple-100 text-purple-800 @break <!-- Special -->
                                                @case(4) bg-orange-100 text-orange-800 @break <!-- Unpaid -->
                                                @case(5) bg-teal-100 text-teal-800 @break    <!-- Compensate -->
                                                @case(6) bg-pink-100 text-pink-800 @break    <!-- Maternity -->
                                                @default bg-gray-100 text-gray-800
                                            @endswitch">
                                            {{ $leave->leaveType?->name ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <!-- From Date - Separate Column -->
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($leave->from_date)->format('d-m-Y') }}
                                        @if($leave->leave_days == 0.5 && $leave->from_date === $leave->to_date)
                                            <span class="text-orange-600 font-medium">(Half Day)</span>
                                        @endif
                                    </td>
                                    <!-- To Date - Separate Column -->
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($leave->to_date)->format('d-m-Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ number_format($leave->leave_days, 1) }} day{{ $leave->leave_days != 1 ? 's' : '' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $leave->reason }}">
                                        {{ $leave->reason ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm space-x-6">
                                        <!-- Approve Form -->
                                        <form action="{{ route('hr.leave.approve', $leave->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    onclick="return confirm('Approve this leave request?')"
                                                    class="text-green-600 hover:text-green-800 font-medium transition">
                                                Approve
                                            </button>
                                        </form>

                                        <!-- Reject Button -->
                                        <button onclick="openRejectModal({{ $leave->id }})"
                                                class="text-red-600 hover:text-red-800 font-medium transition">
                                            Reject
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-12 text-gray-500 text-lg">
                                        No pending leave requests found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $leaves->appends(request()->query())->links() }}
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Reject Leave Request</h3>
<form id="rejectForm" method="POST" action="">
    @csrf
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Reason for Rejection <span class="text-red-600">*</span>
        </label>
        <textarea name="reject_reason" id="rejectReasonTextarea" rows="4" required
                  class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                  placeholder="Please explain why this leave request is being rejected..."></textarea>
    </div>
    <div class="flex justify-end gap-4">
        <button type="button" onclick="closeRejectModal()"
                class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
            Cancel
        </button>
        <button type="submit"
                class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
            Reject Request
        </button>
    </div>
</form>
    </div>
</div>

<script>
function openRejectModal(leaveId) {
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectForm').action = `/hr/leave/${leaveId}/reject`;
        document.getElementById('rejectReasonTextarea').value = '';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    // Prevent modal close when clicking inside
    document.getElementById('rejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });

    // Optional: Client-side validation before submit
    document.getElementById('rejectForm').addEventListener('submit', function(e) {
        const reason = document.getElementById('rejectReasonTextarea').value.trim();
        if (!reason) {
            e.preventDefault();
            alert('Please provide a reason for rejection.');
        }
    });
</script>

</body>
</html>