<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultEmailStatus extends Model
{
    protected $fillable = [
        'context_key',
        'report_type',
        'student_id',
        'exam_id',
        'session_id',
        'class_id',
        'section_id',
        'is_sent',
        'sent_at',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'exam_id' => 'integer',
        'session_id' => 'integer',
        'class_id' => 'integer',
        'section_id' => 'integer',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
    ];
}
