<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['title_bn', 'title_en', 'description', 'due_date', 'course_id', 'teacher_id', 'attachment', 'status'];

    protected $casts = ['due_date' => 'date'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
