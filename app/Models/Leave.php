<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leaves';
    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'from_date',
        'to_date',
        'leave_days',
        'leave_period',
        'half_day_type',
        'reason',
        'status'
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

}
