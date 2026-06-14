<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCertificateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transfer_certificate_template' => 'nullable|string|max:10000',
            'testimonial_template' => 'nullable|string|max:10000',
        ];
    }
}
