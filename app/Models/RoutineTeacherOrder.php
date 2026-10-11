<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutineTeacherOrder extends Model
{
    protected $fillable = [
        'academic_session_id',
        'teacher_id',
        'sort_order',
    ];

    protected $casts = [
        'academic_session_id' => 'integer',
        'teacher_id' => 'integer',
        'sort_order' => 'integer',
    ];
}
