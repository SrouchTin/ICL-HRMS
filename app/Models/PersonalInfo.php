<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class PersonalInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'salutation',
        'full_name_kh',
        'full_name_en',
        'dob',
        'gender',
        'marital_status',
        'nationality',
        'blood_group',
        'religion',
        'bank_account_number',
        'tax_number',
        'joining_date',
        'effective_date',
        'end_date'
    ];



    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    // app/Models/PersonalInfo.php

}
