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
        if ($this->gender !== 'all' && $this->normalizeGender($gender) !== $this->gender) {
            return false;
        }

        if ($this->religion !== 'all' && $this->normalizeReligion($religion) !== $this->religion) {
            return false;
        }

        return true;
    }

    private function normalizeGender($gender): string
    {
        return match ($gender) {
            1, '1', 'male', 'm' => 'male',
            2, '2', 'female', 'f' => 'female',
            default => 'all',
        };
    }

    private function normalizeReligion($religion): string
    {
        if (is_int($religion) || ctype_digit((string) $religion)) {
            return Student::religionTokenFromId((int) $religion);
        }

        $normalized = strtolower(trim((string) $religion));

        return match ($normalized) {
            'islam', 'muslim' => 'islam',
            'hindu', 'hinduism' => 'hindu',
            'christian', 'christianity' => 'christian',
            'buddhist', 'buddhism' => 'buddhist',
            default => $normalized,
        };
    }
}
