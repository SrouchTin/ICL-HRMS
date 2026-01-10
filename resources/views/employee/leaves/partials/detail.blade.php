<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Employee</label>
            <p class="text-lg font-bold text-gray-900">{{ $leave->employee->personalInfo?->full_name_en ?? 'N/A' }}</p>
            <p class="text-sm text-gray-600">{{ $leave->employee->employee_code }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Leave Type</label>
            <p class="text-lg font-bold text-indigo-600">{{ $leave->leaveType?->name ?? 'N/A' }}</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Duration</label>
            <p class="text-lg font-bold text-gray-900">{{ number_format($leave->leave_days, 1) }} day{{ $leave->leave_days != 1 ? 's' : '' }}</p>
            @if($leave->leave_for === 'half_day')
                <p class="text-sm text-orange-600 mt-1">{{ ucfirst($leave->half_day_type) }} half day</p>
            @endif
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Dates</label>
            <p class="text-lg font-bold text-gray-900">
                {{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}
                @if($leave->to_date !== $leave->from_date)
                    → {{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }}
                @endif
            </p>
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-3">Subject & Reason</label>
        <div class="bg-gray-50 p-4 rounded-xl">
            <h4 class="font-bold text-lg text-gray-900 mb-2">{{ $leave->subject }}</h4>
            <p class="text-gray-700 leading-relaxed">{{ nl2br(e($leave->reason)) }}</p>
        </div>
    </div>

    @if($leave->remark)
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Remarks</label>
            <p class="bg-blue-50 p-4 rounded-xl text-gray-800">{{ nl2br(e($leave->remark)) }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Person In Charge</label>
            <p class="text-gray-900 font-medium">
                {{ $leave->personInCharge?->personalInfo?->full_name_en ?? 'N/A' }}
                <span class="text-xs text-gray-500">({{ $leave->personInCharge?->employee_code ?? '' }})</span>
            </p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Requested On</label>
            <p class="text-sm text-gray-600">{{ $leave->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
</div>