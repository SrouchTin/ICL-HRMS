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
use App\Models\Achievement;
use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\EmployeeSchedule;
use App\Models\Attendance;
use Carbon\Carbon;

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
        'supervisor_id',
    ];
    
    public function supervisor() {
            return $this->belongsTo(Employee::class, 'supervisor_id');
        }

        public function supervisees() {
            return $this->hasMany(Employee::class, 'supervisor_id');
        }
    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }
        public function user()
    {
        return $this->hasOne(User::class); // Important: hasOne, not belongsTo
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
        return $this->hasOne(PersonalInfo::class, 'employee_id', 'id');
    }

    public function contact()           
    {
        return $this->hasOne(Contact::class);
    }

    public function address()           
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

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }
    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'supervisor_id');
    }
    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class, 'employee_id');
    }

    /**
     * Check if this employee has any subordinates
     */
    public function isSupervisor()
    {
        return $this->subordinates()->exists();
    }

    /**
     * Get pending leave approvals count for this employee
     */
    public function getPendingApprovalsCountAttribute()
    {
        return $this->leavesToApprove()->where('status', 'Pending')->count();
    }
       public function leavesToApprove()
    {
        return $this->hasMany(Leave::class, 'approver_id');
    }

    /**
     * Get all leave requests where this employee is the person in charge
     */
    public function leavesAsPersonInCharge()
    {
        return $this->hasMany(Leave::class, 'person_incharge_id');
    }
    public function employeeSchedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    // === THIS IS THE FIX ===
    public function getNameAttribute()
    {
        return $this->personalInfo?->full_name_en ?? 'Unknown Employee';
    }

    // Optional: Khmer name
    public function getNameKhAttribute()
    {
        return $this->personalInfo?->full_name_kh;
    }

    // Optional: Employee ID accessor if needed
    public function getEmployeeIdAttribute()
    {
        return $this->employee_code;
    }
    public function currentShift($date = null)
{
    $date = $date ? Carbon::parse($date) : Carbon::today();

    return $this->hasOne(EmployeeSchedule::class)
        ->where('start_date', '<=', $date)
        ->where(function ($query) use ($date) {
            $query->where('end_date', '>=', $date)
                  ->orWhereNull('end_date');
        })
        ->with('shift'); // load the actual shift details
}
}