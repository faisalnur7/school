<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * "Front School Detail" used to be a switch that did nothing on ID / library / employee cards.
     * It now shows the school address under the school name, so existing rows (which all hold the
     * old, ineffective "true") are turned off to keep printed cards looking exactly as before.
     */
    public function up(): void
    {
        DB::table('admit_seat_card_settings')
            ->whereIn('card_type', [3, 4, 5])
            ->update(['card_show_school_detail_front' => false]);
    }

    public function down(): void
    {
        DB::table('admit_seat_card_settings')
            ->whereIn('card_type', [3, 4, 5])
            ->update(['card_show_school_detail_front' => true]);
    }
};
