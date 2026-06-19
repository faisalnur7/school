<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_academic_information', function (Blueprint $table) {
            try {
                // $table->dropUnique(['student_id', 'academic_session_id']);
            } catch (\Throwable $e) {
                // Constraint may not exist in some environments.
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_academic_information', function (Blueprint $table) {
            $table->unique(['student_id', 'academic_session_id']);
        });
    }
};
