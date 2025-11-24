<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class EmergencyContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'contact_person',
        'relationship',
        'phone_number',
    ];

    /**
     * EmergencyContact belongs to an Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
