<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamRoutineItem extends Model
{
    protected $fillable = ['exam_routine_id', 'subject_id', 'sort_order', 'slot', 'exam_date'];

    protected $casts = ['exam_date' => 'date', 'sort_order' => 'integer'];

    public function routine(): BelongsTo { return $this->belongsTo(ExamRoutine::class, 'exam_routine_id'); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }

    public function getDayNameAttribute(): string
    {
        return $this->exam_date instanceof Carbon ? $this->exam_date->format('l') : Carbon::parse($this->exam_date)->format('l');
    }
}
