<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['date', 'title', 'description'];

    protected $casts = ['date' => 'date'];
}
