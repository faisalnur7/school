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
        Schema::create('admission_applications', function (Blueprint $table) {
            $table->id();

            // Applicant basic info
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
            $table->string('present_phone')->nullable();
            $table->string('present_mobile')->nullable();

            // Permanent address
            $table->text('permanent_address')->nullable();
            $table->string('permanent_phone')->nullable();
            $table->string('permanent_mobile')->nullable();

            // Guardian (if different)
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->text('guardian_address')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_mobile')->nullable();
            $table->string('guardian_email')->nullable();

            // Academic info
            $table->string('previous_school')->nullable();

            // Admission specific
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('academic_session_id')->nullable();
            $table->string('application_no')->unique()->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'cancelled'
            ])->default('pending');

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_applications');
    }
};
