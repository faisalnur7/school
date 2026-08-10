<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_items', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_items', 'scholarship_amount')) {
                $table->decimal('scholarship_amount', 12, 2)->default(0)->after('amount');
            }

            if (!Schema::hasColumn('payment_items', 'free_studentship_amount')) {
                $table->decimal('free_studentship_amount', 12, 2)->default(0)->after('scholarship_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_items', function (Blueprint $table) {
            if (Schema::hasColumn('payment_items', 'free_studentship_amount')) {
                $table->dropColumn('free_studentship_amount');
            }

            if (Schema::hasColumn('payment_items', 'scholarship_amount')) {
                $table->dropColumn('scholarship_amount');
            }
        });
    }
};
