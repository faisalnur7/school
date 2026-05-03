<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectClassAssignment extends Model
{
    protected $table = 'subject_class_assignments';

    protected $fillable = [
        'subject_id',
        'school_class_id',
        'group_id',
        'gender',
        'religion',
        'is_optional',
        'is_compulsory',
        'exclusive_group_key',
        'is_active',
    ];

    protected $casts = [
        'is_optional' => 'boolean',
        'is_compulsory' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Scope for active assignments
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific class
     */
    public function forClass($query, $classId)
    {
        return $query->where('school_class_id', $classId);
    }

    /**
     * Scope for specific group
     */
    public function forGroup($query, $groupId = null)
    {
        return $query->where(function ($q) use ($groupId) {
            $q->whereNull('group_id')->orWhere('group_id', $groupId);
        });
    }

    /**
     * Check if this assignment applies to a student based on gender and religion
     */
    public function appliesToStudent($gender, $religion): bool
    {
        // If gender is 'all', it applies to everyone
        if ($this->gender === 'all') {
            return true;
        }

        // If religion is 'all', it applies to everyone
        if ($this->religion === 'all') {
            return true;
        }

        // Otherwise check specific gender and religion
        return $this->gender === $gender && $this->religion === $religion;
    }
}