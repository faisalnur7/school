<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('discount_type')->nullable()->after('amount');   // 'flat' | 'percent'
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_type');
            $table->decimal('gross_amount', 10, 2)->default(0)->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_amount', 'gross_amount']);
        });
    }
};
