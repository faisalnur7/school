<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admit_seat_card_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('card_type')->unique();
            $table->unsignedTinyInteger('cards_per_page')->default(8);
            $table->unsignedTinyInteger('cards_per_row')->default(2);
            $table->decimal('card_width_value', 8, 2)->default(9.40);
            $table->decimal('card_height_value', 8, 2)->default(6.60);
            $table->decimal('grid_gap_value', 8, 2)->default(0.85);
            $table->string('card_dimension_unit', 2)->default('cm');
            $table->timestamps();
        });

        DB::table('admit_seat_card_settings')->insert([
            'id' => 1,
            'card_type' => 1,
            'cards_per_page' => 8,
            'cards_per_row' => 2,
            'card_width_value' => 9.40,
            'card_height_value' => 6.60,
            'grid_gap_value' => 0.85,
            'card_dimension_unit' => 'cm',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('admit_seat_card_settings')->insert([
            'id' => 2,
            'card_type' => 2,
            'cards_per_page' => 8,
            'cards_per_row' => 2,
            'card_width_value' => 9.40,
            'card_height_value' => 6.60,
            'grid_gap_value' => 0.85,
            'card_dimension_unit' => 'cm',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('admit_seat_card_settings');
    }
};
