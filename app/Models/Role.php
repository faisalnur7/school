<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'description', 'system_key', 'is_reserved'];

    protected function casts(): array
    {
        return ['is_reserved' => 'boolean'];
    }

    public function isPlatformAdmin(): bool
    {
        return $this->system_key === 'platform_admin';
    }

    public function isInstituteSuperAdmin(): bool
    {
        return $this->system_key === 'super_admin';
    }

    public function scopeAssignableToInstituteUsers($query)
    {
        return $query->where('is_reserved', false);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }
}
