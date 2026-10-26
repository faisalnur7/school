<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationalQualification extends Model
{
    protected $fillable = ['name', 'abbreviation', 'status'];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function employees()
    {
        return $this->hasMany(EmployeeQualification::class, 'qualification_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->abbreviation} - {$this->name}";
    }
}
