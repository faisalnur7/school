<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Section;
use App\Models\YearlyFinalReportTemplateSetting;
use Illuminate\Http\Request;

class YearlyFinalReportTemplateSettingController extends Controller
{
    public function edit()
    {
        $setting = YearlyFinalReportTemplateSetting::current();
        $school = SchoolSetting::current();
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes = SchoolClass::where('status', 1)->orderBy('id')->get();
        $previewReport = [
            'highest' => 495.10,
            'rows' => [
                [
                    'student' => (object) [
                        'full_name_en' => 'Aynun Jariya',
                        'full_name_bn' => 'Aynun Jariya',
                        'student_cid' => '0309',
                        'class_name' => 'Play',
                        'section_name' => 'A',
                    ],
                    'totals' => [
                        1 => ['tutorial' => 87, 'terminal' => 395, 'total' => 482, 'weight' => 20, 'weighted' => 96.4],
                        2 => ['tutorial' => 89, 'terminal' => 384, 'total' => 473, 'weight' => 20, 'weighted' => 94.6],
                        3 => ['tutorial' => 93, 'terminal' => 414, 'total' => 507, 'weight' => 60, 'weighted' => 304.2],
                    ],
                    'grand_total' => 495.10,
                    'position' => 1,
                ],
            ],
        ];

        return view('pages.yearly-final-report.template-settings', compact('setting', 'school', 'sessions', 'classes', 'previewReport'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'margin_top_mm' => ['required', 'numeric', 'min:0', 'max:5'],
            'margin_right_mm' => ['required', 'numeric', 'min:0', 'max:5'],
            'margin_bottom_mm' => ['required', 'numeric', 'min:0', 'max:5'],
            'margin_left_mm' => ['required', 'numeric', 'min:0', 'max:5'],
            'show_watermark' => ['nullable', 'boolean'],
            'watermark_opacity' => ['required', 'numeric', 'min:0', 'max:1'],
            'watermark_scale' => ['required', 'numeric', 'min:10', 'max:100'],
            'show_grade_scale' => ['nullable', 'boolean'],
            'show_student_info' => ['nullable', 'boolean'],
            'show_table' => ['nullable', 'boolean'],
            'show_summary' => ['nullable', 'boolean'],
            'show_position' => ['nullable', 'boolean'],
            'show_remarks' => ['nullable', 'boolean'],
            'show_comments' => ['nullable', 'boolean'],
            'show_signature' => ['nullable', 'boolean'],
            'show_print_date' => ['nullable', 'boolean'],
            'school_name_font_size' => ['required', 'numeric', 'min:8', 'max:40'],
            'school_address_font_size' => ['required', 'numeric', 'min:6', 'max:28'],
            'report_title_text' => ['required', 'string', 'max:255'],
            'report_title_font_size' => ['required', 'numeric', 'min:8', 'max:40'],
            'annual_report_label' => ['required', 'string', 'max:255'],
            'school_name_color' => ['required', 'string', 'max:20'],
            'school_address_color' => ['required', 'string', 'max:20'],
            'report_title_color' => ['required', 'string', 'max:20'],
            'annual_report_color' => ['required', 'string', 'max:20'],
            'table_border_color' => ['required', 'string', 'max:20'],
            'table_header_bg_color' => ['required', 'string', 'max:20'],
            'table_header_text_color' => ['required', 'string', 'max:20'],
            'table_body_bg_color' => ['required', 'string', 'max:20'],
            'table_body_text_color' => ['required', 'string', 'max:20'],
            'grade_border_color' => ['required', 'string', 'max:20'],
            'position_border_color' => ['required', 'string', 'max:20'],
            'position_label_bg_color' => ['required', 'string', 'max:20'],
            'position_label_text_color' => ['required', 'string', 'max:20'],
            'position_value_text_color' => ['required', 'string', 'max:20'],
            'promo_box_bg_color' => ['required', 'string', 'max:20'],
            'promo_box_text_color' => ['required', 'string', 'max:20'],
            'remarks_title_color' => ['required', 'string', 'max:20'],
            'remarks_text_color' => ['required', 'string', 'max:20'],
            'remarks_note_color' => ['required', 'string', 'max:20'],
            'comments_border_color' => ['required', 'string', 'max:20'],
            'comments_text_color' => ['required', 'string', 'max:20'],
            'signature_line_color' => ['required', 'string', 'max:20'],
            'grade_scale_title' => ['required', 'string', 'max:255'],
            'pair_heading_1' => ['required', 'string', 'max:255'],
            'pair_heading_2' => ['required', 'string', 'max:255'],
            'pair_heading_3' => ['required', 'string', 'max:255'],
            'grand_total_label' => ['required', 'string', 'max:255'],
            'highest_total_label' => ['required', 'string', 'max:255'],
            'position_label_text' => ['required', 'string', 'max:255'],
            'promoted_text' => ['required', 'string', 'max:255'],
            'class_teacher_label' => ['required', 'string', 'max:255'],
            'principal_label' => ['required', 'string', 'max:255'],
            'remark_excellent_text' => ['required', 'string', 'max:100'],
            'remark_good_text' => ['required', 'string', 'max:100'],
            'remark_satisfactory_text' => ['required', 'string', 'max:100'],
            'remark_improve_text' => ['required', 'string', 'max:100'],
            'comments_excellent_text' => ['required', 'string', 'max:500'],
            'comments_good_text' => ['required', 'string', 'max:500'],
            'comments_default_text' => ['required', 'string', 'max:500'],
            'subject_column_widths' => ['nullable', 'array'],
            'subject_column_widths.pair1_tutorial' => ['nullable', 'numeric', 'min:1', 'max:50'],
            'subject_column_widths.pair1_terminal' => ['nullable', 'numeric', 'min:1', 'max:50'],
            'subject_column_widths.pair1_total' => ['nullable', 'numeric', 'min:1', 'max:50'],
            'subject_column_widths.pair1_weight' => ['nullable', 'numeric', 'min:1', 'max:50'],
            'subject_column_widths.pair2_tutorial' => ['nullable', 'numeric', 'min:1', 'max:50'],
            'subject_column_widths.pair2_terminal' => ['nullable', 'numeric', 'min:1', 'max:50'],
            'subject_column_widths.pair2_total' => ['nullable', 'numeric', 'min:1', 'max:50'],
            'subject_column_widths.pair2_weight' => ['nullable', 'numeric', 'min:1', 'max:50'],
            'subject_column_widths.pair3_tutorial' => ['nullable', 'numeric', 'min:1', 'max:50'],
            'subject_column_widths.pair3_terminal' => ['nullable', 'numeric', 'min:1', 'max:50'],
            'subject_column_widths.pair3_total' => ['nullable', 'numeric', 'min:1', 'max:50'],
            'subject_column_widths.pair3_weight' => ['nullable', 'numeric', 'min:1', 'max:50'],
            'subject_column_widths.grand_total' => ['nullable', 'numeric', 'min:1', 'max:50'],
            'subject_column_widths.highest' => ['nullable', 'numeric', 'min:1', 'max:50'],
        ]);

        $setting = YearlyFinalReportTemplateSetting::current();
        $subjectColumnWidths = [
            'pair1_tutorial' => 8,
            'pair1_terminal' => 8,
            'pair1_total' => 6,
            'pair1_weight' => 6,
            'pair2_tutorial' => 8,
            'pair2_terminal' => 8,
            'pair2_total' => 6,
            'pair2_weight' => 6,
            'pair3_tutorial' => 8,
            'pair3_terminal' => 8,
            'pair3_total' => 6,
            'pair3_weight' => 6,
            'grand_total' => 8,
            'highest' => 7,
        ];

        foreach ($subjectColumnWidths as $key => $default) {
            $value = data_get($validated, "subject_column_widths.{$key}");
            if (is_numeric($value)) {
                $subjectColumnWidths[$key] = (float) $value;
            }
        }

        $setting->fill(array_merge($validated, [
            'paper_orientation' => 'landscape',
            'show_watermark' => $request->boolean('show_watermark'),
            'show_grade_scale' => $request->boolean('show_grade_scale'),
            'show_student_info' => $request->boolean('show_student_info'),
            'show_table' => $request->boolean('show_table'),
            'show_summary' => $request->boolean('show_summary'),
            'show_position' => $request->boolean('show_position'),
            'show_remarks' => $request->boolean('show_remarks'),
            'show_comments' => $request->boolean('show_comments'),
            'show_signature' => $request->boolean('show_signature'),
            'show_print_date' => $request->boolean('show_print_date'),
            'subject_column_widths' => $subjectColumnWidths,
        ]));
        $setting->save();

        return redirect()
            ->route('result.yearly-final-report.template-settings.edit')
            ->with('success', 'Yearly final report template settings saved.');
    }
}
