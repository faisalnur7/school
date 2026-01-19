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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();

            $table->string('title_bn')->comment('Lesson title in Bengali');
            $table->string('title_en')->comment('Lesson title in English');
            $table->text('description')->nullable()->comment('Lesson description');

            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();

            $table->string('attachment')->nullable()->comment('Optional file attachment or video URL');

            $table->enum('status', ['draft', 'published'])->default('draft')->comment('Lesson status');

            $table->timestamps();

            // Foreign keys
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
