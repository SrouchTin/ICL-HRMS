<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Contact extends Model
{
    use HasFactory;

    protected $table = "contacts";
    protected $fillable = [
        'employee_id',
        'phone_number',
        'home_phone',
        'office_phone',
        'email',
    ];

    /**
     * Contact belongs to an Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
