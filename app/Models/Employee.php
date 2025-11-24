<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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

class Employee extends Model
{
    use HasFactory;

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
    // Relationships
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

    public function personalInfo()
    {
        return $this->hasOne(PersonalInfo::class);
    }

    public function identifications()
    {
        return $this->hasMany(Identification::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

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
    public function achievements()
{
    return $this->hasMany(Achievement::class);
}
    public function attachments()
{
    return $this->hasMany(Attachment::class);
}

    // Add other relationships like education, training, employment histories, etc.
}
