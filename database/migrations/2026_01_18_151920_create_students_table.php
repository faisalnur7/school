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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            
            // Student basic info
            $table->string('full_name_bn')->nullable();
            $table->string('full_name_en');
            $table->date('date_of_birth')->nullable();
            $table->enum('sex', ['male', 'female'])->nullable();

            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            $table->string('blood_group')->nullable();

            // Father info
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_organization')->nullable();
            $table->string('father_designation')->nullable();
            $table->string('father_location')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('father_email')->nullable();

            // Mother info
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_organization')->nullable();
            $table->string('mother_designation')->nullable();
            $table->string('mother_location')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('mother_email')->nullable();

            // Present address
            $table->text('present_address')->nullable();
            // Permanent address
            $table->text('permanent_address')->nullable();

            // Guardian info
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->text('guardian_address')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_mobile')->nullable();
            $table->string('guardian_email')->nullable();

            // Academic history
            $table->string('previous_school')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
