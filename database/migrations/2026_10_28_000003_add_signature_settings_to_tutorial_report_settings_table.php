<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tutorial_report_settings', function (Blueprint $table) {
            $table->json('signature_settings')->nullable()->after('element_font_sizes');
        });
    }

    public function down(): void
    {
        Schema::table('tutorial_report_settings', function (Blueprint $table) {
            $table->dropColumn('signature_settings');
        });
    }
};
