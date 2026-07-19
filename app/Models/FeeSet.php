<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bn_name',
        'academic_session_id',
        'school_class_id',
        'group_id',
        'frequency',
        'due_date',
        'month',
        'description',
        'status',
    ];

    protected $casts = [
        'months' => 'array',
        'due_date' => 'date',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function items()
    {
        return $this->hasMany(FeeSetItem::class);
    }

    public function academic_sessions()
    {
        return $this->belongsTo(AcademicSession::class);
    }
}
