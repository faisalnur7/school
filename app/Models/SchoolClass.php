<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolClass extends Model
{
    protected $fillable = [
        'name_en',
        'name_bn',
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'integer',
    ];

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_class_assignments', 'school_class_id', 'subject_id')
            ->withPivot(['group_id', 'gender', 'religion', 'is_optional', 'is_compulsory', 'exclusive_group_key'])
            ->withTimestamps();
    }
}
