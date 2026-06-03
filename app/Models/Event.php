<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'location',
        'image',
        'is_published',
        'published_at',
        'sort_order',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeVisibleOnWebsite($query)
    {
        return $query->published();
    }

    public function isVisibleOnWebsite(): bool
    {
        return $this->is_published
            && ($this->published_at === null || $this->published_at->isPast());
    }
}
