<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeCategory extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'bn_name',
        'description',
        'status',
        'is_transport',
        'student_type',
    ];

    public function setNameEnAttribute($value): void
    {
        $this->attributes['name'] = $value;
    }
}
