<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_routine_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_routine_id')->constrained('exam_routines')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->unsignedInteger('sort_order');
            $table->enum('slot', ['morning', 'noon']);
            $table->date('exam_date');
            $table->timestamps();

            $table->unique(['exam_routine_id', 'subject_id'], 'exam_routine_items_routine_subject_unique');
            $table->index(['exam_routine_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_routine_items');
    }
};
