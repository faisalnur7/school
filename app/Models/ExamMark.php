<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamMark extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'subject_id',
        'cq_marks',
        'mcq_marks',
        'practical_marks',
        'viva_marks',
        'tutorial_marks',
        'total',
        'is_absent',
        'letter_grade',
        'gpa',
    ];

    protected $casts = [
        'cq_marks'        => 'decimal:2',
        'mcq_marks'       => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'viva_marks'      => 'decimal:2',
        'tutorial_marks'  => 'decimal:2',
        'total'           => 'decimal:2',
        'gpa'             => 'decimal:2',
        'is_absent'       => 'boolean',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
