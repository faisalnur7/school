<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Student-detail font size was added with the admit-card default (8.5pt) for every
     * card type but was never applied to ID cards. Now that ID / library / employee
     * cards honour it, rows still on the untouched admit default are reset to the
     * ID-card scale (4.6pt) so existing printed layouts do not suddenly blow up.
     */
    public function up(): void
    {
        DB::table('admit_seat_card_settings')
            ->whereIn('card_type', [3, 4, 5])
            ->where('card_student_detail_font_size', 8.5)
            ->update(['card_student_detail_font_size' => 4.6]);
    }

    public function down(): void
    {
        DB::table('admit_seat_card_settings')
            ->whereIn('card_type', [3, 4, 5])
            ->where('card_student_detail_font_size', 4.6)
            ->update(['card_student_detail_font_size' => 8.5]);
    }
};
