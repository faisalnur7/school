<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('progress_report_template_settings')
            ->where('id', 1)
            ->update(['margin_top_mm' => 0.9]);
    }

    public function down(): void
    {
        DB::table('progress_report_template_settings')
            ->where('id', 1)
            ->update(['margin_top_mm' => 0.8]);
    }
};
