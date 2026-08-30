<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('admission_application_drafts')) return;

        Schema::create('admission_application_drafts', function (Blueprint $table) {
            $table->id();
            $table->string('token_hash', 64)->unique();
            $table->foreignId('admission_exam_id')->constrained('admission_exams')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->json('applicant_data');
            $table->string('image_path')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            $table->index(['expires_at', 'confirmed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_application_drafts');
    }
};
