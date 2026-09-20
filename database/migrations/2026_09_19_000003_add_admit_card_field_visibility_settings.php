<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            $table->boolean('card_show_student_name_label_front')->default(false)->after('card_show_mother_name_front');
            $table->boolean('card_show_roll_front')->default(true)->after('card_show_student_name_label_front');
            $table->boolean('card_show_class_front')->default(true)->after('card_show_roll_front');
            $table->boolean('card_show_section_front')->default(true)->after('card_show_class_front');
            $table->boolean('card_show_session_front')->default(true)->after('card_show_section_front');
            $table->boolean('card_show_vertical_label_front')->default(false)->after('card_show_session_front');
            $table->boolean('card_exam_name_badge_front')->default(false)->after('card_show_vertical_label_front');
        });
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            $table->dropColumn([
                'card_show_student_name_label_front',
                'card_show_roll_front',
                'card_show_class_front',
                'card_show_section_front',
                'card_show_session_front',
                'card_show_vertical_label_front',
                'card_exam_name_badge_front',
            ]);
        });
    }
};
