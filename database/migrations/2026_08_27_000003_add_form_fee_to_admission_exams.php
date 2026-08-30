<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('admission_exams', 'form_fee')) {
            Schema::table('admission_exams', function (Blueprint $table) {
                $table->decimal('form_fee', 10, 2)->default(0)->after('exam_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('admission_exams', 'form_fee')) {
            Schema::table('admission_exams', function (Blueprint $table) {
                $table->dropColumn('form_fee');
            });
        }
    }
};
