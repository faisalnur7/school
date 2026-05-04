<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('class_id')->constrained('sections')->nullOnDelete();
            $table->foreignId('academic_session_id')->nullable()->after('section_id')->constrained('academic_sessions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropForeign(['academic_session_id']);
            $table->dropColumn(['section_id', 'academic_session_id']);
        });
    }
};
