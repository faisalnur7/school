<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->enum('exam_category', ['tutorial', 'terminal'])->default('tutorial')->after('type');
            $table->tinyInteger('pair_no')->nullable()->after('exam_category');
            $table->tinyInteger('pair_weight_percent')->default(20)->after('pair_no');
        });

        DB::table('exams')
            ->where('type', 'term')
            ->update(['exam_category' => 'terminal']);
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['exam_category', 'pair_no', 'pair_weight_percent']);
        });
    }
};
