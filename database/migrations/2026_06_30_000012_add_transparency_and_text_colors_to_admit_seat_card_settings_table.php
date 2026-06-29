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
            if (!Schema::hasColumn('admit_seat_card_settings', 'card_is_transparent')) {
                $table->boolean('card_is_transparent')->default(false)->after('card_logo');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_school_name_text_color')) {
                $table->string('card_school_name_text_color', 20)->default('#ffffff')->after('card_is_transparent');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_school_detail_text_color')) {
                $table->string('card_school_detail_text_color', 20)->default('#e5e7eb')->after('card_school_name_text_color');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_title_text_color')) {
                $table->string('card_title_text_color', 20)->default('#ffffff')->after('card_school_detail_text_color');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_exam_type_text_color')) {
                $table->string('card_exam_type_text_color', 20)->default('#ffffff')->after('card_title_text_color');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_exam_name_text_color')) {
                $table->string('card_exam_name_text_color', 20)->default('#e5e7eb')->after('card_exam_type_text_color');
            }
        });

        DB::table('admit_seat_card_settings')->update([
            'card_is_transparent' => false,
            'card_school_name_text_color' => '#ffffff',
            'card_school_detail_text_color' => '#e5e7eb',
            'card_title_text_color' => '#ffffff',
            'card_exam_type_text_color' => '#ffffff',
            'card_exam_name_text_color' => '#e5e7eb',
        ]);
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            if (Schema::hasColumn('admit_seat_card_settings', 'card_exam_name_text_color')) {
                $table->dropColumn('card_exam_name_text_color');
            }

            if (Schema::hasColumn('admit_seat_card_settings', 'card_exam_type_text_color')) {
                $table->dropColumn('card_exam_type_text_color');
            }

            if (Schema::hasColumn('admit_seat_card_settings', 'card_title_text_color')) {
                $table->dropColumn('card_title_text_color');
            }

            if (Schema::hasColumn('admit_seat_card_settings', 'card_school_detail_text_color')) {
                $table->dropColumn('card_school_detail_text_color');
            }

            if (Schema::hasColumn('admit_seat_card_settings', 'card_school_name_text_color')) {
                $table->dropColumn('card_school_name_text_color');
            }

            if (Schema::hasColumn('admit_seat_card_settings', 'card_is_transparent')) {
                $table->dropColumn('card_is_transparent');
            }
        });
    }
};
