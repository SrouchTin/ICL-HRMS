<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Identification extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'identification_type',
        'identification_number',
        'expiration_date',
    ];

    // =============================
    // Relationship
    // =============================

    /**
     * Identification belongs to an Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
