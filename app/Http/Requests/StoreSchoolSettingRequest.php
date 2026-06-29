<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolSettingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'short_name'       => 'nullable|string|max:100',
            'address'          => 'required|string',
            'eiin'             => 'nullable|string|max:50',
            'from_class'       => 'nullable|exists:school_classes,id',
            'to_class'         => 'nullable|exists:school_classes,id',
            'slogan'           => 'nullable|string|max:255',
            'website'          => 'nullable|url|max:255',
            'email'            => 'nullable|email|max:255',
            'facebook_page'    => 'nullable|url|max:255',
            'whatsapp_number'  => 'nullable|string|max:20',
            'whatsapp_qr'      => 'nullable|image|max:100',
            'contact_number_1' => 'nullable|string|max:20',
            'contact_number_2' => 'nullable|string|max:20',
            'primary_color'    => 'nullable|string|max:20',
            'secondary_color'  => 'nullable|string|max:20',
            'principal_designation' => 'nullable|string|max:255',
            'principal_name'        => 'nullable|string|max:255',
            'principal_school_name'  => 'nullable|string|max:255',
            'principal_phone'        => 'nullable|string|max:50',
            'transfer_certificate_template' => 'nullable|string|max:10000',
            'testimonial_template'          => 'nullable|string|max:10000',
            'logo'             => 'nullable|image|max:100',
            'favicon'          => 'nullable|file|mimes:ico,png,jpg,jpeg,gif,webp|max:100',
            'letter_head'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:100',
        ];
    }
}
