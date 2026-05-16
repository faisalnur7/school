<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_academic_information', function (Blueprint $table) {
            if (!Schema::hasColumn('student_academic_information', 'academic_status')) {
                $table->string('academic_status')->default('active');
            }
            if (!Schema::hasColumn('student_academic_information', 'promotion_status')) {
                $table->string('promotion_status')->default('new_admission');
            }
            if (!Schema::hasColumn('student_academic_information', 'is_current')) {
                $table->boolean('is_current')->default(true);
            }
            if (!Schema::hasColumn('student_academic_information', 'previous_academic_information_id')) {
                $table->unsignedBigInteger('previous_academic_information_id')->nullable();
            }
            if (!Schema::hasColumn('student_academic_information', 'checkout_date')) {
                $table->date('checkout_date')->nullable();
            }
            if (!Schema::hasColumn('student_academic_information', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        // Add FK separately (may already exist from partial run)
        try {
            Schema::table('student_academic_information', function (Blueprint $table) {
                $table->foreign('previous_academic_information_id', 'sai_prev_id_foreign')
                      ->references('id')->on('student_academic_information')->nullOnDelete();
            });
        } catch (\Exception $e) { /* already exists */ }

        // Add unique constraint separately
        try {
            Schema::table('student_academic_information', function (Blueprint $table) {
                $table->unique(['student_id', 'academic_session_id']);
            });
        } catch (\Exception $e) { /* already exists */ }
    }

    public function down(): void
    {
        Schema::table('student_academic_information', function (Blueprint $table) {
            $table->dropForeign(['previous_academic_information_id']);
            $table->dropUnique(['student_id', 'academic_session_id']);
            $table->dropColumn(['academic_status', 'promotion_status', 'is_current', 'previous_academic_information_id', 'checkout_date', 'notes']);
        });
    }
};
