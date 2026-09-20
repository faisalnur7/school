<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            $table->decimal('card_text_padding_value', 6, 2)->default(0)->after('card_student_detail_text_color');
            $table->decimal('card_text_margin_value', 6, 2)->default(0)->after('card_text_padding_value');
        });
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            $table->dropColumn(['card_text_padding_value', 'card_text_margin_value']);
        });
    }
};
