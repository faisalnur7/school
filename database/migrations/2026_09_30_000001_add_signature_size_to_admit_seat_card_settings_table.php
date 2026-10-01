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
            if (!Schema::hasColumn('admit_seat_card_settings', 'card_signature_size_value')) {
                $table->decimal('card_signature_size_value', 8, 2)->default(1.40)->after('card_logo_size_value');
            }
        });

        DB::table('admit_seat_card_settings')
            ->whereNull('card_signature_size_value')
            ->update(['card_signature_size_value' => 1.40]);
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            if (Schema::hasColumn('admit_seat_card_settings', 'card_signature_size_value')) {
                $table->dropColumn('card_signature_size_value');
            }
        });
    }
};
