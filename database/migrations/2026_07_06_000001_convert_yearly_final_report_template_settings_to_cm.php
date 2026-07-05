<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('yearly_final_report_template_settings')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                $needsConversion = max(
                    (float) $row->margin_top_mm,
                    (float) $row->margin_right_mm,
                    (float) $row->margin_bottom_mm,
                    (float) $row->margin_left_mm
                ) > 5;

                if (! $needsConversion) {
                    continue;
                }

                DB::table('yearly_final_report_template_settings')
                    ->where('id', $row->id)
                    ->update([
                        'margin_top_mm' => ((float) $row->margin_top_mm) / 10,
                        'margin_right_mm' => ((float) $row->margin_right_mm) / 10,
                        'margin_bottom_mm' => ((float) $row->margin_bottom_mm) / 10,
                        'margin_left_mm' => ((float) $row->margin_left_mm) / 10,
                    ]);
            }
        });
    }

    public function down(): void
    {
        DB::table('yearly_final_report_template_settings')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                DB::table('yearly_final_report_template_settings')
                    ->where('id', $row->id)
                    ->update([
                        'margin_top_mm' => ((float) $row->margin_top_mm) * 10,
                        'margin_right_mm' => ((float) $row->margin_right_mm) * 10,
                        'margin_bottom_mm' => ((float) $row->margin_bottom_mm) * 10,
                        'margin_left_mm' => ((float) $row->margin_left_mm) * 10,
                    ]);
            }
        });
    }
};
