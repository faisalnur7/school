<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeCategory extends Model
{
    protected $fillable = [
        'name',
        'bn_name',
        'description',
        'status',
        'is_transport',
        'student_type',
    ];

}
