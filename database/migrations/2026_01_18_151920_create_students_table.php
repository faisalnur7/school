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

            // ================= BASIC INFO =================
            $table->string('full_name_bn')->nullable();
            $table->string('full_name_en');
            $table->string('image')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('gender')->nullable();
            $table->string('birth_certificate_number')->nullable();
            $table->integer('religion')->nullable();
            $table->integer('blood_group')->nullable();
            $table->boolean('disable')->default(false);

            // ================= FATHER =================
            $table->string('father_name')->nullable();
            $table->string('father_nid_number')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('father_email')->nullable();

            // ================= MOTHER =================
            $table->string('mother_name')->nullable();
            $table->string('mother_nid_number')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('mother_email')->nullable();

            // ================= INCOME =================
            $table->string('annual_income')->nullable();

            // ================= PRESENT ADDRESS =================
            $table->text('present_address')->nullable();
            $table->foreignId('present_division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('present_district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('present_police_station_id')->nullable()->constrained('police_stations')->nullOnDelete();
            $table->foreignId('present_post_office_id')->nullable()->constrained('post_offices')->nullOnDelete();

            // ================= PERMANENT ADDRESS =================
            $table->text('permanent_address')->nullable();
            $table->foreignId('permanent_division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('permanent_district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('permanent_police_station_id')->nullable()->constrained('police_stations')->nullOnDelete();
            $table->foreignId('permanent_post_office_id')->nullable()->constrained('post_offices')->nullOnDelete();

            // ================= GUARDIAN =================
            $table->unsignedTinyInteger('guardian_type')->nullable()->default(1)->comment('1=Father, 2=Mother, 3=Other');
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->text('guardian_address')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_email')->nullable();

            // ================= ACADEMIC HISTORY =================
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
