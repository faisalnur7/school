<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            $table->boolean('card_show_father_name_front')->default(false)->after('card_show_photo_front');
            $table->boolean('card_show_mother_name_front')->default(false)->after('card_show_father_name_front');
        });
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            $table->dropColumn([
                'card_show_father_name_front',
                'card_show_mother_name_front',
            ]);
        });
    }
};
