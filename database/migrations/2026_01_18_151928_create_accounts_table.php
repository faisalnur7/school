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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Account name, e.g., Cash, Bank, Tuition Income');
            $table->enum('type', ['income', 'expense', 'asset', 'liability', 'equity'])->comment('Type of account');
            $table->decimal('opening_balance', 15, 2)->default(0)->comment('Opening balance for the account');
            $table->text('description')->nullable()->comment('Optional notes about this account');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
