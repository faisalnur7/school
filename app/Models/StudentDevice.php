<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDevice extends Model
{
    protected $fillable = [
        'user_id',
        'student_id',
        'fcm_token',
        'platform',
        'app_version',
        'last_seen_at',
        'is_active',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
