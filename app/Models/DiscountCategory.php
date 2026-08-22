<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCategory extends Model
{
    protected $fillable = ['name'];

    public function freeStudentships()
    {
        return $this->hasMany(FreeStudentship::class);
    }
}
