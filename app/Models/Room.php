<?php

namespace App\Models;

use App\Enums\RoomType;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'building_id',
        'department_id',
        'name',
        'code',
        'floor_number',
        'room_type',
        'seating_capacity',
        'is_active',
    ];

    protected $casts = [
        'room_type' => RoomType::class,
        'is_active' => 'boolean',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
