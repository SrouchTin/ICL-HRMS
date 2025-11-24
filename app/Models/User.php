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
        'name', 'email', 'password', 'role_id', 'branch_id', 'status'
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

    // Helper methods
    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Optional: បន្ថែម method សម្រាប់ប្រើងាយៗ
    public function isAdmin(): bool    { return $this->hasRole('admin'); }
    public function isHR(): bool       { return $this->hasRole('hr'); }
    public function isEmployee(): bool { return $this->hasRole('employee'); }
}