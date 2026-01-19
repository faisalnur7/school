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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('staff_id')->comment('Reference to staff member');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('allowances', 10, 2)->default(0)->comment('Additional allowances');
            $table->decimal('deductions', 10, 2)->default(0)->comment('Deductions like tax, absence, loan');
            $table->decimal('net_salary', 10, 2)->computedAs('basic_salary + allowances - deductions');
            $table->date('pay_date')->comment('Salary payment date');
            $table->enum('status', ['pending', 'paid'])->default('pending');

            $table->text('remarks')->nullable();

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
        Schema::dropIfExists('payrolls');
    }
};
