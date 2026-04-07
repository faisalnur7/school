<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payment_informations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->unique()->constrained('employees')->cascadeOnDelete();
            $table->enum('payment_method', ['bank', 'cash', 'mobile_wallet'])->default('cash');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('mobile_wallet_number')->nullable();
            $table->string('mobile_wallet_provider')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('payment_informations'); }
};
