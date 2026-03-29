<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('name');
            $table->enum('type', ['fixed', 'percentage'])->default('percentage');
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->unsignedBigInteger('academic_session_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
            
            $table->index('student_id');
            $table->index('academic_session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
