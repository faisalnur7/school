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
            $table->string('image')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('gender')->nullable();

            $table->string('birth_certificate_number')->nullable();
            $table->integer('religion')->nullable();
            $table->integer('blood_group')->nullable();
            $table->integer('disable')->nullable();

            // Father info
            $table->string('father_name')->nullable();
            $table->string('father_nid_number')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('father_email')->nullable();

            // Mother info
            $table->string('mother_name')->nullable();
            $table->string('mother_nid_number')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('mother_email')->nullable();
            $table->string('annual_income')->nullable();

            // Present address
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();

            $table->foreignId('division_id')->nullable()->constrained('divisions');
            $table->foreignId('district_id')->nullable()->constrained('districts');
            $table->foreignId('police_station_id')->nullable()->constrained('police_stations');
            $table->foreignId('post_office_id')->nullable()->constrained('post_offices');

            // Guardian info
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->text('guardian_address')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_email')->nullable();

            // Academic history
            $table->string('previous_school')->nullable();
            $table->string('previous_class_appeared')->nullable();
            $table->string('tc_number')->nullable();
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
