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
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_id')->comment('Student who is attending');
            $table->unsignedBigInteger('school_class_id')->nullable()->comment('Class of the student');
            $table->unsignedBigInteger('section_id')->nullable()->comment('Section of the student');
            
            $table->date('attendance_date')->comment('Date of attendance');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present')->comment('Attendance status');
            $table->text('remarks')->nullable()->comment('Optional remarks');

            $table->timestamps();

            // Foreign keys
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('set null');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('set null');

            $table->unique(['student_id', 'attendance_date']); // Prevent duplicate entries for same student on same date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};
