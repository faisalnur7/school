<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('qualification_id')->constrained('educational_qualifications')->restrictOnDelete();
            $table->string('institution_name', 150)->nullable();
            $table->unsignedSmallInteger('passing_year')->nullable();
            $table->string('result', 100)->nullable();
            $table->string('document_path')->nullable();
            $table->string('document_original_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_qualifications');
    }
};
