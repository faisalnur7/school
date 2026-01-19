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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('title_bn')->comment('Course title in Bengali');
            $table->string('title_en')->comment('Course title in English');
            $table->text('description')->nullable()->comment('Course description');
            
            $table->unsignedBigInteger('teacher_id')->nullable()->comment('Teacher assigned to the course');
            $table->unsignedBigInteger('school_class_id')->nullable()->comment('Optional class assigned');
            $table->unsignedBigInteger('section_id')->nullable()->comment('Optional section assigned');

            $table->enum('status', ['draft', 'active', 'archived'])->default('draft')->comment('Course status');

            $table->timestamps();

            // Foreign keys
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('set null');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
