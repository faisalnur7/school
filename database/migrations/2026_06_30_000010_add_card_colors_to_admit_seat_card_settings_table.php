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
            if (!Schema::hasColumn('admit_seat_card_settings', 'card_color_type')) {
                $table->string('card_color_type', 20)->default('gradient')->after('card_dimension_unit');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_color_gradient_1')) {
                $table->string('card_color_gradient_1', 20)->default('#1e3a5f')->after('card_color_type');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_color_gradient_2')) {
                $table->string('card_color_gradient_2', 20)->default('#2563eb')->after('card_color_gradient_1');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_solid_color')) {
                $table->string('card_solid_color', 20)->default('#1e3a5f')->after('card_color_gradient_2');
            }
        });

        DB::table('admit_seat_card_settings')->update([
            'card_color_type' => 'gradient',
            'card_color_gradient_1' => '#1e3a5f',
            'card_color_gradient_2' => '#2563eb',
            'card_solid_color' => '#1e3a5f',
        ]);
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            if (Schema::hasColumn('admit_seat_card_settings', 'card_solid_color')) {
                $table->dropColumn('card_solid_color');
            }

            if (Schema::hasColumn('admit_seat_card_settings', 'card_color_gradient_2')) {
                $table->dropColumn('card_color_gradient_2');
            }

            if (Schema::hasColumn('admit_seat_card_settings', 'card_color_gradient_1')) {
                $table->dropColumn('card_color_gradient_1');
            }

            if (Schema::hasColumn('admit_seat_card_settings', 'card_color_type')) {
                $table->dropColumn('card_color_type');
            }
        });
    }
};
