<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'branch_id',
        'status'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationship
    public function role(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Role::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    public function employee()
    {
        return $this->hasOne(\App\Models\Employee::class, 'user_id', 'id');
        //                          ↑ your foreign key   ↑ local key (users.id)
    }

    // Helper methods
    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }

// App/Models/User.php
        public function isActive(): bool
        {
            // If no employee record → assume inactive (or adjust logic as needed)
            return optional($this->employee)->status === 'active';
        }

    // Optional: បន្ថែម method សម្រាប់ប្រើងាយៗ
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
