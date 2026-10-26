<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('admit_seat_card_settings')->updateOrInsert(
            ['card_type' => 5],
            [
                'card_type' => 5,
                'cards_per_page' => 4,
                'cards_per_row' => 2,
                'card_width_value' => 6.20,
                'card_height_value' => 8.80,
                'grid_gap_value' => 0.50,
                'card_dimension_unit' => 'cm',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('admit_seat_card_settings')->where('card_type', 5)->delete();
    }
};
