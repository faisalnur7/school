<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yearly_final_report_template_settings', function (Blueprint $table) {
            $table->id();
            $table->string('paper_orientation')->default('landscape');
            $table->decimal('margin_top_mm', 6, 2)->default(8);
            $table->decimal('margin_right_mm', 6, 2)->default(8);
            $table->decimal('margin_bottom_mm', 6, 2)->default(8);
            $table->decimal('margin_left_mm', 6, 2)->default(8);
            $table->boolean('show_watermark')->default(true);
            $table->decimal('watermark_opacity', 6, 3)->default(0.14);
            $table->decimal('watermark_scale', 6, 2)->default(96);
            $table->boolean('show_grade_scale')->default(true);
            $table->boolean('show_student_info')->default(true);
            $table->boolean('show_table')->default(true);
            $table->boolean('show_summary')->default(true);
            $table->boolean('show_position')->default(true);
            $table->boolean('show_remarks')->default(true);
            $table->boolean('show_comments')->default(true);
            $table->boolean('show_signature')->default(true);
            $table->boolean('show_print_date')->default(true);
            $table->decimal('school_name_font_size', 6, 2)->default(24);
            $table->decimal('school_address_font_size', 6, 2)->default(12);
            $table->string('report_title_text')->default('PROGRESS REPORT');
            $table->decimal('report_title_font_size', 6, 2)->default(22);
            $table->string('annual_report_label')->default('Annual Report');
            $table->string('school_name_color', 20)->default('#5b8f42');
            $table->string('school_address_color', 20)->default('#5b8f42');
            $table->string('report_title_color', 20)->default('#2f2f2f');
            $table->string('annual_report_color', 20)->default('#333333');
            $table->string('table_border_color', 20)->default('#7b7b7b');
            $table->string('table_header_bg_color', 20)->default('#f3f4f6');
            $table->string('table_header_text_color', 20)->default('#111827');
            $table->string('table_body_bg_color', 20)->default('#ffffff');
            $table->string('table_body_text_color', 20)->default('#222222');
            $table->string('grade_border_color', 20)->default('#7b7b7b');
            $table->string('position_border_color', 20)->default('#8ca06e');
            $table->string('position_label_bg_color', 20)->default('#9ccf63');
            $table->string('position_label_text_color', 20)->default('#1f3a1d');
            $table->string('position_value_text_color', 20)->default('#243524');
            $table->string('promo_box_bg_color', 20)->default('#7fbf3a');
            $table->string('promo_box_text_color', 20)->default('#17310e');
            $table->string('remarks_title_color', 20)->default('#444444');
            $table->string('remarks_text_color', 20)->default('#3f3f3f');
            $table->string('remarks_note_color', 20)->default('#1f4d1a');
            $table->string('comments_border_color', 20)->default('#a3a3a3');
            $table->string('comments_text_color', 20)->default('#333333');
            $table->string('signature_line_color', 20)->default('#111111');
            $table->string('grade_scale_title')->default('Letter Grade');
            $table->string('pair_heading_1')->default('1st Terminal');
            $table->string('pair_heading_2')->default('2nd Terminal');
            $table->string('pair_heading_3')->default('3rd Terminal');
            $table->string('grand_total_label')->default('Grand Total');
            $table->string('highest_total_label')->default('Highest Marks');
            $table->string('position_label_text')->default('POSITION');
            $table->string('promoted_text')->default('Promoted');
            $table->string('class_teacher_label')->default('Class Teacher');
            $table->string('principal_label')->default('Principal');
            $table->string('remark_excellent_text')->default('Excellent');
            $table->string('remark_good_text')->default('Good');
            $table->string('remark_satisfactory_text')->default('Satisfactory');
            $table->string('remark_improve_text')->default('Need to be improved');
            $table->text('comments_excellent_text');
            $table->text('comments_good_text');
            $table->text('comments_default_text');
            $table->json('subject_column_widths')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yearly_final_report_template_settings');
    }
};
