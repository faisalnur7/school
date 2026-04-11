<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('budget_allocations', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable()->after('id');
            $table->foreign('account_id')->references('id')->on('accounts')->nullOnDelete();
        });

        // Drop old budget_head_id FK and column if it exists
        if (Schema::hasColumn('budget_allocations', 'budget_head_id')) {
            Schema::table('budget_allocations', function (Blueprint $table) {
                $table->dropForeign(['budget_head_id']);
                $table->dropColumn('budget_head_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('budget_allocations', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
            $table->unsignedBigInteger('budget_head_id')->nullable();
            $table->foreign('budget_head_id')->references('id')->on('budget_heads')->nullOnDelete();
        });
    }
};
