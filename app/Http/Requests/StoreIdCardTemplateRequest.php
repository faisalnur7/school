<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIdCardTemplateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'background_image' => 'nullable|image|max:4096',
            'orientation'      => 'required|in:portrait,landscape',
        ];
    }
}
