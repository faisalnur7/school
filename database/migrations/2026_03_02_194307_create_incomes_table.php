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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('income_category_id')->constrained('income_categories')->restrictOnDelete();
            $table->string('title');
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('income_date');
            $table->enum('payment_method', ['Cash', 'Bank Transfer', 'Cheque', 'Mobile Banking', 'Other'])->default('Cash');
            $table->string('reference_no')->nullable();
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('income_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
