<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('department')?->id ?? $this->route('department');

        return [
            'name'          => 'required|string|max:100|unique:departments,name,' . $id,
            'employee_type' => 'required|in:teacher,staff',
            'status'        => 'required|in:active,inactive',
        ];
    }
}