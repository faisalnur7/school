<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('progress_report_template_settings')->get();

        foreach ($rows as $row) {
            DB::table('progress_report_template_settings')
                ->where('id', $row->id)
                ->update([
                    'margin_top_mm' => ((float) $row->margin_top_mm) / 10,
                    'margin_right_mm' => ((float) $row->margin_right_mm) / 10,
                    'margin_bottom_mm' => ((float) $row->margin_bottom_mm) / 10,
                    'margin_left_mm' => ((float) $row->margin_left_mm) / 10,
                    'school_logo_max_width_mm' => ((float) $row->school_logo_max_width_mm) / 10,
                ]);
        }
    }

    public function down(): void
    {
        $rows = DB::table('progress_report_template_settings')->get();

        foreach ($rows as $row) {
            DB::table('progress_report_template_settings')
                ->where('id', $row->id)
                ->update([
                    'margin_top_mm' => ((float) $row->margin_top_mm) * 10,
                    'margin_right_mm' => ((float) $row->margin_right_mm) * 10,
                    'margin_bottom_mm' => ((float) $row->margin_bottom_mm) * 10,
                    'margin_left_mm' => ((float) $row->margin_left_mm) * 10,
                    'school_logo_max_width_mm' => ((float) $row->school_logo_max_width_mm) * 10,
                ]);
        }
    }
};
