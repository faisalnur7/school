<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorialReportSetting extends Model
{
    protected $fillable = [
        'school_class_id',
        'results_per_page', 'page_width_mm', 'page_height_mm',
        'page_margin_top_mm', 'page_margin_right_mm', 'page_margin_bottom_mm',
        'page_margin_left_mm', 'card_gap_mm', 'font_scale_percent',
        'content_width_percent', 'table_padding_mm', 'card_border_width_mm',
        'card_border_color', 'card_radius_mm', 'element_order',
        'element_positions', 'element_sizes', 'element_visibility',
        'element_font_sizes', 'signature_settings', 'marks_column_widths',
    ];

    protected $casts = [
        'school_class_id' => 'integer',
        'results_per_page' => 'integer',
        'font_scale_percent' => 'integer',
        'content_width_percent' => 'integer',
        'element_order' => 'array',
        'element_positions' => 'array',
        'element_sizes' => 'array',
        'element_visibility' => 'array',
        'element_font_sizes' => 'array',
        'signature_settings' => 'array',
        'marks_column_widths' => 'array',
    ];

    public const ELEMENTS = ['header', 'student', 'marks', 'footer'];

    public static function defaults(): array
    {
        return [
            'results_per_page' => 2,
            'page_width_mm' => 210,
            'page_height_mm' => 297,
            'page_margin_top_mm' => 10,
            'page_margin_right_mm' => 12,
            'page_margin_bottom_mm' => 10,
            'page_margin_left_mm' => 12,
            'card_gap_mm' => 2,
            'font_scale_percent' => 100,
            'content_width_percent' => 96,
            'table_padding_mm' => 1.2,
            'card_border_width_mm' => 0.3,
            'card_border_color' => '#cbd6d2',
            'card_radius_mm' => 2,
            'element_order' => self::ELEMENTS,
            'element_positions' => [
                'header' => ['x' => 0, 'y' => 0],
                'student' => ['x' => 0, 'y' => 0],
                'marks' => ['x' => 0, 'y' => 0],
                'footer' => ['x' => 0, 'y' => 0],
            ],
            'element_sizes' => [
                'header' => ['width' => 100, 'height' => 12],
                'student' => ['width' => 96, 'height' => 10],
                'marks' => ['width' => 96, 'height' => 65],
                'footer' => ['width' => 96, 'height' => 15],
            ],
            'element_visibility' => [
                'header' => true, 'student' => true, 'marks' => true, 'footer' => true,
            ],
            'element_font_sizes' => [
                'header' => 14, 'student' => 12, 'marks' => 12, 'footer' => 12,
            ],
            'signature_settings' => [
                'visible' => true, 'width_mm' => 55, 'height_mm' => 18,
                'space_mm' => 12, 'offset_x_percent' => 0, 'offset_y_percent' => 0,
                'show_name' => true, 'show_designation' => true, 'show_school_name' => true,
            ],
            'marks_column_widths' => [
                'subject' => 25, 'full_marks' => 15,
                'obtained_marks' => 35, 'highest_marks' => 25,
            ],
        ];
    }

    public static function current(?int $classId = null): self
    {
        $setting = static::query()
            ->when($classId !== null, fn ($query) => $query->where('school_class_id', $classId))
            ->when($classId === null, fn ($query) => $query->whereNull('school_class_id'))
            ->first();

        if (! $setting && $classId !== null) {
            $global = static::query()->whereNull('school_class_id')->first();
            $setting = $global ? new static($global->toArray()) : new static();
            $setting->exists = false;
            $setting->id = null;
            $setting->school_class_id = $classId;
        }

        $setting ??= new static(['school_class_id' => $classId]);
        $defaults = self::defaults();

        foreach ($defaults as $key => $value) {
            if ($setting->{$key} === null) {
                $setting->{$key} = $value;
            }
        }

        return $setting;
    }
}
