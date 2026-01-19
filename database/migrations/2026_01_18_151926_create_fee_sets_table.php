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
            $table->unsignedBigInteger('school_class_id')->nullable()->comment('Class this fee set applies to');
            $table->unsignedBigInteger('fee_category_id')->nullable()->comment('Category of the fee set');
            $table->decimal('amount', 10, 2)->comment('Amount for this fee set');
            $table->enum('frequency', ['monthly', 'quarterly', 'yearly'])->default('monthly')->comment('Billing frequency');
            $table->text('description')->nullable()->comment('Optional notes about this fee set');
            $table->timestamps();

            // Foreign keys
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('set null');
            $table->foreign('fee_category_id')->references('id')->on('fee_categories')->onDelete('set null');
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
