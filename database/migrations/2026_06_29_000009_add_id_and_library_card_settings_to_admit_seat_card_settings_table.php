<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            'cards_per_page' => 4,
            'cards_per_row' => 2,
            'card_width_value' => 5.40,
            'card_height_value' => 8.40,
            'grid_gap_value' => 0.50,
            'card_dimension_unit' => 'cm',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('admit_seat_card_settings')->updateOrInsert(
            ['card_type' => 3],
            array_merge(['card_type' => 3], $defaults)
        );

        DB::table('admit_seat_card_settings')->updateOrInsert(
            ['card_type' => 4],
            array_merge(['card_type' => 4], $defaults)
        );
    }

    public function down(): void
    {
        DB::table('admit_seat_card_settings')->whereIn('card_type', [3, 4])->delete();
    }
};
