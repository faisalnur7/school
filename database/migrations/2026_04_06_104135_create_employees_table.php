<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('employee_id')->unique();
            $table->string('name');
            $table->enum('employee_type', ['teacher', 'staff']);
            $table->foreignId('designation_id')->constrained('designations');
            $table->string('department')->nullable();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreignId('reporting_manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->date('joining_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('employees'); }
};
