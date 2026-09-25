<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table): void {
            $table->decimal('card_vertical_label_font_size', 6, 2)->default(5.2)->after('card_title_font_size');
            $table->string('card_vertical_label_text_color', 20)->default('#16a085')->after('card_title_text_color');
        });
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table): void {
            $table->dropColumn(['card_vertical_label_font_size', 'card_vertical_label_text_color']);
        });
    }
};
