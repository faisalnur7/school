<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_routines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->time('morning_start_time');
            $table->time('morning_end_time');
            $table->time('noon_start_time');
            $table->time('noon_end_time');
            $table->timestamps();

            $table->unique(['exam_id', 'school_class_id', 'group_id'], 'exam_routines_exam_class_group_unique');
            $table->index(['academic_session_id', 'school_class_id', 'group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_routines');
    }
};
