<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();

            // Which account (BankAccount, MobileBankingAccount, HandCash)
            $table->string('account_type');
            $table->unsignedBigInteger('account_id');

            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_after', 12, 2);  // snapshot after transaction

            $table->enum('purpose', [
                'income',
                'expense',
                'student_payment',
                'salary',
                'transfer_in',
                'transfer_out',
                'adjustment',
                'opening',
            ])->default('adjustment');

            $table->string('reference_no')->nullable();
            $table->text('description')->nullable();
            $table->date('transaction_date');

            // Links back to Income, Expense, Payment etc.
            $table->string('transactionable_type')->nullable();
            $table->unsignedBigInteger('transactionable_id')->nullable();

            $table->foreignId('recorded_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['account_type', 'account_id'], 'acct_trans_account_idx');
            $table->index(['transactionable_type', 'transactionable_id'], 'acct_trans_transactionable_idx');
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
    }
};