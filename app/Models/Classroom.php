<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = [
        'name_bn',
        'name_en',
        'capacity',
        'location',
    ];
}
