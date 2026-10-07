<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamRoutine extends Model
{
    protected $fillable = [
        'academic_session_id', 'exam_id', 'school_class_id', 'group_id',
        'morning_start_time', 'morning_end_time', 'noon_start_time', 'noon_end_time',
    ];

    protected $casts = [
        'morning_start_time' => 'datetime:H:i',
        'morning_end_time' => 'datetime:H:i',
        'noon_start_time' => 'datetime:H:i',
        'noon_end_time' => 'datetime:H:i',
    ];

    public function academicSession(): BelongsTo { return $this->belongsTo(AcademicSession::class); }
    public function exam(): BelongsTo { return $this->belongsTo(Exam::class); }
    public function schoolClass(): BelongsTo { return $this->belongsTo(SchoolClass::class); }
    public function group(): BelongsTo { return $this->belongsTo(Group::class); }
    public function items(): HasMany { return $this->hasMany(ExamRoutineItem::class)->orderBy('sort_order'); }
}
