<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationHistory extends Model
{
    use HasFactory;

    protected $table = 'education_histories'; // optional if table name is plural

    protected $fillable = [
        'employee_id',
        'degree',
        'field_of_study',
        'institution',
        'start_year',
        'end_year',
        'grade',
    ];
}