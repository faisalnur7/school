<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('admit_seat_card_settings', 'card_element_sizes')) {
            Schema::table('admit_seat_card_settings', function (Blueprint $table): void {
                $table->json('card_element_sizes')->nullable()->after('card_element_positions');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('admit_seat_card_settings', 'card_element_sizes')) {
            Schema::table('admit_seat_card_settings', function (Blueprint $table): void {
                $table->dropColumn('card_element_sizes');
            });
        }
    }
};
