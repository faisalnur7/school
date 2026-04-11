<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    protected $fillable = ['name', 'bn_name'];

    public function studentsAsFather()
    {
        return $this->hasMany(Student::class, 'fathers_profession_id');
    }

    public function studentsAsMother()
    {
        return $this->hasMany(Student::class, 'mothers_profession_id');
    }
}
