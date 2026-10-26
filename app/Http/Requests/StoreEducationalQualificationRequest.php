<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEducationalQualificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $qualificationId = $this->route('educational_qualification')?->id
            ?? $this->route('educational_qualification');

        return [
            'name' => ['required', 'string', 'max:150', Rule::unique('educational_qualifications', 'name')->ignore($qualificationId)],
            'abbreviation' => ['required', 'string', 'max:30', Rule::unique('educational_qualifications', 'abbreviation')->ignore($qualificationId)],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
