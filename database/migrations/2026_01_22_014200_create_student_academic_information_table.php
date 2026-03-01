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
        Schema::create('student_academic_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('students');
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions');
            $table->foreignId('school_class_id')->nullable()->constrained('school_classes');
            $table->foreignId('section_id')->nullable()->constrained('sections');
            $table->foreignId('group_id')->nullable()->constrained('groups');
            $table->string('roll')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_academic_information');
    }
};
