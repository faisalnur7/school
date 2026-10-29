<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileAppFeature extends Model
{
    protected $fillable = [
        'key',
        'label',
        'icon',
        'flutter_route',
        'api_capability',
        'is_enabled',
        'sort_order',
        'updated_by',
    ];

    protected $casts = [
        'api_capability' => 'array',
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
