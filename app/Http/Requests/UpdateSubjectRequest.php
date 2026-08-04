<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subject = $this->route('subject');

        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:subjects,code,' . $subject->id,
            'type' => 'required|in:mandatory,optional',
            'has_multiple_papers' => 'nullable|boolean',
            'combine_papers_for_result' => 'nullable|boolean',
            'parent_id' => 'nullable|exists:subjects,id',
            'is_parent' => 'nullable|boolean',
            'is_paper' => 'nullable|boolean',
            'creative_marks' => 'nullable|numeric|min:0',
            'mcq_marks' => 'nullable|numeric|min:0',
            'practical_marks' => 'nullable|numeric|min:0',
            'viva_marks' => 'nullable|numeric|min:0',
            'tutorial_marks' => 'nullable|numeric|min:0',
            'pass_mark' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'assign_to_class' => 'nullable|boolean',
            'school_class_ids' => 'nullable|array',
            'school_class_ids.*' => 'exists:school_classes,id',
            'group_id' => 'nullable|exists:groups,id',
            'gender' => 'nullable|in:all,male,female',
            'religion' => 'nullable|string',
            'is_optional' => 'nullable|boolean',
            'exclusive_group_key' => 'nullable|string|max:100',
            // Class-wise marks override
            'class_configs' => 'nullable|array',
            'class_configs.*.class_id' => 'required_with:class_configs|exists:school_classes,id',
            'class_configs.*.creative_marks' => 'nullable|numeric|min:0',
            'class_configs.*.mcq_marks' => 'nullable|numeric|min:0',
            'class_configs.*.practical_marks' => 'nullable|numeric|min:0',
            'class_configs.*.viva_marks' => 'nullable|numeric|min:0',
            'class_configs.*.tutorial_marks' => 'nullable|numeric|min:0',
            'class_configs.*.pass_mark' => 'nullable|numeric|min:0',
            // Papers
            'papers' => 'nullable|array',
            'papers.*.name' => 'required_with:papers|string|max:255',
            'papers.*.code' => 'nullable|string|max:50|unique:subjects,code',
            'papers.*.creative_marks' => 'nullable|numeric|min:0',
            'papers.*.mcq_marks' => 'nullable|numeric|min:0',
            'papers.*.practical_marks' => 'nullable|numeric|min:0',
            'papers.*.viva_marks' => 'nullable|numeric|min:0',
            'papers.*.tutorial_marks' => 'nullable|numeric|min:0',
            'papers.*.pass_mark' => 'nullable|numeric|min:0',
        ];
    }
}
