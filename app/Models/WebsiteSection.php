<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_page_id',
        'title',
        'section_key',
        'content',
        'image',
        'image_position',
        'sort_order',
        'is_active',
    ];

    public function page()
    {
        return $this->belongsTo(WebsitePage::class, 'website_page_id');
    }
}
