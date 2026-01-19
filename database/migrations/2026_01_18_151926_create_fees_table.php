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
        Schema::create('fees', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_id')->comment('Student who has this fee');
            $table->unsignedBigInteger('fee_set_id')->comment('Reference to class-wise fee set');
            $table->decimal('amount', 10, 2)->comment('Fee amount for this student');
            $table->date('due_date')->nullable()->comment('Due date for payment');
            $table->enum('status', ['pending', 'paid', 'partial'])->default('pending')->comment('Payment status');
            $table->text('remarks')->nullable()->comment('Optional notes about this fee');

            $table->timestamps();

            // Foreign keys
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('fee_set_id')->references('id')->on('fee_sets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
