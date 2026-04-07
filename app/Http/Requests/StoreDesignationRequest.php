<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDesignationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('designation')?->id ?? $this->route('designation');
        return [
            'name'            => 'required|string|max:100',
            'employee_type'   => 'required|in:teacher,staff',
            'hierarchy_level' => 'required|integer|min:1|max:10',
            'status'          => 'required|in:active,inactive',
        ];
    }
}
