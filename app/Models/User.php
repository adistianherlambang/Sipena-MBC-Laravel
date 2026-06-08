<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['nama', 'username', 'password', 'role', 'is_active', 'last_login'])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_login' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function assignedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'assigned_admin_id');
    }

    public function escalatedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'escalated_to_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(ComplaintResponse::class, 'user_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(StatusLog::class, 'changed_by');
    }
}
