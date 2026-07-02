<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admit_seat_card_settings', 'card_student_detail_alignment')) {
                $table->string('card_student_detail_alignment', 20)->default('left')->after('card_name_font_size');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_student_detail_font_size')) {
                $table->decimal('card_student_detail_font_size', 8, 2)->default(8.50)->after('card_student_detail_alignment');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_student_detail_text_color')) {
                $table->string('card_student_detail_text_color', 20)->default('#111827')->after('card_student_detail_font_size');
            }
        });

        DB::table('admit_seat_card_settings')->update([
            'card_student_detail_alignment' => 'left',
            'card_student_detail_font_size' => 8.50,
            'card_student_detail_text_color' => '#111827',
        ]);
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            if (Schema::hasColumn('admit_seat_card_settings', 'card_student_detail_text_color')) {
                $table->dropColumn('card_student_detail_text_color');
            }

            if (Schema::hasColumn('admit_seat_card_settings', 'card_student_detail_font_size')) {
                $table->dropColumn('card_student_detail_font_size');
            }

            if (Schema::hasColumn('admit_seat_card_settings', 'card_student_detail_alignment')) {
                $table->dropColumn('card_student_detail_alignment');
            }
        });
    }
};
