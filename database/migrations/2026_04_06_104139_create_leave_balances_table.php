<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('leave_type', ['casual', 'sick', 'annual', 'maternity', 'other']);
            $table->unsignedInteger('total_leave')->default(0);
            $table->unsignedInteger('used_leave')->default(0);
            $table->unsignedInteger('remaining_leave')->default(0);
            $table->timestamps();
            $table->unique(['employee_id', 'leave_type']);
        });
    }
    public function down(): void { Schema::dropIfExists('leave_balances'); }
};
