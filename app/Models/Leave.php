<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $table = 'leaves';
    
    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'leave_for',   
        'half_day_type',     
        'from_date',
        'to_date',
        'leave_days',
        'reason',
        'remark',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'reject_reason',
        'subject',
        'person_incharge_id',
        'approver_id'
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'leave_days' => 'float',
    ];

    /**
     * Get the employee who requested the leave
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the person in charge during the leave
     */
    public function personInCharge()
    {
        return $this->belongsTo(Employee::class, 'person_incharge_id');
    }

    /**
     * Get the designated approver (HR or Supervisor employee)
     * This is the employee assigned to approve when creating the request
     */
    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }

    /**
     * Get the employee who actually approved this leave
     * This is populated when the leave gets approved
     */
    public function approvedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    /**
     * Get the employee who rejected this leave
     * This is populated when the leave gets rejected
     */
    public function rejectedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'rejected_by');
    }

    /**
     * Get the leave type
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    /**
     * Scope for pending leaves
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope for approved leaves
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    /**
     * Scope for rejected leaves
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

    /**
     * DEPRECATED - Use approvedByEmployee() instead
     * Kept for backward compatibility
     */
    public function approvedBy()
    {
        return $this->approvedByEmployee();
    }

    /**
     * DEPRECATED - Use rejectedByEmployee() instead
     * Kept for backward compatibility
     */
    public function rejectedBy()
    {
        return $this->rejectedByEmployee();
    }
    public function rejecter()
    {
        return $this->belongsTo(Employee::class, 'rejected_by');
    }
}