<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_sale_items', 'paid_amount')) {
                $table->decimal('paid_amount', 12, 2)->default(0)->after('subtotal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_sale_items', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_sale_items', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
        });
    }
};
