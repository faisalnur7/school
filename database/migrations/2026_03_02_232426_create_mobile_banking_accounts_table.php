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
        Schema::create('mobile_banking_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('provider');                         // bKash, Nagad, Rocket, Upay etc.
            $table->string('account_name');
            $table->string('account_number')->unique();
            $table->enum('account_type', ['Personal', 'Agent', 'Merchant'])->default('Personal');
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->date('opening_date');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_banking_accounts');
    }
};
