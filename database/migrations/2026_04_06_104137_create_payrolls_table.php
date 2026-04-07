<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('hr_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedTinyInteger('payroll_month');
            $table->unsignedSmallInteger('payroll_year');
            $table->decimal('gross_salary', 10, 2);
            $table->decimal('other_deductions', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2);
            $table->enum('payment_method', ['bank', 'cash', 'mobile_wallet'])->default('cash');
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->boolean('is_locked')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'payroll_month', 'payroll_year']);
        });
    }
    public function down(): void { Schema::dropIfExists('hr_payrolls'); }
};
