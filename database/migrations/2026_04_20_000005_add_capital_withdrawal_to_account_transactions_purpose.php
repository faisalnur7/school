<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE account_transactions MODIFY COLUMN purpose ENUM(
            'income',
            'expense',
            'student_payment',
            'salary',
            'transfer_in',
            'transfer_out',
            'adjustment',
            'opening',
            'capital',
            'withdrawal'
        ) NOT NULL DEFAULT 'adjustment'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE account_transactions MODIFY COLUMN purpose ENUM(
            'income',
            'expense',
            'student_payment',
            'salary',
            'transfer_in',
            'transfer_out',
            'adjustment',
            'opening'
        ) NOT NULL DEFAULT 'adjustment'");
    }
};
