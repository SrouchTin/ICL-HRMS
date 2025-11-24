<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HR Dashboard</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div class="flex h-screen">

        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-b from-indigo-800 to-indigo-900 text-white flex flex-col">
            <div class="p-6 text-center border-b border-indigo-700">
                <h2 class="text-2xl font-bold">HR Dashboard</h2>

            </div>

            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('hr.dashboard') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-indigo-700 text-white font-medium">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('hr.employees.index') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-users"></i>
                    <span>Employees</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-clock"></i>
                    <span>Attendance</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-calendar-check"></i>
                    <span>Leave Requests</span>

                </a>
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-route"></i>
                    <span>Missions</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-car"></i>
                    <span>Company Vehicles</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Payroll</span>
                </a>
            </nav>

            <div class="p-4 border-t border-indigo-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Bar -->


            <main class="flex-1 p-6 overflow-auto">
                <div class="w-full max-w-7xl bg-white rounded-2xl shadow-lg p-6 mx-auto space-y-6">
                    <h1 class="text-3xl font-bold mb-4">Add New Employee</h1>

                    {{-- 1. Personal Info --}}
                    <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        <div class="bg-gray-100 p-5 rounded-xl space-y-4">
                            <h2 class="text-3xl font-semibold">Personal Information</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 font-medium">Employee Code</label>
                                    <input type="text" name="employee_code" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Salutation</label>
                                    <select name="salutation" class="border p-2 rounded w-full">
                                        <option value="">Select Salutation</option>
                                        <option value="Mr">Mr.</option>
                                        <option value="Ms">Ms.</option>
                                        <option value="Mrs">Mrs.</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Full Name (KH)</label>
                                    <input type="text" name="full_name_kh" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Full Name (EN)</label>
                                    <input type="text" name="full_name_en" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Gender</label>
                                    <select name="gender" class="border p-2 rounded w-full">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Date of Birth</label>
                                    <input type="date" name="dob" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Nationality</label>
                                    <input type="text" name="nationality" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Marital Status</label>
                                    <select name="marital_status" class="border p-2 rounded w-full">
                                        <option value="">Select Marital Status</option>
                                        <option value="single">Single</option>
                                        <option value="married">Married</option>
                                        <option value="divorced">Divorced</option>
                                        <option value="widowed">Widowed</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Religion</label>
                                    <input type="text" name="religion" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Blood Group</label>
                                    <input type="text" name="blood_group" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Bank Account Number</label>
                                    <input type="text" name="bank_account_number" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Contract Type</label>
                                    <select name="contract_type" class="border p-2 rounded w-full">
                                        <option value="">Select Contract Type</option>
                                        <option value="UDC">UDC</option>
                                        <option value="FDC">FDC</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Employee Type</label>
                                    <select name="employee_type" class="border p-2 rounded w-full">
                                        <option value="">Select Employee Type</option>
                                        <option value="full_time">Full Time</option>
                                        <option value="part_time">Part Time</option>
                                        <option value="probation">Probation</option>
                                        <option value="internship">Internship</option>
                                        <option value="contract">Contract</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Join Data</label>
                                    <input type="date" name="join_data" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">End Data</label>
                                    <input type="date" name="end_data" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Effective Data</label>
                                    <input type="date" name="effective_data" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Employee Photo</label>
                                    <input type="file" name="image" class="border p-2 rounded w-full">
                                </div>
                            </div>
                            <div>
                                <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                                    Add
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- 2. Identification Info --}}
                    <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        <div class="bg-gray-100 p-5 rounded-xl space-y-4">
                            <h2 class="text-3xl font-semibold">Identification Information</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 font-medium">Identification Type</label>
                                    <input type="text" name="identification_type" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Identification Number</label>
                                    <input type="number" name="identification_number" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Explration Data</label>
                                    <input type="date" name="explration_date" class="border p-2 rounded w-full">
                                </div>
                            </div>
                            <div>
                                <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                                    Add
                                </button>
                            </div>
                        </div>

                    </form>

                    {{-- 3. Permanent Address Info --}}
                    <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        <div class="bg-gray-100 p-5 rounded-xl space-y-4">
                            <h2 class="text-3xl font-semibold">Identification Information</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 font-medium">City</label>
                                    <input type="text" name="city" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Province</label>
                                    <input type="text" name="province" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Country</label>
                                    <input type="text" name="country" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Address</label>
                                    <textarea name="address" id="" cols="30" rows="10"
                                        class="border p-2 rounded w-full">

                                    </textarea>
                                </div>
                            </div>
                            <div>
                                <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                                    Add
                                </button>
                            </div>
                        </div>

                    </form>

                    {{-- 4. Contact Info --}}
                    <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        <div class="bg-gray-100 p-5 rounded-xl space-y-4">
                            <h2 class="text-3xl font-semibold">Contact Information</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 font-medium">Phone Number</label>
                                    <input type="number" name="phone_number" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Home Phone Number</label>
                                    <input type="number" name="home_phone_number" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Office Phone Number</label>
                                    <input type="number" name="office_phone_number" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Email</label>
                                    <input type="email" name="email" class="border p-2 rounded w-full">
                                </div>
                            </div>
                            <div>
                                <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                                    Add
                                </button>
                            </div>
                        </div>

                    </form>

                    {{-- 5. Emergency Contact --}}
                    <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        <div class="bg-gray-100 p-5 rounded-xl space-y-4">
                            <h2 class="text-3xl font-semibold">Emergency Contact</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 font-medium">Contact Person</label>
                                    <input type="text" name="contact_person" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Relationship</label>
                                    <input type="text" name="relationship" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Phone Number</label>
                                    <input type="number" name="phone_number" class="border p-2 rounded w-full">
                                </div>
                            </div>
                            <div>
                                <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                                    Add
                                </button>
                            </div>
                        </div>

                    </form>

                    {{-- 6. Family Member Info --}}
                    <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        <div class="bg-gray-100 p-5 rounded-xl space-y-4">
                            <h2 class="text-3xl font-semibold">Family Member Information</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 font-medium">Name</label>
                                    <input type="text" name="name" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Relationship</label>
                                    <input type="text" name="relationship" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Date Of Birth</label>
                                    <input type="date" name="dob" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Gender</label>
                                    <input type="text" name="gender" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Nationality</label>
                                    <input type="text" name="nationality" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Tax Filling</label>
                                    <input type="text" name="tax" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Phone Number</label>
                                    <input type="number" name="phone_number" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Remark</label>
                                    <input type="text" name="remark" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Attachment</label>
                                    <input type="file" name="attachment">
                                </div>
                            </div>
                            <div>
                                <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                                    Add
                                </button>
                            </div>
                        </div>

                    </form>

                    {{-- 7. Education History Info --}}
                    <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        <div class="bg-gray-100 p-5 rounded-xl space-y-4">
                            <h2 class="text-3xl font-semibold">Education History Information</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 font-medium">Institue</label>
                                    <input type="text" name="Institue" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Subject</label>
                                    <input type="text" name="subject" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Degree</label>
                                    <input type="text" name="degree" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Start Data</label>
                                    <input type="date" name="start_data" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">End Date</label>
                                    <input type="date" name="end_date" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Remark</label>
                                    <input type="text" name="remark" class="border p-2 rounded w-full">
                                </div>
                            </div>
                            <div>
                                <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                                    Add
                                </button>
                            </div>
                        </div>

                    </form>

                    {{-- 8. Training History Info --}}
                    <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        <div class="bg-gray-100 p-5 rounded-xl space-y-4">
                            <h2 class="text-3xl font-semibold">Training History Information</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 font-medium">Institue</label>
                                    <input type="text" name="Institue" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Subject</label>
                                    <input type="text" name="subject" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Start Data</label>
                                    <input type="date" name="start_data" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">End Date</label>
                                    <input type="date" name="end_date" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Remark</label>
                                    <input type="text" name="remark" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Attachment</label>
                                    <input type="file" name="attachment">
                                </div>
                            </div>
                            <div>
                                <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                                    Add
                                </button>
                            </div>
                        </div>

                    </form>

                    {{-- 9. Employment History Info --}}
                    <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        <div class="bg-gray-100 p-5 rounded-xl space-y-4">
                            <h2 class="text-3xl font-semibold">Employment History Information</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 font-medium">Company Name</label>
                                    <input type="text" name="company_name" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Start Date</label>
                                    <input type="date" name="start_date" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">End Date</label>
                                    <input type="date" name="end_date" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Designation</label>
                                    <input type="text" name="designation" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Supervisor Name</label>
                                    <input type="text" name="supervisor_name" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Remark</label>
                                    <input type="text" name="remark" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Rate</label>
                                    <input type="text" name="rate" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Reason for leaving</label>
                                    <textarea name="reason" id="" cols="30" rows="10" class="border p-2 rounded w-full">

                                    </textarea>
                                </div>
                            </div>
                            <div>
                                <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                                    Add
                                </button>
                            </div>
                        </div>

                    </form>

                    {{-- 10. Archievment Info --}}
                    <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        <div class="bg-gray-100 p-5 rounded-xl space-y-4">
                            <h2 class="text-3xl font-semibold">Archievment Information</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 font-medium">Title</label>
                                    <input type="text" name="title" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Year Awarded</label>
                                    <input type="date" name="year_aworded" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Country</label>
                                    <input type="text" name="country" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Program Name</label>
                                    <input type="text" name="program_name" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Organizer Name</label>
                                    <input type="text" name="organizer_name" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Remark</label>
                                    <input type="text" name="remark" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Attachment</label>
                                    <input type="file" name="attachment">
                                </div>
                            </div>
                            <div>
                                <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                                    Add
                                </button>
                            </div>
                        </div>

                    </form>

                    {{-- 10. Attachment Info --}}
                    <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        <div class="bg-gray-100 p-5 rounded-xl space-y-4">
                            <h2 class="text-3xl font-semibold">Attachment Information</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 font-medium">Attachment Name</label>
                                    <input type="text" name="attachment_name" class="border p-2 rounded w-full">
                                </div>
                                <div>
                                    <label class="block mb-1 font-medium">Attachment</label>
                                    <input type="file" name="attachment">
                                </div>
                            </div>
                            <div>
                                <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                                    Add
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </main>

        </div>
    </div>

    <!-- Simple JS for Notification Toggle -->
    <script>
        document.getElementById('notificationBtn').addEventListener('click', function () {
            document.getElementById('notificationDropdown').classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('#notificationBtn') && !e.target.closest('#notificationDropdown')) {
                document.getElementById('notificationDropdown').classList.add('hidden');
            }
        });
    </script>

</body>

</html>