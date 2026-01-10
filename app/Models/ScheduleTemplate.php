<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'shift_id', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun',
    ];

    protected $casts = [
        'mon' => 'boolean',
        'tue' => 'boolean',
        'wed' => 'boolean',
        'thu' => 'boolean',
        'fri' => 'boolean',
        'sat' => 'boolean',
        'sun' => 'boolean',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function employeeSchedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }
}