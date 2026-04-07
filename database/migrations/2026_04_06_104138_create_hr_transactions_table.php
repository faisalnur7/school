<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('hr_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('payroll_id')->constrained('hr_payrolls')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method');
            $table->string('account_head')->default('Salary Expense');
            $table->date('transaction_date');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('hr_transactions'); }
};
