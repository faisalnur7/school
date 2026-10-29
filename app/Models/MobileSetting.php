<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileSetting extends Model
{
    protected $fillable = [
        'is_active',
        'maintenance_message',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            ['is_active' => true],
        );
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
