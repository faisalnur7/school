<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = json_encode([
            'page' => ['width' => 210, 'height' => 297],
            'margins' => ['top' => 28, 'right' => 16, 'bottom' => 18, 'left' => 16],
            'typography' => [
                'title_size' => 18,
                'title_font_family' => 'Arial, Helvetica, sans-serif',
                'title_color' => '#111827',
                'title_background' => '#eef2e3',
                'title_border_color' => '#4b5563',
                'title_border_width' => 1,
                'title_border_style' => 'solid',
                'title_padding_top' => 9,
                'title_padding_right' => 42,
                'title_padding_bottom' => 9,
                'title_padding_left' => 42,
                'title_font_weight' => 500,
                'title_text_align' => 'center',
                'title_letter_spacing' => 0.01,
                'title_text_transform' => 'uppercase',
                'body_size' => 17,
                'line_height' => 2.05,
                'font_family' => 'Georgia, Times New Roman, serif',
                'font_color' => '#111827',
                'font_weight' => 400,
                'placeholder_font_family' => 'Georgia, Times New Roman, serif',
                'placeholder_font_size' => 17,
                'placeholder_font_weight' => 700,
                'placeholder_color' => '#b45309',
                'text_align' => 'justify',
            ],
            'watermark' => ['opacity' => 0.12, 'size' => 55],
            'visibility' => [
                'title' => true,
                'body' => true,
                'watermark' => true,
                'reason' => true,
                'principal' => true,
                'principal_name' => true,
                'school_name' => true,
                'contact_number' => true,
            ],
            'header' => [
                'logo' => ['width' => 48, 'height' => 48],
                'bangla' => ['font_size' => 29, 'font_weight' => 800, 'color' => '#111827', 'text_align' => 'left'],
                'english' => ['font_size' => 17, 'font_weight' => 800, 'color' => '#111827', 'text_align' => 'left'],
            ],
            'footer' => [
                'reason' => ['font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 15, 'font_weight' => 400, 'color' => '#111827', 'text_align' => 'left'],
                'principal' => ['font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 16, 'font_weight' => 400, 'color' => '#111827', 'text_align' => 'center'],
                'principal_label' => ['font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 16, 'font_weight' => 700, 'color' => '#111827', 'text_align' => 'center'],
                'school_name' => ['font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 16, 'font_weight' => 700, 'color' => '#111827', 'text_align' => 'center'],
                'contact_number' => ['font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 14, 'font_weight' => 400, 'color' => '#111827', 'text_align' => 'center'],
            ],
            'positions' => [
                'title' => ['x' => 0, 'y' => 0],
                'body' => ['x' => 0, 'y' => 0],
                'bottom' => ['x' => 0, 'y' => 0],
                'watermark' => ['x' => 0, 'y' => 0],
            ],
        ], JSON_THROW_ON_ERROR);

        DB::table('certificates')
            ->whereNull('layout_settings')
            ->update(['layout_settings' => $defaults]);
    }

    public function down(): void
    {
        // MySQL does not support defaults on JSON columns. The model supplies
        // the defaults for new records, so there is no schema change to undo.
    }
};
