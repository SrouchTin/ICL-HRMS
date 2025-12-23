<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leaves';
    
    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'leave_period',   
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
        'person_incharge_id'
    ];

    protected $casts = [
        'from_date'    => 'date',
        'to_date'      => 'date',
        'approved_at'  => 'datetime',
        'leave_days'   => 'float',
    ];

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}