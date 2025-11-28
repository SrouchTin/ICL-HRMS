<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'title',
        'year_awarded',
        'country',
        'program_name',
        'organizer_name',
        'remark',
        'attachment',
    ];

    /**
     * Achievement belongs to an Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
