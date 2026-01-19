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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();

            $table->string('title_bn')->comment('Assignment title in Bengali');
            $table->string('title_en')->comment('Assignment title in English');
            $table->text('description')->nullable()->comment('Assignment description');
            $table->date('due_date')->nullable()->comment('Assignment due date');

            $table->unsignedBigInteger('course_id')->comment('Associated course');
            $table->unsignedBigInteger('teacher_id')->nullable()->comment('Teacher who created the assignment');

            $table->string('attachment')->nullable()->comment('Optional file attachment');

            $table->enum('status', ['draft', 'published', 'closed'])->default('draft')->comment('Assignment status');

            $table->timestamps();

            // Foreign keys
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
