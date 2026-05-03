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
        Schema::create('subject_class_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('school_class_id')->constrained('school_classes')->onDelete('cascade');
            
            // Class-wise marks configuration (overrides subject defaults)
            $table->decimal('creative_marks', 5, 2)->nullable()->comment('Creative marks (CQ) for this class');
            $table->decimal('mcq_marks', 5, 2)->nullable()->comment('MCQ marks for this class');
            $table->decimal('practical_marks', 5, 2)->nullable()->comment('Practical marks for this class');
            $table->decimal('viva_marks', 5, 2)->nullable()->comment('Viva marks for this class');
            $table->decimal('total_marks', 5, 2)->virtualAs('COALESCE(creative_marks, 0) + COALESCE(mcq_marks, 0) + COALESCE(practical_marks, 0) + COALESCE(viva_marks, 0)')->comment('Total marks (auto calculated)');
            $table->decimal('pass_mark', 5, 2)->nullable()->comment('Pass mark for this class');
            
            $table->timestamps();
            
            // Unique constraint: one config per subject per class
            $table->unique(['subject_id', 'school_class_id'], 'subject_class_config_subject_class_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_class_configs');
    }
};
