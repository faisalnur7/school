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
        Schema::create('fee_sets', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Fee set name, e.g., Tuition Fee, Lab Fee');
            $table->string('bn_name')->nullable()->comment('Fee set name, e.g., Tuition Fee, Lab Fee');
            $table->unsignedBigInteger('school_class_id')->nullable()->comment('Class this fee set applies to');
            $table->unsignedBigInteger('group_id')->nullable()->comment('Optional group/stream within the class');
            $table->unsignedBigInteger('academic_session_id')->nullable()->comment('Academic session to isolate the fee sets');
            $table->enum('frequency', ['monthly', 'yearly', 'others'])->default('monthly')->comment('Billing frequency');
            $table->text('description')->nullable()->comment('Optional notes about this fee set');
            $table->timestamps();

            // Foreign keys
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('set null');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('set null');
            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->onDelete('set null');
            
            $table->index('school_class_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_sets');
    }
};
