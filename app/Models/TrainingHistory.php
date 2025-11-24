<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class TrainingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'institute',
        'subject',
        'start_date',
        'end_date',
        'remark',
        'attachment',
    ];

    /**
     * TrainingHistory belongs to an Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
