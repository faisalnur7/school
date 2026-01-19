<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name_bn',
        'full_name_en',
        'date_of_birth',
        'sex',
        'nationality',
        'religion',
        'blood_group',

        'father_name',
        'father_occupation',
        'father_organization',
        'father_designation',
        'father_location',
        'father_phone',
        'father_email',

        'mother_name',
        'mother_occupation',
        'mother_organization',
        'mother_designation',
        'mother_location',
        'mother_phone',
        'mother_email',

        'present_address',
        'present_phone',
        'present_mobile',

        'permanent_address',
        'permanent_phone',
        'permanent_mobile',

        'guardian_name',
        'guardian_relation',
        'guardian_occupation',
        'guardian_address',
        'guardian_phone',
        'guardian_mobile',
        'guardian_email',

        'previous_school',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];
}
