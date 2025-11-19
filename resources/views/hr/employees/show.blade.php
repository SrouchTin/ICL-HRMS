{{-- resources/views/hr/employees/show.blade.php --}}
@extends('layouts.hr-app')

@section('title', $employee->user->name . ' Profile')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-8">
            <div class="flex items-center space-x-6">
                <div>
                    @if($employee->image)
                        <img src="{{ asset('storage/' . $employee->image) }}" class="h-32 w-32 rounded-full border-4 border-white shadow-lg">
                    @else
                        <div class="h-32 w-32 rounded-full bg-white text-indigo-600 flex items-center justify-center text-4xl font-bold shadow-lg">
                            {{ substr($employee->user->name, 0, 2) }}
                        </div>
                    @endif
                </div>
                <div>
                    <h1 class="text-3xl font-bold">{{ $employee->personalInfo?->full_name_en ?? $employee->user->name }}</h1>
                    <p class="text-xl opacity-90">{{ $employee->department?->department_name }}</p>
                    <p class="mt-2">
                        <span class="bg-white text-indigo-700 px-4 py-1 rounded-full text-sm font-medium">
                            {{ $employee->employee_code }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-8" aria-label="Tabs">
                <a href="#personal" class="tab-link py-4 px-1 border-b-2 font-medium text-sm border-indigo-500 text-indigo-600">Personal Info</a>
                <a href="#contact" class="tab-link py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700">Contact</a>
                <a href="#family" class="tab-link py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700">Family</a>
                <a href="#education" class="tab-link py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700">Education</a>
                <a href="#documents" class="tab-link py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700">Documents</a>
            </nav>
        </div>

        <div class="p-8">
            <!-- Personal Info Tab -->
            <div id="personal" class="tab-content">
                <h2 class="text-2xl font-bold mb-4">Personal Information</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div><strong>Khmer Name:</strong> {{ $employee->personalInfo?->full_name_kh ?? '—' }}</div>
                    <div><strong>Gender:</strong> {{ ucfirst($employee->personalInfo?->gender ?? '—') }}</div>
                    <div><strong>Date of Birth:</strong> {{ $employee->personalInfo?->dob?->format('d M Y') ?? '—' }}</div>
                    <div><strong>Nationality:</strong> {{ $employee->personalInfo?->nationality ?? '—' }}</div>
                    <div><strong>Marital Status:</strong> {{ ucfirst($employee->personalInfo?->marital_status ?? '—') }}</div>
                    <div><strong>Blood Group:</strong> {{ $employee->personalInfo?->blood_group ?? '—' }}</div>
                </div>
            </div>

            <!-- Other tabs (Contact, Family, etc.) follow same pattern -->
            <div id="contact" class="tab-content hidden">
                <h2 class="text-2xl font-bold mb-4">Contact Details</h2>
                <p>Email: {{ $employee->contact?->email ?? '—' }}</p>
                <p>Phone: {{ $employee->contact?->phone_number ?? '—' }}</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.tab-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.tab-link').forEach(l => {
                l.classList.remove('border-indigo-500', 'text-indigo-600');
                l.classList.add('border-transparent', 'text-gray-500');
            });
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            
            this.classList.add('border-indigo-500', 'text-indigo-600');
            document.querySelector(this.getAttribute('href')).classList.remove('hidden');
        });
    });
</script>
@endsection