<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admit_seat_card_settings', 'card_front_header_padding_value')) {
                $table->decimal('card_front_header_padding_value', 8, 2)->default(0.30)->after('card_back_padding_value');
            }
            if (!Schema::hasColumn('admit_seat_card_settings', 'card_back_header_padding_value')) {
                $table->decimal('card_back_header_padding_value', 8, 2)->default(0.30)->after('card_front_header_padding_value');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            foreach (['card_front_header_padding_value', 'card_back_header_padding_value'] as $column) {
                if (Schema::hasColumn('admit_seat_card_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
