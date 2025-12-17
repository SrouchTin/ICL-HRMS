<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Leaves • {{ Auth::user()->username }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        [x-cloak] { display: none !important; }
        
        .status-badge {
            @apply inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold;
        }
        .status-approved { @apply bg-green-100 text-green-800; }
        .status-pending  { @apply bg-amber-100 text-amber-800; }
        .status-rejected { @apply bg-red-100 text-red-800; }
        
        .card-hover {
            @apply transition-all duration-200 hover:shadow-xl hover:-translate-y-1;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen font-sans antialiased">

<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

    @include('layout.employeeSidebar')

    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Minimalist Header -->
        <header class="bg-white/80 backdrop-blur-lg border-b border-slate-200">
            <div class="px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">My Leaves</h1>
                        <p class="text-sm text-slate-500 mt-1">Manage your time off requests</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('employee.dashboard') }}"
                           class="px-4 py-2.5 text-slate-600 hover:bg-slate-100 rounded-lg transition font-medium flex items-center gap-2">
                            <i class="fas fa-arrow-left text-sm"></i>
                            <span>Back</span>
                        </a>
                        <a href="{{ route('employee.leaves.create') }}"
                           class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium flex items-center gap-2 shadow-sm">
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

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-500 font-medium">Total Requests</p>
                                <p class="text-3xl font-bold text-slate-900 mt-1">{{ $leaves->total() ?? 0 }}</p>
                            </div>
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-indigo-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-500 font-medium">Approved</p>
                                <p class="text-3xl font-bold text-green-600 mt-1">{{ $leaves->where('status', 'Approved')->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-500 font-medium">Pending</p>
                                <p class="text-3xl font-bold text-amber-600 mt-1">{{ $leaves->where('status', 'Pending')->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-amber-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-500 font-medium">Rejected</p>
                                <p class="text-3xl font-bold text-red-600 mt-1">{{ $leaves->where('status', 'Rejected')->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-times-circle text-red-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" placeholder="Search by leave type, reason, or status..."
                               class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                    </div>
                </div>

                <!-- Leave Records Table -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Leave Type</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">From Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">To Date</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Days</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Reason</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Approved By</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Approved At</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($leaves as $leave)
                                    <tr class="hover:bg-slate-50 transition-colors
                                        @if($leave->status === 'Approved') bg-green-50/30 @endif
                                        @if($leave->status === 'Pending') bg-amber-50/30 @endif
                                        @if($leave->status === 'Rejected') bg-red-50/30 @endif">
                                        
                                        <td class="px-6 py-4">
                                            <span class="font-semibold text-slate-900">{{ $leave->leaveType?->name ?? '—' }}</span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="text-slate-700">{{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}</span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="text-slate-700">{{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }}</span>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <span class="font-bold text-indigo-700">
                                                {{ $leave->leave_days }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <p class="text-slate-600 text-sm max-w-xs" title="{{ $leave->reason }}">
                                                {{ Str::limit($leave->reason, 60) }}
                                            </p>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="text-slate-700">{{ $leave->approver?->username ?? '—' }}</span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="text-slate-700 text-sm">
                                                {{ $leave->approved_at ? \Carbon\Carbon::parse($leave->approved_at)->format('d M Y') : '—' }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            @switch($leave->status)
                                                @case('Approved')
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-green-500 text-white">
                                                        APPROVED
                                                    </span>
                                                    @break
                                                @case('Pending')
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-500 text-white">
                                                        PENDING
                                                    </span>
                                                    @break
                                                @case('Rejected')
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-red-500 text-white">
                                                        REJECTED
                                                    </span>
                                                    @break
                                            @endswitch
                                        </td>

                                        <td class="px-6 py-4">
                                            @if($leave->status === 'Pending')
                                                <div class="flex items-center justify-center gap-3">
                                                    <a href="{{ route('employee.leaves.edit', $leave->id) }}"
                                                       class="text-blue-600 hover:text-blue-800 transition"
                                                       title="Edit">
                                                        <i class="fas fa-edit text-lg"></i>
                                                    </a>
                                                    <form action="{{ route('employee.leaves.destroy', $leave->id) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Delete this leave request permanently?')"
                                                          class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="text-red-600 hover:text-red-800 transition"
                                                                title="Delete">
                                                            <i class="fas fa-trash text-lg"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-slate-300 text-center block">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                                    <i class="fas fa-calendar-times text-slate-400 text-3xl"></i>
                                                </div>
                                                <h3 class="text-lg font-bold text-slate-900 mb-2">No Leave Requests Yet</h3>
                                                <p class="text-slate-500 mb-6">
                                                    You haven't submitted any leave requests. Start by creating your first request.
                                                </p>
                                                <a href="{{ route('employee.leaves.create') }}"
                                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                                    <i class="fas fa-plus"></i>
                                                    Create Leave Request
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endempty
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if($leaves instanceof \Illuminate\Pagination\LengthAwarePaginator && $leaves->hasPages())
                    <div class="mt-6 bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                        {{ $leaves->links('pagination::tailwind') }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>

</body>
</html>