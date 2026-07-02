<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admit_seat_card_settings', 'card_logo_size_value')) {
                $table->decimal('card_logo_size_value', 8, 2)->default(0.80)->after('card_photo_height_value');
            }
        });

        DB::table('admit_seat_card_settings')->update([
            'card_logo_size_value' => 0.80,
        ]);
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            if (Schema::hasColumn('admit_seat_card_settings', 'card_logo_size_value')) {
                $table->dropColumn('card_logo_size_value');
            }
        });
    }
};
