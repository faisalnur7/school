<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_super_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission($permission)
    {
        if ($this->is_super_admin) {
            return true;
        }

        if (!$this->role_id) {
            return false;
        }

        return $this->role()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', trim((string) $permission));
            })
            ->exists();
    }

    public function hasAnyPermission($permissions)
    {
        if ($this->is_super_admin) {
            return true;
        }

        if (!$this->role_id) {
            return false;
        }

        $names = collect((array) $permissions)
            ->map(fn ($permission) => trim((string) $permission))
            ->filter()
            ->values()
            ->all();

        if (empty($names)) {
            return false;
        }

        return $this->role()
            ->whereHas('permissions', function ($query) use ($names) {
                $query->whereIn('name', $names);
            })
            ->exists();
    }
}
