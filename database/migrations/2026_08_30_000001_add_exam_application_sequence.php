<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('admission_exams') && ! Schema::hasColumn('admission_exams', 'application_sequence')) {
            Schema::table('admission_exams', function (Blueprint $table) {
                $table->unsignedInteger('application_sequence')->default(0)->after('form_fee');
            });
        }

        if (Schema::hasTable('admission_applications')) {
            Schema::table('admission_applications', function (Blueprint $table) {
                $table->dropUnique('admission_applications_application_no_unique');
                $table->unique(['admission_exam_id', 'academic_session_id', 'application_number'], 'admission_application_exam_session_number_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('admission_applications')) {
            Schema::table('admission_applications', function (Blueprint $table) {
                $table->dropUnique('admission_application_exam_session_number_unique');
                $table->unique('application_no');
            });
        }

        if (Schema::hasTable('admission_exams') && Schema::hasColumn('admission_exams', 'application_sequence')) {
            Schema::table('admission_exams', fn (Blueprint $table) => $table->dropColumn('application_sequence'));
        }
    }
};
