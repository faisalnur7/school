<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('employee_type', ['teacher', 'staff']);
            $table->unsignedTinyInteger('hierarchy_level');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->unique(['name', 'employee_type']);
        });
    }
    public function down(): void { Schema::dropIfExists('designations'); }
};
