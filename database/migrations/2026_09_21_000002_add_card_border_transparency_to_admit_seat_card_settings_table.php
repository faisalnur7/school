<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('admit_seat_card_settings', 'card_border_transparent')) {
            Schema::table('admit_seat_card_settings', function (Blueprint $table): void {
                $table->json('card_border_transparent')->nullable()->after('card_border_colors');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('admit_seat_card_settings', 'card_border_transparent')) {
            Schema::table('admit_seat_card_settings', function (Blueprint $table): void {
                $table->dropColumn('card_border_transparent');
            });
        }
    }
};
