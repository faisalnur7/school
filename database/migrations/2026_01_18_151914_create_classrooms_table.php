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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();

            $table->string('name_bn')->comment('Classroom name in Bengali');
            $table->string('name_en')->comment('Classroom name in English');
            $table->integer('capacity')->nullable()->comment('Number of students classroom can accommodate');
            $table->string('location')->nullable()->comment('Optional location or description');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
