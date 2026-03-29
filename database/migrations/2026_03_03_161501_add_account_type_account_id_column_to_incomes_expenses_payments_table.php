<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->string('account_type')->nullable()->after('payment_method');
            $table->unsignedBigInteger('account_id')->nullable()->after('account_type');
            $table->index(['account_type', 'account_id']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->string('account_type')->nullable()->after('payment_method');
            $table->unsignedBigInteger('account_id')->nullable()->after('account_type');
            $table->index(['account_type', 'account_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('account_type')->nullable()->after('payment_method');
            $table->unsignedBigInteger('account_id')->nullable()->after('account_type');
            $table->index(['account_type', 'account_id']);
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropIndex(['account_type', 'account_id']);
            $table->dropColumn(['account_type', 'account_id']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['account_type', 'account_id']);
            $table->dropColumn(['account_type', 'account_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['account_type', 'account_id']);
            $table->dropColumn(['account_type', 'account_id']);
        });
    }
};