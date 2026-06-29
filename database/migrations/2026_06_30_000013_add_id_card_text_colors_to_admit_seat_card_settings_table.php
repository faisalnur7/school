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
            if (!Schema::hasColumn('admit_seat_card_settings', 'card_slogan_text_color')) {
                $table->string('card_slogan_text_color', 20)->default('#e5e7eb')->after('card_school_detail_text_color');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_back_notice_text_color')) {
                $table->string('card_back_notice_text_color', 20)->default('#94a3b8')->after('card_slogan_text_color');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_footer_text_color')) {
                $table->string('card_footer_text_color', 20)->default('#e5e7eb')->after('card_back_notice_text_color');
            }
        });

        DB::table('admit_seat_card_settings')->update([
            'card_slogan_text_color' => '#e5e7eb',
            'card_back_notice_text_color' => '#94a3b8',
            'card_footer_text_color' => '#e5e7eb',
        ]);
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            if (Schema::hasColumn('admit_seat_card_settings', 'card_footer_text_color')) {
                $table->dropColumn('card_footer_text_color');
            }

            if (Schema::hasColumn('admit_seat_card_settings', 'card_back_notice_text_color')) {
                $table->dropColumn('card_back_notice_text_color');
            }

            if (Schema::hasColumn('admit_seat_card_settings', 'card_slogan_text_color')) {
                $table->dropColumn('card_slogan_text_color');
            }
        });
    }
};
