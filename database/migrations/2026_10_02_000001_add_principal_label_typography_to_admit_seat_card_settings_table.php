<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table): void {
            $table->decimal('card_principal_label_font_size', 6, 2)->default(5.2)->after('card_vertical_label_font_size');
            $table->string('card_principal_label_text_color', 20)->default('#3f3f46')->after('card_vertical_label_text_color');
        });
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table): void {
            $table->dropColumn(['card_principal_label_font_size', 'card_principal_label_text_color']);
        });
    }
};
