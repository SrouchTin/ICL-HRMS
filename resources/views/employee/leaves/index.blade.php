<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Leaves • {{ Auth::user()->username }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="{{ asset('assets/toast/css.css') }}" rel="stylesheet">
    <style>
        [x-cloak] {
            display: none !important;
        }

        .no-wrap {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .disabled-icon {
            @apply text-gray-300 cursor-not-allowed;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen font-sans antialiased">

    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

        @include('layout.employeeSidebar')

        <div class="flex-1 flex flex-col overflow-hidden">
            @include('toastify.toast')
            <!-- Header -->
            <header class="bg-white/80 backdrop-blur-lg border-b border-slate-200">
                <div class="px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-slate-900">My Leaves</h1>
                            <p class="text-sm text-slate-500 mt-1">Manage your time off requests</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('employee.dashboard') }}"
                                class="px-4 py-2.5 bg-indigo-600 text-white hover:bg-indigo-700 rounded-lg transition font-medium flex items-center gap-2">
                                <i class="fas fa-arrow-left text-sm"></i>
                                Back
                            </a>
                            <a href="{{ route('employee.leaves.create') }}"
                                class="px-5 py-2.5 bg-indigo-600 text-white hover:bg-indigo-700 rounded-lg transition font-medium flex items-center gap-2 shadow-sm">
                                <i class="fas fa-plus text-sm"></i>
                                New Request
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-8">
                <div class="max-w-7xl mx-auto">

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                            <p class="text-sm text-slate-500 font-medium">Total Requests</p>
                            <p class="text-3xl font-bold text-slate-900 mt-1">{{ $leaves->total() ?? 0 }}</p>
                        </div>
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                            <p class="text-sm text-slate-500 font-medium">Approved</p>
                            <p class="text-3xl font-bold text-green-600 mt-1">
                                {{ $leaves->where('status', 'Approved')->count() }}
                            </p>
                        </div>
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                            <p class="text-sm text-slate-500 font-medium">Pending</p>
                            <p class="text-3xl font-bold text-amber-600 mt-1">
                                {{ $leaves->where('status', 'Pending')->count() }}
                            </p>
                        </div>
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                            <p class="text-sm text-slate-500 font-medium">Rejected</p>
                            <p class="text-3xl font-bold text-red-600 mt-1">
                                {{ $leaves->where('status', 'Rejected')->count() }}
                            </p>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-max">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200">
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider w-16">
                                            No.</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Leave Type</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            From Date</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            To Date</th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Days</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Reason</th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Half Day</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Approved By</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Approved At</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Rejected By</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Rejected At</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Reject Reason</th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @forelse($leaves as $leave)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <!-- No. -->
                                            <td class="px-6 py-4 text-center text-slate-700 font-medium">
                                                {{ $loop->iteration + ($leaves->currentPage() - 1) * $leaves->perPage() }}
                                            </td>

                                            <!-- Leave Type -->
                                            <td class="px-6 py-4">
                                                <span
                                                    class="font-semibold text-slate-900">{{ $leave->leaveType?->name ?? 'null' }}</span>
                                            </td>

                                            <!-- From Date -->
                                            <td class="px-6 py-4 text-slate-700 no-wrap">
                                                {{ \Carbon\Carbon::parse($leave->from_date)->format('d-m-Y') }}
                                            </td>

                                            <!-- To Date -->
                                            <td class="px-6 py-4 text-slate-700 no-wrap">
                                                {{ \Carbon\Carbon::parse($leave->to_date)->format('d-m-Y') }}
                                            </td>

                                            <!-- Days -->
                                            <td class="px-6 py-4 text-center font-medium text-slate-900">
                                                {{ $leave->leave_days }} day{{ $leave->leave_days != 1 ? 's' : '' }}
                                            </td>

                                            <!-- Reason -->
                                            <td class="px-6 py-4 text-slate-700 no-wrap" title="{{ $leave->reason }}">
                                                {{ $leave->reason ?: 'null' }}
                                            </td>

                                            {{-- Hald day --}}
                                            <td class="px-6 py-4 text-center">
                                                @if($leave->leave_for === 'half_day')
                                                    <span
                                                        class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                                                                                        @if($leave->half_day_type === 'morning') bg-blue-100 text-blue-800
                                                                                        @elseif($leave->half_day_type === 'afternoon') bg-purple-100 text-purple-800
                                                                                        @elseif($leave->half_day_type === 'other') bg-orange-100 text-orange-800
                                                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ ucfirst($leave->half_day_type ?? 'null') }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400">null</span>
                                                @endif
                                            </td>
                                            <!-- Approved By Column (similar structure) -->
                                            <td class="px-6 py-4">
                                                @if($leave->status === 'Approved')
                                                                                    <div>
                                                                                        <div class="font-medium text-slate-900">
                                                                                            {{ $leave->approvedByEmployee?->personalInfo?->full_name_en
                                                                                                ?? $leave->approvedByEmployee?->personalInfo?->full_name_kh
                                                                                                ?? 'Unknown' }}
                                                                                        </div>
                                                                                        {{-- <div class="text-xs text-green-600">
                                                                                            @if($leave->approved_by == $leave->approver_id)
                                                                                                Approved
                                                                                            @endif
                                                                                        </div> --}}
                                                                                    </div>
                                                @else
                                                    <span class="text-slate-400">null</span>
                                                @endif
                                            </td>

                                            <!-- Approved At -->
                                            <td class="px-6 py-4 text-slate-700 no-wrap">
                                                @if($leave->status === 'Approved' && $leave->approved_at)
                                                    {{ \Carbon\Carbon::parse($leave->approved_at)->format('d-m-Y h:i A') }}
                                                @else
                                                    <span class="text-slate-400">null</span>
                                                @endif
                                            </td>

                                            <!-- Rejected By -->
                                            <!-- Rejected By Column -->
                                            <td class="px-6 py-4">
                                                @if($leave->status === 'Rejected')
                                                                                    <div>
                                                                                        <div class="font-medium text-slate-900">
                                                                                            {{ $leave->rejectedByEmployee?->personalInfo?->full_name_en
                                                    ?? $leave->rejectedByEmployee?->personalInfo?->full_name_kh
                                                    ?? 'Unknown' }}
                                                                                        </div>
                                                                                        {{-- <div class="text-xs text-red-600">
                                                                                            @if($leave->rejected_by == $leave->person_incharge_id)
                                                                                                Supervisor Rejecter
                                                                                            @elseif($leave->rejected_by == $leave->approver_id)
                                                                                                {{ $leave->approver_id ? 'HR Rejecter' : 'Approver' }}
                                                                                            @else
                                                                                                Rejecter
                                                                                            @endif
                                                                                        </div> --}}
                                                                                    </div>
                                                @else
                                                    <span class="text-slate-400">null</span>
                                                @endif
                                            </td>

                                            <!-- Rejected At -->
                                            <td class="px-6 py-4 text-slate-700 no-wrap">
                                                @if($leave->status === 'Rejected' && $leave->rejected_at)
                                                    {{ \Carbon\Carbon::parse($leave->rejected_at)->format('d-m-Y h:i A') }}
                                                @else
                                                    <span class="text-slate-400">null</span>
                                                @endif
                                            </td>

                                            <!-- Reject Reason -->
                                            <td class="px-6 py-4 text-red-700 font-medium no-wrap"
                                                title="{{ $leave->reject_reason }}">
                                                {{ $leave->reject_reason ?: 'null' }}
                                            </td>

                                            <!-- Status -->
                                            <td class="px-6 py-4 text-center">
                                                @if($leave->status === 'Approved')
                                                    <span
                                                        class="inline-flex px-4 py-2 rounded-full text-sm font-bold bg-green-500 text-white">APPROVED</span>
                                                @elseif($leave->status === 'Pending')
                                                    <span
                                                        class="inline-flex px-4 py-2 rounded-full text-sm font-bold bg-amber-500 text-white">PENDING</span>
                                                @elseif($leave->status === 'Rejected')
                                                    <span
                                                        class="inline-flex px-4 py-2 rounded-full text-sm font-bold bg-red-500 text-white">REJECTED</span>
                                                @else
                                                    <span class="text-slate-400">—</span>
                                                @endif
                                            </td>

                                            <!-- Actions -->
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex items-center justify-center gap-4">
                                                    @if($leave->status === 'Pending')
                                                        <a href="{{ route('employee.leaves.edit', $leave->id) }}"
                                                            class="text-blue-600 hover:text-blue-800">
                                                            <i class="fas fa-edit text-lg"></i>
                                                        </a>
                                                    @else
                                                        <i class="fas fa-edit text-lg disabled-icon"
                                                            title="Cannot edit after processing"></i>
                                                    @endif

                                                    @if($leave->status === 'Pending')
                                                        <form action="{{ route('employee.leaves.destroy', $leave->id) }}"
                                                            method="POST" class="inline"
                                                            onsubmit="return confirm('Delete this request permanently?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                                <i class="fas fa-trash text-lg"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <i class="fas fa-trash text-lg disabled-icon"
                                                            title="Cannot delete after processing"></i>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="13" class="px-6 py-20 text-center text-slate-500">
                                                <div class="flex flex-col items-center">
                                                    <i class="fas fa-calendar-times text-6xl text-slate-300 mb-4"></i>
                                                    <h3 class="text-xl font-semibold mb-2">No Leave Requests Yet</h3>
                                                    <p class="mb-6">Start by creating your first leave request.</p>
                                                    <a href="{{ route('employee.leaves.create') }}"
                                                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                                        Create New Request
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    @if($leaves->hasPages())
                        <div class="mt-8 flex justify-center">
                            {{ $leaves->links('pagination::tailwind') }}
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

</body>
<script src="{{ asset('assets/toast/script.js') }}"></script>
</html>