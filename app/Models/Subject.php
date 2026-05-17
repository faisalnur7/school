<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type',
        'has_multiple_papers',
        'combine_papers_for_result',
        'parent_id',
        'is_parent',
        'is_paper',
        'creative_marks',
        'mcq_marks',
        'practical_marks',
        'viva_marks',
        'tutorial_marks',
        'pass_mark',
        'is_active',
    ];

    protected $casts = [
        'has_multiple_papers' => 'boolean',
        'combine_papers_for_result' => 'boolean',
        'is_parent' => 'boolean',
        'is_paper' => 'boolean',
        'is_active' => 'boolean',
        'creative_marks' => 'decimal:2',
        'mcq_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'viva_marks' => 'decimal:2',
        'tutorial_marks' => 'decimal:2',
        'pass_mark' => 'decimal:2',
    ];

    /**
     * Boot the model
     */
    protected static function booted()
    {
        static::deleting(function ($subject) {
            Log::alert('SUBJECT DELETION TRIGGERED', [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'is_forced' => $subject->isForceDeleting(),
                'trace' => collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5))->map(function ($frame) {
                    return $frame['function'].' at '.($frame['file'] ?? 'N/A').':'.($frame['line'] ?? 'N/A');
                })->toArray(),
            ]);
        });

        // Auto-update is_parent flag when parent_id is set/cleared
        static::saving(function ($subject) {
            if ($subject->parent_id) {
                $subject->is_paper = true;
                // Update parent to be marked as has_multiple_papers and is_parent
                $parent = static::find($subject->parent_id);
                if ($parent && !$parent->is_parent) {
                    $parent->is_parent = true;
                    $parent->has_multiple_papers = true;
                    $parent->save();
                }
            } else {
                $subject->is_paper = false;
            }
        });

        static::saved(function ($subject) {
            // If this subject has papers, ensure is_parent is set
            if ($subject->papers()->exists()) {
                $subject->is_parent = true;
                $subject->has_multiple_papers = true;
                $subject->saveQuietly();
            }
        });
    }

    /**
     * Calculate total marks automatically
     */
    public function getTotalMarksAttribute(): float
    {
        return (float) $this->creative_marks
            + (float) $this->mcq_marks
            + (float) $this->practical_marks
            + (float) $this->viva_marks;
    }

    /**
     * Get effective marks for a specific class (uses class config if exists, otherwise subject defaults)
     */
    public function getEffectiveMarksForClass(?int $classId = null): array
    {
        if ($classId) {
            $config = $this->classConfigs()->where('school_class_id', $classId)->first();
            if ($config) {
                return [
                    'creative_marks' => (float) $config->creative_marks,
                    'mcq_marks' => (float) $config->mcq_marks,
                    'practical_marks' => (float) $config->practical_marks,
                    'viva_marks' => (float) $config->viva_marks,
                    'total_marks' => (float) $config->total_marks,
                    'pass_mark' => (float) $config->pass_mark,
                ];
            }
        }

        return [
            'creative_marks' => (float) $this->creative_marks,
            'mcq_marks' => (float) $this->mcq_marks,
            'practical_marks' => (float) $this->practical_marks,
            'viva_marks' => (float) $this->viva_marks,
            'total_marks' => (float) $this->total_marks,
            'pass_mark' => (float) $this->pass_mark,
        ];
    }

    /**
     * Check if at least one mark field is greater than 0
     */
    public function hasValidMarks(): bool
    {
        return $this->creative_marks > 0
            || $this->mcq_marks > 0
            || $this->practical_marks > 0
            || $this->viva_marks > 0
            || $this->tutorial_marks > 0;
    }

    /**
     * Check if pass mark is valid (must be <= total marks)
     */
    public function hasValidPassMark(): bool
    {
        return $this->pass_mark <= $this->total_marks;
    }

    /**
     * Relationships
     */
    /**
     * Parent subject (for combined/paper subjects)
     */
    public function parent()
    {
        return $this->belongsTo(Subject::class, 'parent_id');
    }

    /**
     * Child papers (for combined subjects)
     */
    public function papers()
    {
        return $this->hasMany(Subject::class, 'parent_id');
    }

    /**
     * Class assignments
     */
    public function classAssignments()
    {
        return $this->hasMany(SubjectClassAssignment::class, 'subject_id');
    }

    /**
     * Class-wise marks configuration
     */
    public function classConfigs()
    {
        return $this->hasMany(SubjectClassConfig::class, 'subject_id');
    }

    /**
     * Get config for a specific class
     */
    public function getConfigForClass(int $classId): ?SubjectClassConfig
    {
        return $this->classConfigs()->where('school_class_id', $classId)->first();
    }

    /**
     * Classes this subject is assigned to (via assignments)
     */
    public function schoolClasses()
    {
        return $this->belongsToMany(SchoolClass::class, 'subject_class_assignments', 'subject_id', 'school_class_id')
            ->withPivot(['group_id', 'gender', 'religion', 'is_optional', 'is_compulsory', 'exclusive_group_key'])
            ->withTimestamps();
    }

    /**
     * Student subject mappings
     */
    public function studentSubjects()
    {
        return $this->hasMany(StudentSubject::class, 'subject_id');
    }

    /**
     * Students taking this subject
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_subjects', 'subject_id', 'student_id')
            ->withPivot(['school_class_id', 'academic_session_id', 'is_optional', 'is_mandatory'])
            ->withTimestamps();
    }

    /**
     * Scope for active subjects
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for mandatory subjects
     */
    public function scopeMandatory($query)
    {
        return $query->where('type', 'mandatory');
    }

    /**
     * Scope for optional subjects
     */
    public function scopeOptional($query)
    {
        return $query->where('type', 'optional');
    }

    /**
     * Scope for parent subjects (combined subjects)
     */
    public function scopeParents($query)
    {
        return $query->where('is_parent', true);
    }

    /**
     * Scope for paper subjects
     */
    public function scopePapers($query)
    {
        return $query->where('is_paper', true);
    }

    /**
     * Scope for subjects without parent (standalone)
     */
    public function scopeStandalone($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Get the type label
     */
    public function getTypeLabelAttribute(): string
    {
        return ucfirst($this->type);
    }

    /**
     * Get papers count
     */
    public function getPapersCountAttribute(): int
    {
        return $this->papers()->count();
    }

    /**
     * Check if subject is combined (has papers)
     */
    public function getIsCombinedAttribute(): bool
    {
        return $this->is_parent || $this->has_multiple_papers;
    }

    /**
     * Get display name with paper indicator
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->name;
        if ($this->is_paper && $this->parent) {
            $name .= ' (' . $this->parent->name . ')';
        }
        return $name;
    }

    /**
     * Validate marks total
     */
    public function validateMarks(): array
    {
        $errors = [];
        
        if (!$this->hasValidMarks()) {
            $errors[] = 'At least one mark field must be greater than 0.';
        }
        
        if (!$this->hasValidPassMark()) {
            $errors[] = 'Pass mark cannot exceed total marks.';
        }

        return $errors;
    }
}
