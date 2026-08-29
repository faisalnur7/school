<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admission_payments')) {
            return;
        }

        Schema::table('admission_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('admission_payments', 'gross_amount')) {
                $table->decimal('gross_amount', 10, 2)->default(0)->after('amount');
            }
            if (! Schema::hasColumn('admission_payments', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('gross_amount');
            }
            if (! Schema::hasColumn('admission_payments', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('discount_amount');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('admission_payments')) {
            return;
        }

        Schema::table('admission_payments', function (Blueprint $table) {
            $columns = collect(['gross_amount', 'discount_amount', 'total_amount'])
                ->filter(fn ($column) => Schema::hasColumn('admission_payments', $column))
                ->all();

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
