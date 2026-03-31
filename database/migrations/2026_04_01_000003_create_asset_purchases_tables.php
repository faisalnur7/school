<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->date('purchase_date');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('payment_type'); // hand_cash, bank, mobile
            $table->string('account_type')->nullable(); // App\Models\HandCash etc
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('expense_id')->nullable(); // FK to expenses
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->timestamps();

            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('asset_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_purchase_id');
            $table->unsignedBigInteger('asset_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->timestamps();

            $table->foreign('asset_purchase_id')->references('id')->on('asset_purchases')->onDelete('cascade');
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_purchase_items');
        Schema::dropIfExists('asset_purchases');
    }
};
