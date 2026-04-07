<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('designation_id')->constrained('designations');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('house_rent', 10, 2)->default(0);
            $table->decimal('medical_allowance', 10, 2)->default(0);
            $table->decimal('transport_allowance', 10, 2)->default(0);
            $table->decimal('special_allowance', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('other_deductions', 10, 2)->default(0);
            $table->date('effective_from');
            $table->timestamps();
            $table->unique(['employee_id', 'effective_from']);
        });
    }
    public function down(): void { Schema::dropIfExists('salary_structures'); }
};
