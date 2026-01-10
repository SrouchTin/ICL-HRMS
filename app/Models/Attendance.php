<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'date', 'shift_id', 'check_in', 'check_out',
        'come_early', 'come_late', 'leave_early', 'leave_late',
        'overtime_minutes', 'leave_id', 'holiday_id', 'is_working_day', 'remark',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
        'come_early' => 'boolean',
        'come_late' => 'boolean',
        'leave_early' => 'boolean',
        'leave_late' => 'boolean',
        'is_working_day' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }

    public function holiday()
    {
        return $this->belongsTo(Holiday::class);
    }
}