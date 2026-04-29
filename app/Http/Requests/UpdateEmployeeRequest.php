<?php
namespace App\Http\Requests;

use App\Models\Designation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $empId = $this->route('employee')->id ?? $this->route('employee');
        $userId = $this->route('employee')->user_id ?? null;
        return [
            'name'                 => 'required|string|max:150',
            'email'                => "required|email|unique:users,email,{$userId}",
            'employee_type'        => 'required|in:teacher,staff',
            'designation_id'       => 'required|exists:designations,id',
            'department_id'        => 'nullable|exists:departments,id',
            'reporting_manager_id' => "nullable|exists:employees,id|different:id",
            'dob'                  => 'nullable|date',
            'gender'               => 'required|in:male,female,other',
            'phone'                => 'nullable|string|max:20',
            'address'              => 'nullable|string',
            'joining_date'         => 'nullable|date',
            'status'               => 'required|in:active,inactive',
            'photo'                => 'nullable|image|max:2048',
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
