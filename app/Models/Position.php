<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'position_code',
        'position_name',
        'description',
        'status',
    ];

    /**
     * Position has many employees
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
