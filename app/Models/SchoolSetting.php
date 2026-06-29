<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'name', 'short_name', 'address', 'eiin',
        'from_class', 'to_class', 'slogan',
        'website', 'email', 'facebook_page', 'whatsapp_number', 'whatsapp_qr',
        'contact_number_1', 'contact_number_2',
        'primary_color', 'secondary_color',
        'logo', 'favicon', 'letter_head',
        'principal_designation', 'principal_name', 'principal_school_name', 'principal_phone',
        'transfer_certificate_template', 'testimonial_template',
    ];

    public static function current(): self
    {
        return static::firstOrNew(['id' => 1]);
    }
}
