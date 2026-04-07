<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('designation_salary_defaults', function (Blueprint $table) {
            $table->id();
            $table->foreignId('designation_id')->unique()->constrained('designations')->cascadeOnDelete();
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('house_rent', 10, 2)->default(0);
            $table->decimal('medical_allowance', 10, 2)->default(0);
            $table->decimal('transport_allowance', 10, 2)->default(0);
            $table->decimal('special_allowance', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('other_deductions', 10, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('designation_salary_defaults'); }
};
