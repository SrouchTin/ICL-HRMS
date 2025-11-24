<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'attachment_name',
        'file_path',
    ];

    /**
     * Attachment belongs to an Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
