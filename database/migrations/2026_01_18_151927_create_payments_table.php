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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('fee_id')->comment('Reference to the student fee');
            $table->decimal('amount', 10, 2)->comment('Amount paid');
            $table->date('payment_date')->nullable()->comment('Date of payment');
            $table->string('payment_method')->nullable()->comment('Cash, Bank Transfer, Card, etc.');
            $table->string('transaction_id')->nullable()->comment('Optional transaction reference');
            $table->text('remarks')->nullable()->comment('Optional notes about payment');

            $table->timestamps();

            // Foreign key
            $table->foreign('fee_id')->references('id')->on('fees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
