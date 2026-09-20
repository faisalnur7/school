<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admit_seat_card_settings', 'card_footer_font_size')) {
                $table->decimal('card_footer_font_size', 8, 2)
                    ->default(4.5)
                    ->after('card_exam_name_font_size');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            if (Schema::hasColumn('admit_seat_card_settings', 'card_footer_font_size')) {
                $table->dropColumn('card_footer_font_size');
            }
        });
    }
};
