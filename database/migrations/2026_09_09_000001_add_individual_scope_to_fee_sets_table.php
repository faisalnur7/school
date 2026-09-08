<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_sets', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->after('academic_session_id')
                ->constrained('students')->cascadeOnDelete();
            $table->enum('scope', ['global', 'individual'])->default('global')->after('student_id');
            $table->index(['scope', 'student_id', 'academic_session_id'], 'fee_sets_scope_student_session_index');
        });
    }

    public function down(): void
    {
        Schema::table('fee_sets', function (Blueprint $table) {
            $table->dropIndex('fee_sets_scope_student_session_index');
            $table->dropForeign(['student_id']);
            $table->dropColumn(['student_id', 'scope']);
        });
    }
};
