<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'name',
        'type',
        'exam_category',
        'pair_no',
        'pair_weight_percent',
        'academic_session_id',
        'year',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'pair_no'    => 'integer',
        'pair_weight_percent' => 'integer',
    ];

    const TYPE_TERMINAL = 'term';
    const TYPE_TUTORIAL = 'tutorial';

    const TYPES = [
        self::TYPE_TERMINAL => 'Terminal Exam',
        self::TYPE_TUTORIAL => 'Tutorial Exam',
    ];

    const STATUS_DRAFT     = 'draft';
    const STATUS_PUBLISHED = 'published';

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function examSubjects(): HasMany
    {
        return $this->hasMany(ExamSubject::class);
    }

    public function marks(): HasMany
    {
        return $this->hasMany(ExamMark::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function getExamCategoryAttribute(?string $value): string
    {
        if ($value) {
            return $value;
        }

        return $this->type === self::TYPE_TERMINAL ? 'terminal' : 'tutorial';
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }
}
