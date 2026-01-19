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
        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('staff_id')->comment('Reference to staff member');
            $table->date('attendance_date')->comment('Date of attendance');
            $table->time('time_in')->nullable()->comment('Time staff checked in');
            $table->time('time_out')->nullable()->comment('Time staff checked out');
            $table->enum('status', ['present', 'absent', 'leave', 'half_day'])->default('present');
            $table->text('remarks')->nullable()->comment('Optional remarks or notes');

            $table->timestamps();

            // Foreign key
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_attendances');
    }
};
