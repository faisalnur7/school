<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admit_seat_card_settings', 'card_student_field_order')) {
                $table->json('card_student_field_order')->nullable()->after('card_typography_spacing');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            if (Schema::hasColumn('admit_seat_card_settings', 'card_student_field_order')) {
                $table->dropColumn('card_student_field_order');
            }
        });
    }
};
