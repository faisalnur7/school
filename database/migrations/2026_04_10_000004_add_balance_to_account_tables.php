<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->decimal('balance', 12, 2)->default(0)->after('opening_balance');
        });

        Schema::table('mobile_banking_accounts', function (Blueprint $table) {
            $table->decimal('balance', 12, 2)->default(0)->after('opening_balance');
        });

        Schema::table('hand_cashes', function (Blueprint $table) {
            $table->decimal('balance', 12, 2)->default(0)->after('opening_amount');
        });

        // Seed balance from opening values for existing records
        DB::statement('UPDATE bank_accounts SET balance = opening_balance');
        DB::statement('UPDATE mobile_banking_accounts SET balance = opening_balance');
        DB::statement('UPDATE hand_cashes SET balance = opening_amount');
    }

    public function down(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropColumn('balance');
        });

        Schema::table('mobile_banking_accounts', function (Blueprint $table) {
            $table->dropColumn('balance');
        });

        Schema::table('hand_cashes', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
    }
};
