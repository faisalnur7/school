<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_sets', function (Blueprint $table) {
            $table->unsignedTinyInteger('month')->nullable()->after('frequency');
        });
    }

    public function down(): void
    {
        Schema::table('fee_sets', function (Blueprint $table) {
            $table->dropColumn('month');
        });
    }
};