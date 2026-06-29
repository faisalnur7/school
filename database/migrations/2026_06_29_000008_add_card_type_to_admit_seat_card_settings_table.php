<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('admit_seat_card_settings', 'card_type')) {
            Schema::table('admit_seat_card_settings', function (Blueprint $table) {
                $table->unsignedTinyInteger('card_type')->default(1)->after('id');
                $table->unique('card_type');
            });
        }

        $defaults = [
            'cards_per_page' => 8,
            'cards_per_row' => 2,
            'card_width_value' => 9.40,
            'card_height_value' => 6.60,
            'grid_gap_value' => 0.85,
            'card_dimension_unit' => 'cm',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('admit_seat_card_settings')->updateOrInsert(
            ['card_type' => 1],
            array_merge(['card_type' => 1], $defaults)
        );

        DB::table('admit_seat_card_settings')->updateOrInsert(
            ['card_type' => 2],
            array_merge(['card_type' => 2], $defaults)
        );
    }

    public function down(): void
    {
        if (Schema::hasColumn('admit_seat_card_settings', 'card_type')) {
            Schema::table('admit_seat_card_settings', function (Blueprint $table) {
                $table->dropUnique('admit_seat_card_settings_card_type_unique');
                $table->dropColumn('card_type');
            });
        }
    }
};
