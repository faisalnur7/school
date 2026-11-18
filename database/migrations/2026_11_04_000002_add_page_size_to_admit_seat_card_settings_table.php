<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admit_seat_card_settings', 'card_page_size')) {
                $table->string('card_page_size', 10)->default('a4');
            }
            if (!Schema::hasColumn('admit_seat_card_settings', 'card_page_orientation')) {
                $table->string('card_page_orientation', 10)->default('landscape');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            foreach (['card_page_orientation', 'card_page_size'] as $column) {
                if (Schema::hasColumn('admit_seat_card_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
