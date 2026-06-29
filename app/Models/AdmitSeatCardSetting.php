<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmitSeatCardSetting extends Model
{
    protected $fillable = [
        'card_type',
        'cards_per_page',
        'cards_per_row',
        'card_width_value',
        'card_height_value',
        'grid_gap_value',
        'card_dimension_unit',
        'card_color_type',
        'card_color_gradient_1',
        'card_color_gradient_2',
        'card_solid_color',
        'card_logo',
        'card_is_transparent',
        'card_school_name_text_color',
        'card_school_detail_text_color',
        'card_slogan_text_color',
        'card_back_notice_text_color',
        'card_footer_text_color',
        'card_title_text_color',
        'card_exam_type_text_color',
        'card_exam_name_text_color',
    ];

    public static function current(int $cardType = 1): self
    {
        $cardType = in_array($cardType, [1, 2, 3, 4], true) ? $cardType : 1;
        $setting = static::firstOrNew(['card_type' => $cardType]);

        return $setting->fill([
            'card_is_transparent' => $setting->card_is_transparent ?? false,
            'card_color_type' => $setting->card_color_type ?? 'gradient',
            'card_color_gradient_1' => $setting->card_color_gradient_1 ?? '#1e3a5f',
            'card_color_gradient_2' => $setting->card_color_gradient_2 ?? '#2563eb',
            'card_solid_color' => $setting->card_solid_color ?? '#1e3a5f',
            'card_school_name_text_color' => $setting->card_school_name_text_color ?? '#ffffff',
            'card_school_detail_text_color' => $setting->card_school_detail_text_color ?? '#e5e7eb',
            'card_slogan_text_color' => $setting->card_slogan_text_color ?? '#e5e7eb',
            'card_back_notice_text_color' => $setting->card_back_notice_text_color ?? '#94a3b8',
            'card_footer_text_color' => $setting->card_footer_text_color ?? '#e5e7eb',
            'card_title_text_color' => $setting->card_title_text_color ?? '#ffffff',
            'card_exam_type_text_color' => $setting->card_exam_type_text_color ?? '#ffffff',
            'card_exam_name_text_color' => $setting->card_exam_name_text_color ?? '#e5e7eb',
        ]);
    }
}
