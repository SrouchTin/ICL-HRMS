<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_code',
        'department_name',
        'description',
        'status',
    ];

    // =============================
    // Relationships
    // =============================

    /**
     * Department has many employees
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
    // This lets you use $department->name everywhere in code/views
    public function getNameAttribute()
    {
        return $this->department_name;
    }

    // Optional: allow setting via $department->name = '...'
    public function setNameAttribute($value)
    {
        $this->attributes['department_name'] = $value;
    }
}
