<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routine_teacher_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedInteger('sort_order');
            $table->timestamps();

            $table->unique(['academic_session_id', 'teacher_id']);
            $table->index(['academic_session_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routine_teacher_orders');
    }
};
