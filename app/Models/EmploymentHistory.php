<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class EmploymentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'company_name',
        'start_date',
        'end_date',
        'designation',
        'supervisor_name',
        'remark',
        'rate',
        'reason_for_leaving',
    ];

    /**
     * EmploymentHistory belongs to an Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
