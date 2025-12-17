<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $table = 'leave_types';
    protected $fillable = [
        'name','max_days','is_paid','is_accumulative','status'
    ];
}
