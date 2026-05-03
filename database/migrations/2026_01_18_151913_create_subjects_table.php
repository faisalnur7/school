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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name')->comment('Subject name');
            $table->string('code')->unique()->nullable()->comment('Subject code');
            $table->enum('type', ['mandatory', 'optional'])->default('mandatory')->comment('Subject type');
            
            // Multiple papers handling (combined subjects)
            $table->boolean('has_multiple_papers')->default(false)->comment('Has multiple papers');
            $table->boolean('combine_papers_for_result')->default(true)->comment('Combine papers for result');
            $table->foreignId('parent_id')->nullable()->constrained('subjects')->onDelete('cascade')->comment('Parent subject for combined papers');
            $table->boolean('is_parent')->default(false)->comment('Is parent subject (has papers)');
            $table->boolean('is_paper')->default(false)->comment('Is a paper/subject part');
            
            // Marks Distribution
            $table->decimal('creative_marks', 5, 2)->default(0)->comment('Creative marks (CQ)');
            $table->decimal('mcq_marks', 5, 2)->default(0)->comment('MCQ marks');
            $table->decimal('practical_marks', 5, 2)->default(0)->comment('Practical marks');
            $table->decimal('viva_marks', 5, 2)->default(0)->comment('Viva marks');
            $table->decimal('total_marks', 5, 2)->virtualAs('creative_marks + mcq_marks + practical_marks + viva_marks')->comment('Total marks (auto calculated)');
            
            // Pass mark
            $table->decimal('pass_mark', 5, 2)->default(0)->comment('Pass mark');
            
            // Status
            $table->boolean('is_active')->default(true)->comment('Active/Inactive');
            
            // Timestamps
            $table->timestamps();
            
            // Soft delete
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};