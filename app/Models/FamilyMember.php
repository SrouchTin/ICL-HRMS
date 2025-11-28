<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class FamilyMember extends Model
{
    use HasFactory;

    protected $table = "family_members";
    protected $fillable = [
        'employee_id',
        'name',
        'relationship',
        'dob',
        'gender',
        'nationality',
        'tax_filing',
        'phone_number',
        'remark',
        'attachment',
    ];

    /**
     * FamilyMember belongs to an Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
