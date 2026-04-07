<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalaryStructureRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'employee_id'          => 'required|exists:employees,id',
            'designation_id'       => 'required|exists:designations,id',
            'basic_salary'         => 'required|numeric|min:1',
            'house_rent'           => 'nullable|numeric|min:0',
            'medical_allowance'    => 'nullable|numeric|min:0',
            'transport_allowance'  => 'nullable|numeric|min:0',
            'special_allowance'    => 'nullable|numeric|min:0',
            'bonus'                => 'nullable|numeric|min:0',
            'other_deductions'     => 'nullable|numeric|min:0',
            'effective_from'       => 'required|date',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $gross = array_sum(array_map(fn($f) => (float)($this->$f ?? 0),
                ['basic_salary','house_rent','medical_allowance','transport_allowance','special_allowance','bonus']));
            if ((float)($this->other_deductions ?? 0) > $gross) {
                $v->errors()->add('other_deductions', 'Deductions cannot exceed gross salary (৳' . number_format($gross, 2) . ').');
            }
        });
    }
}
