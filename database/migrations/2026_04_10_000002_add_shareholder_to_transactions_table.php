<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL does not support modifying enum directly; use a raw ALTER
        DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('income','expense','capital','withdrawal') NOT NULL");

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('shareholder_id')
                ->nullable()
                ->after('expense_category_id')
                ->constrained('shareholders')
                ->nullOnDelete();

            $table->index('shareholder_id');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['shareholder_id']);
            $table->dropIndex(['shareholder_id']);
            $table->dropColumn('shareholder_id');
        });

        DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('income','expense') NOT NULL");
    }
};
