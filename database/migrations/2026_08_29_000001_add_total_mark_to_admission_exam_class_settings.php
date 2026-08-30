<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('admission_exam_class_settings') && ! Schema::hasColumn('admission_exam_class_settings', 'total_mark')) {
            Schema::table('admission_exam_class_settings', function (Blueprint $table) {
                $table->decimal('total_mark', 8, 2)->default(100)->after('school_class_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('admission_exam_class_settings') && Schema::hasColumn('admission_exam_class_settings', 'total_mark')) {
            Schema::table('admission_exam_class_settings', fn (Blueprint $table) => $table->dropColumn('total_mark'));
        }
    }
};
