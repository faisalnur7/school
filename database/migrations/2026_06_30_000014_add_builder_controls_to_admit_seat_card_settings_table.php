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
            if (!Schema::hasColumn('admit_seat_card_settings', 'card_front_alignment')) {
                $table->string('card_front_alignment', 20)->default('center')->after('card_dimension_unit');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_back_alignment')) {
                $table->string('card_back_alignment', 20)->default('center')->after('card_front_alignment');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_front_padding_value')) {
                $table->decimal('card_front_padding_value', 8, 2)->default(0.80)->after('card_back_alignment');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_back_padding_value')) {
                $table->decimal('card_back_padding_value', 8, 2)->default(0.80)->after('card_front_padding_value');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_photo_width_value')) {
                $table->decimal('card_photo_width_value', 8, 2)->default(1.80)->after('card_back_padding_value');
            }

            if (!Schema::hasColumn('admit_seat_card_settings', 'card_photo_height_value')) {
                $table->decimal('card_photo_height_value', 8, 2)->default(2.70)->after('card_photo_width_value');
            }

            foreach ([
                'card_school_name_font_size' => ['default' => 7.2, 'after' => 'card_photo_height_value'],
                'card_school_detail_font_size' => ['default' => 5.4, 'after' => 'card_school_name_font_size'],
                'card_slogan_font_size' => ['default' => 4.8, 'after' => 'card_school_detail_font_size'],
                'card_title_font_size' => ['default' => 4.7, 'after' => 'card_slogan_font_size'],
                'card_name_font_size' => ['default' => 7.2, 'after' => 'card_title_font_size'],
                'card_exam_type_font_size' => ['default' => 7.4, 'after' => 'card_name_font_size'],
                'card_exam_name_font_size' => ['default' => 6.8, 'after' => 'card_exam_type_font_size'],
            ] as $column => $config) {
                if (!Schema::hasColumn('admit_seat_card_settings', $column)) {
                    $table->decimal($column, 8, 2)->default($config['default'])->after($config['after']);
                }
            }

            foreach ([
                'card_show_logo_front' => ['default' => true, 'after' => 'card_exam_name_font_size'],
                'card_show_logo_back' => ['default' => true, 'after' => 'card_show_logo_front'],
                'card_show_photo_front' => ['default' => true, 'after' => 'card_show_logo_back'],
                'card_show_footer_front' => ['default' => true, 'after' => 'card_show_photo_front'],
                'card_show_footer_back' => ['default' => true, 'after' => 'card_show_footer_front'],
                'card_show_school_detail_front' => ['default' => true, 'after' => 'card_show_footer_back'],
                'card_show_school_detail_back' => ['default' => true, 'after' => 'card_show_school_detail_front'],
                'card_show_slogan_front' => ['default' => true, 'after' => 'card_show_school_detail_back'],
                'card_show_slogan_back' => ['default' => true, 'after' => 'card_show_slogan_front'],
                'card_show_title_front' => ['default' => true, 'after' => 'card_show_slogan_back'],
                'card_show_title_back' => ['default' => true, 'after' => 'card_show_title_front'],
                'card_show_exam_type_front' => ['default' => true, 'after' => 'card_show_title_back'],
                'card_show_exam_name_front' => ['default' => true, 'after' => 'card_show_exam_type_front'],
                'card_show_back_notice' => ['default' => true, 'after' => 'card_show_exam_name_front'],
            ] as $column => $config) {
                if (!Schema::hasColumn('admit_seat_card_settings', $column)) {
                    $table->boolean($column)->default($config['default'])->after($config['after']);
                }
            }
        });

        DB::table('admit_seat_card_settings')->update([
            'card_front_alignment' => 'center',
            'card_back_alignment' => 'center',
            'card_front_padding_value' => 0.80,
            'card_back_padding_value' => 0.80,
            'card_photo_width_value' => 1.80,
            'card_photo_height_value' => 2.70,
            'card_school_name_font_size' => 7.20,
            'card_school_detail_font_size' => 5.40,
            'card_slogan_font_size' => 4.80,
            'card_title_font_size' => 4.70,
            'card_name_font_size' => 7.20,
            'card_exam_type_font_size' => 7.40,
            'card_exam_name_font_size' => 6.80,
            'card_show_logo_front' => true,
            'card_show_logo_back' => true,
            'card_show_photo_front' => true,
            'card_show_footer_front' => true,
            'card_show_footer_back' => true,
            'card_show_school_detail_front' => true,
            'card_show_school_detail_back' => true,
            'card_show_slogan_front' => true,
            'card_show_slogan_back' => true,
            'card_show_title_front' => true,
            'card_show_title_back' => true,
            'card_show_exam_type_front' => true,
            'card_show_exam_name_front' => true,
            'card_show_back_notice' => true,
        ]);
    }

    public function down(): void
    {
        Schema::table('admit_seat_card_settings', function (Blueprint $table) {
            foreach ([
                'card_show_back_notice',
                'card_show_exam_name_front',
                'card_show_exam_type_front',
                'card_show_title_back',
                'card_show_title_front',
                'card_show_slogan_back',
                'card_show_slogan_front',
                'card_show_school_detail_back',
                'card_show_school_detail_front',
                'card_show_footer_back',
                'card_show_footer_front',
                'card_show_photo_front',
                'card_show_logo_back',
                'card_show_logo_front',
            ] as $column) {
                if (Schema::hasColumn('admit_seat_card_settings', $column)) {
                    $table->dropColumn($column);
                }
            }

            foreach ([
                'card_exam_name_font_size',
                'card_exam_type_font_size',
                'card_name_font_size',
                'card_title_font_size',
                'card_slogan_font_size',
                'card_school_detail_font_size',
                'card_school_name_font_size',
                'card_photo_height_value',
                'card_photo_width_value',
                'card_back_padding_value',
                'card_front_padding_value',
                'card_back_alignment',
                'card_front_alignment',
            ] as $column) {
                if (Schema::hasColumn('admit_seat_card_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
