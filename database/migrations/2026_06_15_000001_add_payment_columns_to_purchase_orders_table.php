<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'paid_amount')) {
                $table->decimal('paid_amount', 12, 2)->default(0)->after('total_amount');
            }

            if (!Schema::hasColumn('purchase_orders', 'due_amount')) {
                $table->decimal('due_amount', 12, 2)->default(0)->after('paid_amount');
            }

            if (!Schema::hasColumn('purchase_orders', 'status')) {
                $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid')->after('due_amount');
            }

            if (!Schema::hasColumn('purchase_orders', 'last_paid_at')) {
                $table->date('last_paid_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            foreach (['last_paid_at', 'status', 'due_amount', 'paid_amount'] as $column) {
                if (Schema::hasColumn('purchase_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
