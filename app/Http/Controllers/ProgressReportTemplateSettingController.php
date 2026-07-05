<?php

namespace App\Http\Controllers;

use App\Models\ProgressReportTemplateSetting;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;

class ProgressReportTemplateSettingController extends Controller
{
    public function edit()
    {
        $setting = ProgressReportTemplateSetting::current();
        $school = SchoolSetting::current();

        $previewStudent = [
            'full_name_en' => 'Student Name',
            'student_cid' => '0001',
            'class_name' => 'One - A',
            'roll' => '12',
            'session' => now()->year . '-' . (now()->year + 1),
            'dob' => '20-05-2010',
        ];

        $gradeScale = [
            ['min' => 80, 'max' => 100, 'letter' => 'A+', 'gpa' => 5.0],
            ['min' => 70, 'max' => 79, 'letter' => 'A', 'gpa' => 4.0],
            ['min' => 60, 'max' => 69, 'letter' => 'A-', 'gpa' => 3.5],
            ['min' => 50, 'max' => 59, 'letter' => 'B', 'gpa' => 3.0],
            ['min' => 40, 'max' => 49, 'letter' => 'C', 'gpa' => 2.0],
            ['min' => 33, 'max' => 39, 'letter' => 'D', 'gpa' => 1.0],
            ['min' => 0, 'max' => 32, 'letter' => 'F', 'gpa' => 0.0],
        ];

        $sampleRows = [
            ['subject_name' => 'Bangla', 'full_marks' => 100, 'obtained' => 89, 'highest' => 97, 'grade' => 'A+', 'gpa' => 5.0],
            ['subject_name' => 'English', 'full_marks' => 100, 'obtained' => 81, 'highest' => 95, 'grade' => 'A+', 'gpa' => 5.0],
            ['subject_name' => 'Math', 'full_marks' => 100, 'obtained' => 74, 'highest' => 93, 'grade' => 'A', 'gpa' => 4.0],
            ['subject_name' => 'Science', 'full_marks' => 100, 'obtained' => 68, 'highest' => 90, 'grade' => 'A-', 'gpa' => 3.5],
        ];

        $summary = [
            'fullMarks' => 400,
            'obtained' => 312,
            'percentage' => 78.0,
            'gpa' => 4.38,
            'grade' => 'A',
        ];

        return view('pages.progress-report.template-settings', compact(
            'setting',
            'school',
            'previewStudent',
            'gradeScale',
            'sampleRows',
            'summary'
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'paper_orientation' => ['required', 'in:portrait,landscape'],
            'margin_top_mm' => ['required', 'numeric', 'min:0', 'max:50'],
            'margin_right_mm' => ['required', 'numeric', 'min:0', 'max:50'],
            'margin_bottom_mm' => ['required', 'numeric', 'min:0', 'max:50'],
            'margin_left_mm' => ['required', 'numeric', 'min:0', 'max:50'],
            'show_watermark' => ['nullable', 'boolean'],
            'watermark_opacity' => ['required', 'numeric', 'min:0', 'max:1'],
            'watermark_scale' => ['required', 'numeric', 'min:10', 'max:100'],
            'show_grade_scale' => ['nullable', 'boolean'],
            'show_student_info' => ['nullable', 'boolean'],
            'show_summary' => ['nullable', 'boolean'],
            'show_remarks' => ['nullable', 'boolean'],
            'show_comments' => ['nullable', 'boolean'],
            'show_signature' => ['nullable', 'boolean'],
            'show_print_date' => ['nullable', 'boolean'],
            'school_name_font_size' => ['required', 'numeric', 'min:8', 'max:36'],
            'school_address_font_size' => ['required', 'numeric', 'min:6', 'max:28'],
            'report_title_text' => ['required', 'string', 'max:255'],
            'report_title_font_size' => ['required', 'numeric', 'min:8', 'max:40'],
            'school_name_color' => ['required', 'string', 'max:20'],
            'school_address_color' => ['required', 'string', 'max:20'],
            'report_title_color' => ['required', 'string', 'max:20'],
            'header_border_color' => ['required', 'string', 'max:20'],
            'card_border_color' => ['required', 'string', 'max:20'],
            'table_header_bg_color' => ['required', 'string', 'max:20'],
            'table_header_text_color' => ['required', 'string', 'max:20'],
            'table_border_color' => ['required', 'string', 'max:20'],
            'table_row_alt_bg_color' => ['required', 'string', 'max:20'],
            'student_label_color' => ['required', 'string', 'max:20'],
            'student_value_color' => ['required', 'string', 'max:20'],
            'summary_bg_color' => ['required', 'string', 'max:20'],
            'summary_text_color' => ['required', 'string', 'max:20'],
            'remarks_title_color' => ['required', 'string', 'max:20'],
            'remarks_text_color' => ['required', 'string', 'max:20'],
            'signature_line_color' => ['required', 'string', 'max:20'],
            'remark_excellent_text' => ['required', 'string', 'max:100'],
            'remark_good_text' => ['required', 'string', 'max:100'],
            'remark_satisfactory_text' => ['required', 'string', 'max:100'],
            'remark_improve_text' => ['required', 'string', 'max:100'],
            'comments_excellent_text' => ['required', 'string', 'max:500'],
            'comments_good_text' => ['required', 'string', 'max:500'],
            'comments_default_text' => ['required', 'string', 'max:500'],
            'school_logo_max_width_mm' => ['required', 'numeric', 'min:8', 'max:40'],
            'subject_column_widths' => ['nullable', 'array'],
            'subject_column_widths.subject' => ['nullable', 'numeric', 'min:5', 'max:50'],
            'subject_column_widths.full_marks' => ['nullable', 'numeric', 'min:5', 'max:50'],
            'subject_column_widths.obtained_marks' => ['nullable', 'numeric', 'min:5', 'max:50'],
            'subject_column_widths.highest_marks' => ['nullable', 'numeric', 'min:5', 'max:50'],
            'subject_column_widths.total_marks' => ['nullable', 'numeric', 'min:5', 'max:50'],
            'subject_column_widths.letter_grade' => ['nullable', 'numeric', 'min:5', 'max:50'],
            'subject_column_widths.grade_point' => ['nullable', 'numeric', 'min:5', 'max:50'],
        ]);

        $setting = ProgressReportTemplateSetting::current();
        $subjectColumnWidths = [
            'subject' => 30,
            'full_marks' => 10,
            'obtained_marks' => 12,
            'highest_marks' => 12,
            'total_marks' => 12,
            'letter_grade' => 12,
            'grade_point' => 12,
        ];

        foreach ($subjectColumnWidths as $key => $default) {
            $value = data_get($validated, "subject_column_widths.{$key}");
            if (is_numeric($value)) {
                $subjectColumnWidths[$key] = (float) $value;
            }
        }

        $setting->fill(array_merge($validated, [
            'show_watermark' => $request->boolean('show_watermark'),
            'show_grade_scale' => $request->boolean('show_grade_scale'),
            'show_student_info' => $request->boolean('show_student_info'),
            'show_summary' => $request->boolean('show_summary'),
            'show_remarks' => $request->boolean('show_remarks'),
            'show_comments' => $request->boolean('show_comments'),
            'show_signature' => $request->boolean('show_signature'),
            'show_print_date' => $request->boolean('show_print_date'),
            'subject_column_widths' => $subjectColumnWidths,
        ]));
        $setting->save();

        return redirect()
            ->route('result.progress-report.template-settings.edit')
            ->with('success', 'Terminal result template settings saved.');
    }
}
