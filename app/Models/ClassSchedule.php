<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSchedule extends Model
{
    protected $fillable = [
        'name',
        'kind',
        'start_time',
        'end_time',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function routines(): HasMany
    {
        return $this->hasMany(ClassRoutine::class, 'time_schedule_id');
    }

    public function isRoutineSlot(): bool
    {
        return $this->kind === 'teaching';
    }

    public function getFormattedStartTimeAttribute(): string
    {
        return date('g:i A', strtotime($this->start_time));
    }

    public function getFormattedEndTimeAttribute(): string
    {
        return date('g:i A', strtotime($this->end_time));
    }
}
