<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdCardTemplate extends Model
{
    protected $fillable = [
        'name', 'background_image', 'orientation',
        'design_front', 'design_back',
        'front_bg_image', 'back_bg_image',
        'front_bg_color', 'back_bg_color',
    ];

    protected $casts = [
        'design_front' => 'array',
        'design_back'  => 'array',
    ];
}
