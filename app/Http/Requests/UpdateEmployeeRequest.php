<?php
namespace App\Http\Requests;

use App\Models\Designation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'academic_session_ids' => 'nullable|required_if:employee_type,teacher|array|min:1',
            'academic_session_ids.*' => 'integer|distinct|exists:academic_sessions,id',
            'designation_id'       => 'required|exists:designations,id',
            'department_id'        => 'nullable|exists:departments,id',
            'reporting_manager_id' => "nullable|exists:employees,id|different:id",
            'dob'                  => 'nullable|date',
            'gender'               => 'required|in:male,female,other',
            'phone'                => 'nullable|string|max:20',
            'national_id'          => 'nullable|string|max:50',
            'passport_number'      => 'nullable|string|max:50',
            'address'              => 'nullable|string',
            'notes'                => 'nullable|string|max:5000',
            'emergency_contact_name' => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|string|max:30',
            'emergency_contact_relation' => 'nullable|string|max:50',
            'joining_date'         => 'required|date',
            'status'               => 'required|in:active,inactive',
            'photo'                => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
            'payment_method'       => 'required|in:cash,bank_transfer,mobile_wallet',
            'bank_name'            => 'nullable|string|max:100',
            'account_number'       => 'nullable|string|max:50',
            'mobile_wallet_provider' => 'nullable|string|max:50',
            'mobile_wallet_number'   => 'nullable|string|max:20',
            'qualifications' => 'nullable|array',
            'qualifications.*.id' => 'nullable|integer|exists:employee_qualifications,id',
            'qualifications.*.qualification_id' => ['required', 'integer', Rule::exists('educational_qualifications', 'id')],
            'qualifications.*.institution_name' => 'nullable|string|max:150',
            'qualifications.*.passing_year' => 'nullable|integer|min:1900|max:' . now()->year,
            'qualifications.*.result' => 'nullable|string|max:100',
            'qualifications.*.document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:2048',
            'qualifications.*.remove_document' => 'nullable|boolean',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $desig = Designation::find($this->designation_id);
            if ($desig && $desig->employee_type !== $this->employee_type) {
                $v->errors()->add('designation_id', 'Designation type must match employee type.');
            }

            $employee = $this->route('employee');
            $ownedQualificationIds = $employee?->qualifications()->pluck('id')->all() ?? [];
            foreach ($this->input('qualifications', []) as $index => $row) {
                if (!empty($row['id']) && !in_array((int) $row['id'], $ownedQualificationIds, true)) {
                    $v->errors()->add("qualifications.{$index}.id", 'This qualification record does not belong to the employee.');
                }
                if (!empty($row['qualification_id'])) {
                    $qualification = \App\Models\EducationalQualification::find($row['qualification_id']);
                    if ($qualification?->status === 'inactive' && empty($row['id'])) {
                        $v->errors()->add("qualifications.{$index}.qualification_id", 'Inactive qualifications can only be retained for existing records.');
                    }
                }
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $rows = collect($this->input('qualifications', []))
            ->filter(fn ($row) => collect($row)->except('document')->contains(fn ($value) => filled($value)))
            ->values()
            ->all();

        $this->merge(['qualifications' => $rows]);
    }
}
