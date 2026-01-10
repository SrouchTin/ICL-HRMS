<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Support\Str;
// app/Models/User.php

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = "users";
    protected $primaryKey = 'id';
    protected $fillable = [
        'username',
        'employee_id',
        'password',
        'role_id',
        'branch_id',
        'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    // Relationships
public function personalInfo()
{
    return $this->hasOneThrough(
        PersonalInfo::class,
        Employee::class,
        'id',
        'employee_id',
        'employee_id',
        'id'
    );
}
    public function role(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Role::class,'role_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    // app/Models/User.php
public function employee()
{
    return $this->belongsTo(Employee::class, 'employee_id', 'id');
}

    // Helper methods
    public function hasRole(string $roleName): bool
    {
        return strtolower($this->role?->name ?? '') === strtolower($roleName);
    }

    public function isActive(): bool
    {
        return optional($this->employee)->status === 'active';
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
    public function isHR(): bool
    {
        return $this->hasRole('hr');
    }
    public function isEmployee(): bool
    {
        return $this->hasRole('employee');
    }
}
