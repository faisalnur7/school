<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_subjects', function (Blueprint $table) {
            $table->id();

            // Student relationship
            $table->unsignedBigInteger('student_id')->comment('Student ID');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            // Subject relationship
            $table->unsignedBigInteger('subject_id')->comment('Subject ID');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');

            // Class and session info
            $table->unsignedBigInteger('school_class_id')->comment('Class ID');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');

            $table->unsignedBigInteger('academic_session_id')->comment('Academic Session ID');
            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->onDelete('cascade');

            // Whether student selected this as optional or it's mandatory
            $table->boolean('is_optional')->default(false)->comment('Is optional subject');
            $table->boolean('is_mandatory')->default(true)->comment('Is mandatory subject');

            // Timestamps
            $table->timestamps();

            // Unique constraint: prevent duplicate subject for same student
            $table->unique(['student_id', 'subject_id', 'academic_session_id'], 'student_subject_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_subjects');
    }
};