<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // budget_heads already exists with correct structure
        // budget_allocations exists but needs columns added
        Schema::table('budget_allocations', function (Blueprint $table) {
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('expense_category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('period', ['monthly', 'yearly'])->default('yearly');
            $table->year('fiscal_year');
            $table->tinyInteger('fiscal_month')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('budget_allocations', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropForeign(['expense_category_id']);
            $table->dropForeign(['recorded_by']);
            $table->dropColumn(['account_id','expense_category_id','amount','period','fiscal_year','fiscal_month','notes','recorded_by']);
        });
    }
};
