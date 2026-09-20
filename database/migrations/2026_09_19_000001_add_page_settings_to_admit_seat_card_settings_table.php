<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            $table->decimal('page_width_mm', 8, 2)->default(210)->after('card_dimension_unit');
            $table->decimal('page_height_mm', 8, 2)->default(297)->after('page_width_mm');
            $table->decimal('page_margin_top_mm', 8, 2)->default(10)->after('page_height_mm');
            $table->decimal('page_margin_right_mm', 8, 2)->default(6.35)->after('page_margin_top_mm');
            $table->decimal('page_margin_bottom_mm', 8, 2)->default(4)->after('page_margin_right_mm');
            $table->decimal('page_margin_left_mm', 8, 2)->default(6.35)->after('page_margin_bottom_mm');
        });
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            $table->dropColumn([
                'page_width_mm',
                'page_height_mm',
                'page_margin_top_mm',
                'page_margin_right_mm',
                'page_margin_bottom_mm',
                'page_margin_left_mm',
            ]);
        });
    }
};
