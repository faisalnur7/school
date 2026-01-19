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
        Schema::create('class_routines', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->unsignedBigInteger('school_class_id')->comment('Class ID');
            $table->unsignedBigInteger('section_id')->comment('Section ID');
            $table->unsignedBigInteger('subject_id')->comment('Subject ID');
            $table->unsignedBigInteger('teacher_id')->nullable()->comment('Teacher ID');
            $table->unsignedBigInteger('classroom_id')->nullable()->comment('Classroom ID');

            // Schedule details
            $table->string('day')->comment('Day of the week');
            $table->time('start_time')->comment('Start time of the class');
            $table->time('end_time')->comment('End time of the class');

            $table->timestamps();

            // Foreign keys
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
            $table->foreign('classroom_id')->references('id')->on('classrooms')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_routines');
    }
};
