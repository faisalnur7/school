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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();

            // Personal Information
            $table->string('full_name_bn')->comment('Full name in Bengali');
            $table->string('full_name_en')->comment('Full name in English');
            $table->date('dob')->nullable()->comment('Date of birth');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('nationality')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('religion')->nullable();

            // Contact Information
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();

            // Professional Information
            $table->string('designation')->nullable()->comment('Job title');
            $table->string('department')->nullable()->comment('Department name');
            $table->date('joining_date')->nullable();
            $table->string('qualification')->nullable();
            $table->text('experience')->nullable();

            // Optional
            $table->string('profile_photo')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
