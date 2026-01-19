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
        Schema::create('topics', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->unsignedBigInteger('lesson_id')->comment('Associated lesson');
            $table->unsignedBigInteger('lesson_plan_id')->nullable()->comment('Optional lesson plan');

            // Topic details
            $table->string('title_bn')->comment('Topic title in Bengali');
            $table->string('title_en')->comment('Topic title in English');
            $table->text('description')->nullable()->comment('Topic description');

            $table->enum('status', ['draft', 'planned', 'completed'])->default('draft')->comment('Topic status');

            $table->timestamps();

            // Foreign keys
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->foreign('lesson_plan_id')->references('id')->on('lesson_plans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
