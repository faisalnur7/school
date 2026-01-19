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
        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_id')->comment('Student whose progress is tracked');
            $table->unsignedBigInteger('school_class_id')->nullable()->comment('Class of the student');
            $table->unsignedBigInteger('section_id')->nullable()->comment('Section of the student');
            $table->unsignedBigInteger('subject_id')->nullable()->comment('Subject associated with the progress');

            $table->string('term')->nullable()->comment('Term or semester');
            $table->decimal('marks_obtained', 5, 2)->nullable()->comment('Marks obtained in the subject');
            $table->decimal('total_marks', 5, 2)->nullable()->comment('Total marks possible');
            $table->string('grade')->nullable()->comment('Grade achieved');
            $table->text('remarks')->nullable()->comment('Teacher remarks');

            $table->timestamps();

            // Foreign keys
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('set null');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('set null');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');

            $table->unique(['student_id', 'subject_id', 'term']); // Ensure one record per student/subject/term
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_progress');
    }
};
