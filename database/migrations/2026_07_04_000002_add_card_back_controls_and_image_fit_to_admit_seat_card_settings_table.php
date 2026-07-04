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
            if (!Schema::hasColumn('admit_seat_card_settings', 'card_photo_fit')) {
                $table->string('card_photo_fit', 16)->default('cover')->after('card_photo_height_value');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_logo_fit')) {
                $table->string('card_logo_fit', 16)->default('contain')->after('card_photo_fit');
            }

            foreach ([
                'card_show_back_student_details' => ['default' => true, 'after' => 'card_show_back_notice'],
                'card_show_back_school_contact' => ['default' => true, 'after' => 'card_show_back_student_details'],
                'card_show_back_qr' => ['default' => true, 'after' => 'card_show_back_school_contact'],
                'card_show_back_signature' => ['default' => true, 'after' => 'card_show_back_qr'],
            ] as $column => $config) {
                if (!Schema::hasColumn('admit_seat_card_settings', $column)) {
                    $table->boolean($column)->default($config['default'])->after($config['after']);
                }
            }
        });

        DB::table('admit_seat_card_settings')->update([
            'card_photo_fit' => 'cover',
            'card_logo_fit' => 'contain',
            'card_show_back_student_details' => true,
            'card_show_back_school_contact' => true,
            'card_show_back_qr' => true,
            'card_show_back_signature' => true,
        ]);
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            foreach ([
                'card_show_back_signature',
                'card_show_back_qr',
                'card_show_back_school_contact',
                'card_show_back_student_details',
            ] as $column) {
                if (Schema::hasColumn('admit_seat_card_settings', $column)) {
                    $table->dropColumn($column);
                }
            }

            foreach ([
                'card_logo_fit',
                'card_photo_fit',
            ] as $column) {
                if (Schema::hasColumn('admit_seat_card_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
