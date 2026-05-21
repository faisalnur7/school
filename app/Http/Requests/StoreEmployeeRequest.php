<?php
namespace App\Http\Requests;

use App\Models\Designation;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'                 => 'required|string|max:150',
            'email'                => 'required|email|unique:users,email',
            'password'             => 'required|string|min:6',
            'employee_type'        => 'required|in:teacher,staff',
            'designation_id'       => 'required|exists:designations,id',
            'department_id'        => 'nullable|exists:departments,id',
            'reporting_manager_id' => 'nullable|exists:employees,id',
            'dob'                  => 'nullable|date',
            'gender'               => 'required|in:male,female,other',
            'phone'                => 'nullable|string|max:20',
            'address'              => 'nullable|string',
            'joining_date'         => 'nullable|date',
            'photo'                => 'nullable|image|max:100',
            'payment_method'       => 'nullable|in:cash,bank_transfer,mobile_wallet',
            'bank_name'            => 'nullable|string|max:100',
            'account_number'       => 'nullable|string|max:50',
            'mobile_wallet_provider' => 'nullable|string|max:50',
            'mobile_wallet_number'   => 'nullable|string|max:20',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $desig = Designation::find($this->designation_id);
            if ($desig && $desig->employee_type !== $this->employee_type) {
                $v->errors()->add('designation_id', 'Designation type must match employee type.');
            }
        });
    }
}
