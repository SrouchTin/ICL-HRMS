<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Department;
use App\Models\Branch;
use App\Models\Position;
use App\Models\PersonalInfo;
use App\Models\Identification;
use App\Models\Address;
use App\Models\Contact;
use App\Models\EmergencyContact;
use App\Models\FamilyMember;
use App\Models\EducationHistory;
use App\Models\TrainingHistory;
use App\Models\EmploymentHistory;
use App\Models\Attachment;

class Employee extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'branch_id',
        'position_id',
        'employee_code',
        'start_date',
        'image',
        'contract_type',
        'employment_type',
        'status',
    ];

    // =============================
    // RELATIONSHIPS – ត្រឹមត្រូវ 100%
    // =============================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    // 1:1 Relationships (មនុស្សម្នាក់ → មានតែមួយ)
    public function personalInfo()
    {
        return $this->hasOne(PersonalInfo::class);
    }

    public function contact()           // ប្តូរពី contacts() → contact()
    {
        return $this->hasOne(Contact::class);
    }

    public function address()           // ប្តូរពី addresses() → address()
    {
        return $this->hasOne(Address::class);
    }

    public function identification()    // បើចង់មានតែមួយ → hasOne
    {
        return $this->hasOne(Identification::class);
    }

    // 1:Many Relationships (មនុស្សម្នាក់ → អាចមានច្រើន)
    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function educationHistories()
    {
        return $this->hasMany(EducationHistory::class);
    }

    public function trainingHistories()
    {
        return $this->hasMany(TrainingHistory::class);
    }

    public function employmentHistories()
    {
        return $this->hasMany(EmploymentHistory::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    // បើមាន Achievement
    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }
}