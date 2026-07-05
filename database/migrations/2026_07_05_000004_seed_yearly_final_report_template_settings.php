<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now()->toDateTimeString();

        $payload = [
            'paper_orientation' => 'landscape',
            'margin_top_mm' => 8,
            'margin_right_mm' => 8,
            'margin_bottom_mm' => 8,
            'margin_left_mm' => 8,
            'show_watermark' => true,
            'watermark_opacity' => 0.14,
            'watermark_scale' => 96,
            'show_grade_scale' => true,
            'show_student_info' => true,
            'show_table' => true,
            'show_summary' => true,
            'show_position' => true,
            'show_remarks' => true,
            'show_comments' => true,
            'show_signature' => true,
            'show_print_date' => true,
            'school_name_font_size' => 20,
            'school_address_font_size' => 10,
            'report_title_text' => 'PROGRESS REPORT',
            'report_title_font_size' => 20,
            'annual_report_label' => 'Annual Report',
            'school_name_color' => '#5b8f42',
            'school_address_color' => '#5b8f42',
            'report_title_color' => '#2f2f2f',
            'annual_report_color' => '#1f2937',
            'table_border_color' => '#7b7b7b',
            'table_header_bg_color' => '#ffffff',
            'table_header_text_color' => '#111827',
            'table_body_bg_color' => '#ffffff',
            'table_body_text_color' => '#111827',
            'grade_border_color' => '#7b7b7b',
            'position_border_color' => '#9bb66e',
            'position_label_bg_color' => '#b6dd68',
            'position_label_text_color' => '#1f3a1d',
            'position_value_text_color' => '#1f3a1d',
            'promo_box_bg_color' => '#8bc34a',
            'promo_box_text_color' => '#1f3a1d',
            'remarks_title_color' => '#2f2f2f',
            'remarks_text_color' => '#374151',
            'remarks_note_color' => '#1f4d1a',
            'comments_border_color' => '#a3a3a3',
            'comments_text_color' => '#1f2937',
            'signature_line_color' => '#111111',
            'grade_scale_title' => 'Letter Grade',
            'pair_heading_1' => '1st Terminal',
            'pair_heading_2' => '2nd Terminal',
            'pair_heading_3' => '3rd Terminal',
            'grand_total_label' => 'Grand Total',
            'highest_total_label' => 'Highest Marks',
            'position_label_text' => 'POSITION',
            'promoted_text' => 'Promoted',
            'class_teacher_label' => 'Class Teacher',
            'principal_label' => 'Principal',
            'remark_excellent_text' => 'Excellent',
            'remark_good_text' => 'Good',
            'remark_satisfactory_text' => 'Satisfactory',
            'remark_improve_text' => 'Need to be improved',
            'comments_excellent_text' => 'Excellent results! You faithfully perform classroom tasks.',
            'comments_good_text' => 'Good results! Keep up the good work.',
            'comments_default_text' => 'Need to improve performance.',
            'subject_column_widths' => json_encode([
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
            ]),
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if (DB::table('yearly_final_report_template_settings')->where('id', 1)->exists()) {
            DB::table('yearly_final_report_template_settings')->where('id', 1)->update($payload);
            return;
        }

        DB::table('yearly_final_report_template_settings')->insert(array_merge(['id' => 1], $payload));
    }

    public function down(): void
    {
        DB::table('yearly_final_report_template_settings')->where('id', 1)->delete();
    }
};
