<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_report_template_settings', function (Blueprint $table) {
            $table->id();
            $table->string('paper_orientation')->default('portrait');
            $table->decimal('margin_top_mm', 6, 2)->default(8);
            $table->decimal('margin_right_mm', 6, 2)->default(8);
            $table->decimal('margin_bottom_mm', 6, 2)->default(8);
            $table->decimal('margin_left_mm', 6, 2)->default(8);
            $table->boolean('show_watermark')->default(true);
            $table->decimal('watermark_opacity', 6, 3)->default(0.08);
            $table->decimal('watermark_scale', 6, 2)->default(78);
            $table->boolean('show_grade_scale')->default(true);
            $table->boolean('show_student_info')->default(true);
            $table->boolean('show_summary')->default(true);
            $table->boolean('show_remarks')->default(true);
            $table->boolean('show_comments')->default(true);
            $table->boolean('show_signature')->default(true);
            $table->boolean('show_print_date')->default(true);
            $table->decimal('school_name_font_size', 6, 2)->default(16);
            $table->decimal('school_address_font_size', 6, 2)->default(8.5);
            $table->string('report_title_text')->default('Progress Report');
            $table->decimal('report_title_font_size', 6, 2)->default(17);
            $table->string('school_name_color', 20)->default('#5b8f42');
            $table->string('school_address_color', 20)->default('#5b8f42');
            $table->string('report_title_color', 20)->default('#d97706');
            $table->string('header_border_color', 20)->default('#111111');
            $table->string('card_border_color', 20)->default('#111111');
            $table->string('table_header_bg_color', 20)->default('#f3f4f6');
            $table->string('table_header_text_color', 20)->default('#111827');
            $table->string('table_border_color', 20)->default('#555555');
            $table->string('table_row_alt_bg_color', 20)->default('#f8fafc');
            $table->string('student_label_color', 20)->default('#111827');
            $table->string('student_value_color', 20)->default('#111827');
            $table->string('summary_bg_color', 20)->default('#111827');
            $table->string('summary_text_color', 20)->default('#ffffff');
            $table->string('remarks_title_color', 20)->default('#111827');
            $table->string('remarks_text_color', 20)->default('#374151');
            $table->string('signature_line_color', 20)->default('#111827');
            $table->string('remark_excellent_text')->default('Excellent');
            $table->string('remark_good_text')->default('Good');
            $table->string('remark_satisfactory_text')->default('Satisfactory');
            $table->string('remark_improve_text')->default('Need to be improved');
            $table->text('comments_excellent_text');
            $table->text('comments_good_text');
            $table->text('comments_default_text');
            $table->decimal('school_logo_max_width_mm', 6, 2)->default(16);
            $table->json('subject_column_widths')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_report_template_settings');
    }
};
