<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->boolean('is_reversal')->default(false)->after('recorded_by');
            $table->unsignedBigInteger('reversed_id')->nullable()->after('is_reversal');

            $table->index('is_reversal');
            $table->index('reversed_id');
        });
    }

    public function down(): void
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropIndex(['is_reversal']);
            $table->dropIndex(['reversed_id']);
            $table->dropColumn(['is_reversal', 'reversed_id']);
        });
    }
};
