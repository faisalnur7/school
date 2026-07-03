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
            if (!Schema::hasColumn('admit_seat_card_settings', 'card_name_text_color')) {
                $table->string('card_name_text_color', 20)->default('#111827')->after('card_name_font_size');
            }
        });

        DB::table('admit_seat_card_settings')->update([
            'card_name_text_color' => '#111827',
        ]);
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            if (Schema::hasColumn('admit_seat_card_settings', 'card_name_text_color')) {
                $table->dropColumn('card_name_text_color');
            }
        });
    }
};
