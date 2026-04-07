<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('issued_to');           // name of person / department
            $table->string('issued_to_type')->nullable(); // user, student, department
            $table->integer('quantity')->default(1);
            $table->date('issue_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['issued', 'returned', 'lost'])->default('issued');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_issues');
    }
};
