<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationHistory extends Model
{
    use HasFactory;

    protected $table = 'education_histories'; 

    protected $fillable = [
        'employee_id',
        'degree',
        'institute',
        'subject',
        'start_date',
        'end_date',
        'remark',
    ];
}