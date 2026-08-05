<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectClassConfig extends Model
{
    protected $table = 'subject_class_configs';

    protected $fillable = [
        'subject_id',
        'school_class_id',
        'creative_marks',
        'mcq_marks',
        'practical_marks',
        'viva_marks',
        'tutorial_marks',
        'pass_mark',
    ];

    protected $casts = [
        'creative_marks' => 'decimal:2',
        'mcq_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'viva_marks' => 'decimal:2',
        'tutorial_marks' => 'decimal:2',
        'pass_mark' => 'decimal:2',
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

    /**
     * Get total marks
     */
    public function getTotalMarksAttribute(): float
    {
        return (float) $this->creative_marks
            + (float) $this->mcq_marks
            + (float) $this->practical_marks
            + (float) $this->viva_marks;
    }

    /**
     * Scope for a specific class
     */
    public function forClass($query, $classId)
    {
        return $query->where('school_class_id', $classId);
    }
}
