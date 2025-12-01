<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PersonalInfo;
use App\Models\Contact;
use App\Models\Address;
use App\Models\Attachment;
use App\Models\EmergencyContact;
use App\Models\Department;
use App\Models\Branch;
use App\Models\Position;
use App\Models\EducationHistory;
use App\Models\EmploymentHistory;
use App\Models\FamilyMember;
use App\Models\Identification;
use App\Models\TrainingHistory;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HREmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['department', 'branch', 'position', 'personalInfo']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('personalInfo', fn($q) => $q->whereRaw("CONCAT(full_name_kh, ' ', full_name_en) LIKE ?", ["%{$search}%"]))
                ->orWhere('employee_code', 'like', "%{$search}%");
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $employees = $query->latest()->paginate(15);
        $departments = Department::where('status', 'active')->get();
        $branches = Branch::where('status', 'active')->get();
        $positions = Position::where('status', 'active')->get();

        return view('hr.employees.index', compact('employees', 'departments', 'branches', 'positions'));
    }

    public function create()
    {
        $departments = Department::where('status', 'active')->get();
        $branches    = Branch::where('status', 'active')->get();
        $positions   = Position::where('status', 'active')->get();

        // THIS IS THE FIX — create a dummy employee with empty collections
        $employee = new \App\Models\Employee();

        // Initialize all relationships as empty collections so Blade doesn't crash
        $employee->setRelation('educationHistories', collect());
        $employee->setRelation('trainingHistories',  collect());
        $employee->setRelation('employmentHistories', collect());
        $employee->setRelation('achievements',       collect());
        $employee->setRelation('emergencyContacts',  collect());
        $employee->setRelation('familyMembers',      collect());
        $employee->setRelation('attachments',        collect());

        return view('hr.employees.create', compact('employee', 'departments', 'branches', 'positions'));
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {

            // ==================== 1. FULL VALIDATION (ត្រឹមត្រូវ 100%) ====================
            $request->validate([
                // Core
                'employee_code' => 'required|string|max:50|unique:employees,employee_code',
                'user_id'       => 'required|integer|exists:users,id',
                'department_id' => 'required|integer|exists:departments,id',
                'branch_id'     => 'required|integer|exists:branches,id',
                'position_id'   => 'required|integer|exists:positions,id',
                'contract_type' => 'required|in:UDC,FDC',
                'employee_type' => 'required|in:full_time,part_time,probation,internship,contract',
                'joining_date'  => 'required|date',
                'effective_date' => 'required|date|after_or_equal:joining_date',
                'end_date'      => 'nullable|date|after:effective_date',
                'image'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5048',

                // Personal Info
                'salutation'    => 'required|in:Mr,Mrs,Ms,Miss,Dr,Prof',
                'full_name_kh'  => 'required|string|max:255',
                'full_name_en'  => 'required|string|max:255',
                'gender'        => 'required|in:male,female',
                'dob'           => 'required|date|before:today',
                'marital_status' => 'required|in:single,married,divorced,widowed',
                'nationality'   => 'required|string|max:100',
                'religion'      => 'nullable|string|max:100',
                'blood_group'   => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
                'bank_account_number' => 'nullable|string|max:50',

                // Contact
                'phone_number'  => 'required|string|regex:/^0[0-9]{8,9}$/',
                'home_phone'    => 'nullable|string|regex:/^0[0-9]{8,9}$/',
                'office_phone'  => 'nullable|string|regex:/^0[0-9]{8,9}$/',
                'email'         => 'required|email|max:255|unique:contacts,email',

                // Address
                'city'          => 'required|string|max:100',
                'province'      => 'required|string|max:100',
                'country'       => 'required|string|max:100',
                'address'       => 'required|string|max:500',

                // Identification
                'identification_type'     => 'nullable|string|max:100',
                'identification_number'  => 'nullable|string|max:100',
                'expiration_date'         => 'nullable|date',

                // ==================== DYNAMIC FIELDS ====================
                'emergency_contacts' => 'nullable|array',
                'emergency_contacts.*.contact_person' => 'required_with:emergency_contacts|string|max:255',
                'emergency_contacts.*.relationship'   => 'required_with:emergency_contacts|string|max:100',
                'emergency_contacts.*.phone_number'   => 'required_with:emergency_contacts|regex:/^0[0-9]{8,9}$/',

                'family_members' => 'nullable|array',
                'family_members.*.name'         => 'required_with:family_members|string|max:255',
                'family_members.*.relationship' => 'required_with:family_members|string|max:100',
                'family_members.*.dob'          => 'nullable|date',
                'family_members.*.gender'       => 'nullable|in:male,female',
                'family_members.*.tax_filing'   => 'nullable|in:0,1',
                'family_members.*.phone_number' => 'nullable|regex:/^0[0-9]{8,9}$/',
                'family_members.*.remark'       => 'nullable|string',
                'family_members.*.attachment'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',

                'education_history' => 'nullable|array',
                'education_history.*.institute' => 'required_with:education_history|string|max:255',
                'education_history.*.degree'    => 'required_with:education_history|string|max:255',
                'education_history.*.subject'   => 'nullable|string|max:255',
                'education_history.*.start_date' => 'required_with:education_history|date',
                'education_history.*.end_date'  => 'nullable|date|after_or_equal:education_history.*.start_date',
                'education_history.*.remark'    => 'nullable|string',

                'employment_history' => 'nullable|array',
                'employment_history.*.company_name' => 'required_with:employment_history|string|max:255',
                'employment_history.*.designation'  => 'required_with:employment_history|string|max:255',
                'employment_history.*.start_date'   => 'required_with:employment_history|date',
                'employment_history.*.end_date'     => 'nullable|date|after_or_equal:employment_history.*.start_date',
                'employment_history.*.supervisor_name' => 'nullable|string|max:255',
                'employment_history.*.rate'         => 'nullable|string|max:50',
                'employment_history.*.remark'       => 'nullable|string',
                'employment_history.*.reason_for_leaving' => 'nullable|string',

                'training_history' => 'nullable|array',
                'training_history.*.institute'  => 'required_with:training_history|string|max:255',
                'training_history.*.subject'     => 'required_with:training_history|string|max:255',
                'training_history.*.start_date' => 'required_with:training_history|date',
                'training_history.*.end_date'   => 'nullable|date|after_or_equal:training_history.*.start_date',
                'training_history.*.remark'     => 'nullable|string',
                'training_history.*.attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',

                'achievements' => 'nullable|array',
                'achievements.*.title'          => 'required_with:achievements|string|max:255',
                'achievements.*.year_awarded'   => 'nullable|integer|min:1900|max:2100',
                'achievements.*.country'        => 'nullable|string|max:100',
                'achievements.*.program_name'   => 'nullable|string|max:255',
                'achievements.*.organizer_name' => 'nullable|string|max:255',
                'achievements.*.remark'         => 'nullable|string',
                'achievements.*.attachment'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',

                // REQUIRED ATTACHMENTS (យ៉ាងហោចណាស់ 1)
                'attachments'                => 'required|array|min:1',
                'attachments.*.name'         => 'required|string|max:255',
                'attachments.*.file'         => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            ], [
                'required' => ':attribute ត្រូវតែបំពេញ។',
                'unique'   => ':attribute នេះមានរួចហើយ។',
                'phone_number.regex' => 'លេខទូរស័ព្ទត្រូវចាប់ផ្ដើមដោយ 0 និងមាន 9-10 ខ្ទង់។',
                'attachments.min'    => 'ត្រូវមានឯកសារភ្ជាប់យ៉ាងហោចណាស់ ១។',
            ]);

            // ==================== CREATE EMPLOYEE ====================
            $imagePath = $request->hasFile('image')
                ? $request->file('image')->store('employees/profiles', 'public')
                : null;

            $employee = Employee::create([
                'employee_code' => $request->employee_code,
                'user_id'       => $request->user_id,
                'department_id' => $request->department_id,
                'branch_id'     => $request->branch_id,
                'position_id'   => $request->position_id,
                'image'         => $imagePath,
                'status'        => 'active',
            ]);

            // ==================== PERSONAL INFO ====================
            PersonalInfo::create([
                'employee_id'         => $employee->id,
                'salutation'          => $request->salutation,
                'full_name_kh'        => $request->full_name_kh,
                'full_name_en'        => $request->full_name_en,
                'gender'              => $request->gender,
                'dob'                 => $request->dob,
                'nationality'         => $request->nationality,
                'marital_status'      => $request->marital_status,
                'religion'            => $request->religion,
                'blood_group'         => $request->blood_group,
                'bank_account_number' => $request->bank_account_number,
                'contract_type'       => $request->contract_type,
                'employee_type'       => $request->employee_type,
                'joining_date'        => $request->joining_date,
                'effective_date'      => $request->effective_date,
                'end_date'            => $request->end_date,
            ]);

            // ==================== CONTACT & ADDRESS ====================
            Contact::create([
                'employee_id'  => $employee->id,
                'phone_number' => $request->phone_number,
                'home_phone'   => $request->home_phone,
                'office_phone' => $request->office_phone,
                'email'        => $request->email,
            ]);

            Address::create([
                'employee_id' => $employee->id,
                'city'        => $request->city,
                'province'    => $request->province,
                'country'     => $request->country,
                'address'     => $request->address,
            ]);

            // ==================== IDENTIFICATION ====================
            if ($request->filled('identification_type') || $request->filled('identification_number')) {
                Identification::create([
                    'employee_id'           => $employee->id,
                    'identification_type'   => $request->identification_type,
                    'identification_number' => $request->identification_number,
                    'expiration_date'       => $request->expiration_date,
                ]);
            }

            // ==================== DYNAMIC SECTIONS ====================
            $this->storeEmergencyContacts($request, $employee);
            $this->storeFamilyMembers($request, $employee);
            $this->storeEducationHistory($request, $employee);
            $this->storeEmploymentHistory($request, $employee);
            $this->storeTrainingHistory($request, $employee);
            $this->storeAchievements($request, $employee);
            $this->storeAttachments($request, $employee);

            return redirect()
                ->route('hr.employees.index')
                ->with('success', 'បុគ្គលិកត្រូវបានបង្កើតដោយជោគជ័យ!');
        });
    }

    // ==================== HELPER METHODS (ស្អាត និងអាច reuse បាន) ====================

    private function storeEmergencyContacts($request, $employee)
    {
        if ($request->filled('emergency_contacts')) {
            foreach ($request->emergency_contacts as $ec) {
                if (empty($ec['contact_person'])) continue;
                EmergencyContact::create([
                    'employee_id'    => $employee->id,
                    'contact_person' => $ec['contact_person'],
                    'relationship'   => $ec['relationship'] ?? null,
                    'phone_number'   => $ec['phone_number'] ?? null,
                ]);
            }
        }
    }

    private function storeFamilyMembers($request, $employee)
    {
        if ($request->has('family_members')) {
            foreach ($request->family_members as $i => $fm) {
                if (empty($fm['name'])) continue;

                $path = $request->hasFile("family_members.{$i}.attachment")
                    ? $request->file("family_members.{$i}.attachment")->store("employees/{$employee->id}/family", 'public')
                    : null;

                FamilyMember::create([
                    'employee_id'  => $employee->id,
                    'name'         => $fm['name'],
                    'relationship' => $fm['relationship'] ?? null,
                    'dob'          => $fm['dob'] ?? null,
                    'nationality'  => $fm['nationality'] ?? null,
                    'gender'       => $fm['gender'] ?? null,
                    'tax_filing'   => $fm['tax_filing'] ?? 0,
                    'phone_number' => $fm['phone_number'] ?? null,
                    'remark'       => $fm['remark'] ?? null,
                    'attachment'   => $path,
                ]);
            }
        }
    }

    private function storeEducationHistory($request, $employee)
    {
        if ($request->has('education_history')) {
            foreach ($request->education_history as $edu) {
                if (empty($edu['institute'])) continue;
                EducationHistory::create([
                    'employee_id' => $employee->id,
                    'institute'   => $edu['institute'],
                    'subject'     => $edu['subject'] ?? null,
                    'degree'      => $edu['degree'],
                    'start_date'  => $edu['start_date'],
                    'end_date'    => $edu['end_date'] ?? null,
                    'remark'      => $edu['remark'] ?? null,
                ]);
            }
        }
    }

    private function storeEmploymentHistory($request, $employee)
    {
        if ($request->has('employment_history')) {
            foreach ($request->employment_history as $emp) {
                if (empty($emp['company_name'])) continue;
                EmploymentHistory::create([
                    'employee_id'        => $employee->id,
                    'company_name'       => $emp['company_name'],
                    'designation'        => $emp['designation'],
                    'start_date'         => $emp['start_date'],
                    'end_date'           => $emp['end_date'] ?? null,
                    'supervisor_name'    => $emp['supervisor_name'] ?? null,
                    'rate'               => $emp['rate'] ?? null,
                    'remark'             => $emp['remark'] ?? null,
                    'reason_for_leaving' => $emp['reason_for_leaving'] ?? null,
                ]);
            }
        }
    }

    private function storeTrainingHistory($request, $employee)
    {
        if ($request->has('training_history')) {
            foreach ($request->training_history as $i => $train) {
                if (empty($train['institute'])) continue;

                $path = $request->hasFile("training_history.{$i}.attachment")
                    ? $request->file("training_history.{$i}.attachment")->store("employees/{$employee->id}/training", 'public')
                    : null;

                TrainingHistory::create([
                    'employee_id' => $employee->id,
                    'institute'   => $train['institute'],
                    'subject'     => $train['subject'],
                    'start_date'  => $train['start_date'],
                    'end_date'    => $train['end_date'] ?? null,
                    'remark'      => $train['remark'] ?? null,
                    'attachment'  => $path,
                ]);
            }
        }
    }

    private function storeAchievements($request, $employee)
    {
        if ($request->has('achievements')) {
            foreach ($request->achievements as $i => $ach) {
                if (empty($ach['title'])) continue;

                $path = $request->hasFile("achievements.{$i}.attachment")
                    ? $request->file("achievements.{$i}.attachment")->store("employees/{$employee->id}/achievements", 'public')
                    : null;

                Achievement::create([
                    'employee_id'    => $employee->id,
                    'title'          => $ach['title'],
                    'year_awarded'   => $ach['year_awarded'] ?? null,
                    'country'        => $ach['country'] ?? null,
                    'program_name'   => $ach['program_name'] ?? null,
                    'organizer_name' => $ach['organizer_name'] ?? null,
                    'remark'         => $ach['remark'] ?? null,
                    'attachment'     => $path,
                ]);
            }
        }
    }

    private function storeAttachments($request, $employee)
    {
        foreach ($request->input('attachments', []) as $index => $data) {
            $file = $request->file("attachments.{$index}.file");
            if ($file && $file->isValid()) {
                $path = $file->store("employees/{$employee->id}/attachments", 'public');
                Attachment::create([
                    'employee_id'     => $employee->id,
                    'attachment_name' => $data['name'],
                    'file_path'       => $path,
                    'mime_type'       => $file->getMimeType(),
                    'file_size'       => $file->getSize(),
                ]);
            }
        }
    }
    public function show(Employee $employee)
    {
        $employee->load([
            'department',
            'branch',
            'position',
            'personalInfo',
            'contact',
            'address',
            'identification',
            'emergencyContacts',
            'familyMembers',
            'educationHistories',
            'trainingHistories',
            'employmentHistories',
            'achievements',
            'attachments'
        ]);

        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $employee->load([
            'personalInfo',
            'contact',
            'address',
            'identification',
            'emergencyContacts',
            'familyMembers',
            'educationHistories',
            'trainingHistories',
            'employmentHistories',
            'achievements',
            'attachments'
        ]);

        $departments = Department::where('status', 'active')->get();
        $branches    = Branch::where('status', 'active')->get();
        $positions   = Position::where('status', 'active')->get();

        return view('hr.employees.edit', compact('employee', 'departments', 'branches', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        // 1. VALIDATION ដាក់នៅខាងក្រៅ transaction ជាពុំខាន!
        $request->validate([
            'employee_code' => 'required|string|max:50|unique:employees,employee_code,' . $employee->id,
            'user_id' => 'required|integer|exists:users,id',
            'department_id' => 'required|integer|exists:departments,id',
            'branch_id'     => 'required|integer|exists:branches,id',
            'position_id'   => 'required|integer|exists:positions,id',
            'contract_type' => 'required|in:UDC,FDC',
            'employee_type' => 'required|in:full_time,part_time,probation,internship,contract',
            'joining_date'  => 'required|date',
            'effective_date' => 'required|date|after_or_equal:joining_date',
            'end_date'      => 'nullable|date|after:effective_date',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5048',
            'status'        => 'required|in:active,inactive',
            'salutation'    => 'required|in:Mr,Mrs,Ms,Miss,Dr,Prof',
            'full_name_kh'  => 'required|string|max:255',
            'full_name_en'  => 'required|string|max:255',
            'gender'        => 'required|in:male,female',
            'dob'           => 'required|date|before:today',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'nationality'   => 'required|string|max:100',
            'phone_number'  => 'required|string|regex:/^0[0-9]{8,9}$/',
            'email'         => 'required|email|max:255|unique:contacts,email,' . ($employee->contact?->id ?? 'NULL'),
            'city'          => 'required|string|max:100',
            'province'      => 'required|string|max:100',
            'country'       => 'required|string|max:100',
            'address'       => 'required|string|max:500',

            // Dynamic arrays (សម្រាលបន្តិច)
            'emergency_contacts' => 'nullable|array',
            'family_members'     => 'nullable|array',
            'education_history'  => 'nullable|array',
            'employment_history' => 'nullable|array',
            'training_history'   => 'nullable|array',
            'achievements'       => 'nullable|array',
            'attachments'        => 'nullable|array',
        ]);

        // 2. ឥឡូវ transaction សុទ្ធតែ DB operations
        DB::transaction(function () use ($request, $employee) {

            // Profile image
            if ($request->hasFile('image')) {
                if ($employee->image && Storage::disk('public')->exists($employee->image)) {
                    Storage::disk('public')->delete($employee->image);
                }
                $employee->image = $request->file('image')->store('employees/profiles', 'public');
                $employee->save();
            }

            // Main employee
            $employee->update([
                'employee_code' => $request->employee_code,
                'user_id'       => $request->user_id,
                'department_id' => $request->department_id,
                'status'        => $request->status,
                'branch_id'     => $request->branch_id,
                'position_id'   => $request->position_id,
            ]);

            // កូដដដែលៗ...
            $personalFields = [
                'joining_date',
                'contract_type',
                'employee_type',
                'effective_date',
                'end_date',
                'salutation',
                'full_name_kh',
                'full_name_en',
                'gender',
                'dob',
                'nationality',
                'religion',
                'blood_group',
                'marital_status',
                'bank_account_number'
            ];
            $employee->personalInfo()->updateOrCreate(['employee_id' => $employee->id], $request->only($personalFields));
            $employee->contact()->updateOrCreate(['employee_id' => $employee->id], $request->only(['phone_number', 'home_phone', 'office_phone', 'email']));
            $employee->address()->updateOrCreate(['employee_id' => $employee->id], $request->only(['city', 'province', 'country', 'address']));

            if ($request->filled('identification_type') || $request->filled('identification_number')) {
                $employee->identification()->updateOrCreate(['employee_id' => $employee->id], $request->only(['identification_type', 'identification_number', 'expiration_date']));
            } else {
                $employee->identification()?->delete();
            }
 

            // Sync all dynamic sections
            $this->syncEmergencyContacts($request, $employee);
            $this->syncFamilyMembers($request, $employee);
            $this->syncEducationHistory($request, $employee);
            $this->syncEmploymentHistory($request, $employee);
            $this->syncTrainingHistory($request, $employee);
            $this->syncAchievements($request, $employee);
            $this->syncAttachments($request, $employee);
        });

        // 3. redirect នៅខាងក្រៅ ជានិច្ច!
        return redirect()
            ->route('hr.employees.index')
            ->with('success', 'បុគ្គលិកត្រូវបានកែប្រែដោយជោគជ័យ!');
    }
    // Sync Emergency Contacts
    private function syncEmergencyContacts($request, $employee)
    {
        $existingIds = $employee->emergencyContacts->pluck('id')->toArray();
        $newIds = [];

        if ($request->filled('emergency_contacts')) {
            foreach ($request->emergency_contacts as $ec) {
                if (empty($ec['contact_person'])) continue;

                $contact = EmergencyContact::updateOrCreate(
                    ['id' => $ec['id'] ?? null, 'employee_id' => $employee->id],
                    [
                        'contact_person' => $ec['contact_person'],
                        'relationship'   => $ec['relationship'] ?? null,
                        'phone_number'   => $ec['phone_number'] ?? null,
                    ]
                );
                $newIds[] = $contact->id;
            }
        }

        // Delete removed
        foreach (array_diff($existingIds, $newIds) as $id) {
            EmergencyContact::find($id)?->delete();
        }
    }

    // Sync Family Members (with file handling)
    private function syncFamilyMembers($request, $employee)
    {
        $existingIds = $employee->familyMembers->pluck('id')->toArray();
        $newIds = [];

        if ($request->has('family_members')) {
            foreach ($request->family_members as $i => $fm) {
                if (empty($fm['name'])) continue;

                $path = null;
                if ($request->hasFile("family_members.{$i}.attachment")) {
                    $path = $request->file("family_members.{$i}.attachment")
                        ->store("employees/{$employee->id}/family", 'public');
                }

                $member = FamilyMember::updateOrCreate(
                    ['id' => $fm['id'] ?? null, 'employee_id' => $employee->id],
                    [
                        'name'         => $fm['name'],
                        'relationship' => $fm['relationship'] ?? null,
                        'dob'          => $fm['dob'] ?? null,
                        'nationality'  => $fm['nationality'] ?? null,
                        'gender'       => $fm['gender'] ?? null,
                        'tax_filing'   => $fm['tax_filing'] ?? 0,
                        'phone_number' => $fm['phone_number'] ?? null,
                        'remark'       => $fm['remark'] ?? null,
                        'attachment'   => $path,
                    ]
                );

                if ($path && $member->wasChanged('attachment') && $member->getOriginal('attachment')) {
                    Storage::disk('public')->delete($member->getOriginal('attachment'));
                }

                $newIds[] = $member->id;
            }
        }

        foreach (array_diff($existingIds, $newIds) as $id) {
            $fm = FamilyMember::find($id);
            if ($fm && $fm->attachment) Storage::disk('public')->delete($fm->attachment);
            $fm?->delete();
        }
    }

    // Repeat same pattern for others...
    private function syncEducationHistory($request, $employee)
    {
        $existingIds = $employee->educationHistories->pluck('id')->toArray();
        $newIds = [];

        if ($request->has('education_history')) {
            foreach ($request->education_history as $edu) {
                if (empty($edu['institute'])) continue;

                $education = EducationHistory::updateOrCreate(
                    ['id' => $edu['id'] ?? null, 'employee_id' => $employee->id],
                    $edu
                );
                $newIds[] = $education->id;
            }
        }

        // Only delete removed ones
        EducationHistory::where('employee_id', $employee->id)
            ->whereIn('id', array_diff($existingIds, $newIds))
            ->delete();
    }

    private function syncEmploymentHistory($request, $employee)
    {
        $existingIds = $employee->employmentHistories->pluck('id')->toArray();
        $newIds = [];

        if ($request->has('employment_history')) {
            foreach ($request->employment_history as $emp) {
                if (empty($emp['company_name'])) continue;

                $employment = EmploymentHistory::updateOrCreate(
                    ['id' => $emp['id'] ?? null, 'employee_id' => $employee->id],
                    $emp
                );
                $newIds[] = $employment->id;
            }
        }

        // Only delete removed ones
        EmploymentHistory::where('employee_id', $employee->id)
            ->whereIn('id', array_diff($existingIds, $newIds))
            ->delete();
    }

    private function syncTrainingHistory($request, $employee)
    {
        $existingIds = $employee->trainingHistories->pluck('id')->toArray();
        $newIds = [];

        if ($request->has('training_history')) {
            foreach ($request->training_history as $i => $train) {
                if (empty($train['institute'])) continue;

                $path = $request->hasFile("training_history.{$i}.attachment")
                    ? $request->file("training_history.{$i}.attachment")->store("employees/{$employee->id}/training", 'public')
                    : null;

                $t = TrainingHistory::updateOrCreate(
                    ['id' => $train['id'] ?? null, 'employee_id' => $employee->id],
                    array_merge($train, ['attachment' => $path])
                );

                if ($path && $t->wasChanged('attachment') && $t->getOriginal('attachment')) {
                    Storage::disk('public')->delete($t->getOriginal('attachment'));
                }

                $newIds[] = $t->id;
            }
        }

        foreach (array_diff($existingIds, $newIds) as $id) {
            $t = TrainingHistory::find($id);
            if ($t && $t->attachment) Storage::disk('public')->delete($t->attachment);
            $t?->delete();
        }
    }

    private function syncAchievements($request, $employee)
    {
        $existingIds = $employee->achievements->pluck('id')->toArray();
        $newIds = [];

        if ($request->has('achievements')) {
            foreach ($request->achievements as $i => $ach) {
                if (empty($ach['title'])) continue;

                $path = $request->hasFile("achievements.{$i}.attachment")
                    ? $request->file("achievements.{$i}.attachment")->store("employees/{$employee->id}/achievements", 'public')
                    : null;

                $a = Achievement::updateOrCreate(
                    ['id' => $ach['id'] ?? null, 'employee_id' => $employee->id],
                    array_merge($ach, ['attachment' => $path])
                );

                if ($path && $a->wasChanged('attachment') && $a->getOriginal('attachment')) {
                    Storage::disk('public')->delete($a->getOriginal('attachment'));
                }

                $newIds[] = $a->id;
            }
        }

        foreach (array_diff($existingIds, $newIds) as $id) {
            $a = Achievement::find($id);
            if ($a && $a->attachment) Storage::disk('public')->delete($a->attachment);
            $a?->delete();
        }
    }

    private function syncAttachments($request, $employee)
    {
        $existingIds = $employee->attachments->pluck('id')->toArray();
        $newIds = [];

        if ($request->has('attachments')) {
            foreach ($request->input('attachments', []) as $index => $data) {
                $file = $request->file("attachments.{$index}.file");
                $path = $file?->isValid() ? $file->store("employees/{$employee->id}/attachments", 'public') : null;

                $attach = Attachment::updateOrCreate(
                    ['id' => $data['id'] ?? null],
                    [
                        'employee_id'     => $employee->id,
                        'attachment_name' => $data['name'],
                        'file_path'       => $path,
                        'mime_type'       => $file?->getMimeType(),
                        'file_size'       => $file?->getSize(),
                    ]
                );

                if ($path && $attach->wasChanged('file_path') && $attach->getOriginal('file_path')) {
                    Storage::disk('public')->delete($attach->getOriginal('file_path'));
                }

                $newIds[] = $attach->id;
            }
        }

        foreach (array_diff($existingIds, $newIds) as $id) {
            $att = Attachment::find($id);
            if ($att && $att->file_path) Storage::disk('public')->delete($att->file_path);
            $att?->delete();
        }
    }

    public function destroy(Employee $employee)
    {
        DB::transaction(function () use ($employee) {
            // 1. Delete all related files (keep this — files must be cleaned!)
            // if ($employee->image) {
            //     Storage::disk('public')->delete($employee->image);
            // }

            // foreach ($employee->attachments as $att) {
            //     if ($att->file_path) {
            //         Storage::disk('public')->delete($att->file_path);
            //     }
            //     $att->delete(); // optional: you can keep attachments or soft-delete them too
            // }

            // foreach ($employee->familyMembers as $fm) {
            //     if ($fm->attachment) {
            //         Storage::disk('public')->delete($fm->attachment);
            //     }
            //     $fm->delete();
            // }

            // foreach ($employee->trainingHistories as $th) {
            //     if ($th->attachment) {
            //         Storage::disk('public')->delete($th->attachment);
            //     }
            //     $th->delete();
            // }

            // foreach ($employee->achievements as $ach) {
            //     if ($ach->attachment) {
            //         Storage::disk('public')->delete($ach->attachment);
            //     }
            //     $ach->delete();
            // }

            // 2. Instead of deleting → just mark as inactive
            $employee->status = 'inactive';  // or false, 0, 'terminated' — whatever your column uses
            $employee->save();
        });

        return back()->with('success', 'បុគ្គលិកត្រូវបានផ្លាស់ប្តូរទៅស្ថានភាពអសកម្មដោយជោគជ័យ!');
    }
}
