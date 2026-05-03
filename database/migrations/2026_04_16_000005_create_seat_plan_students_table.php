<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seat_plan_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seat_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('seat_number');
            $table->timestamps();

            $table->unique(['seat_plan_id', 'student_id']);
            $table->unique(['seat_plan_id', 'room_id', 'seat_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seat_plan_students');
    }
};
