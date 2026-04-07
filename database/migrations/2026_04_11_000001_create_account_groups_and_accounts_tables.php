<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // account_groups already exists — skip creation
        // accounts table already exists — add new columns only
        Schema::table('accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('account_group_id')->nullable()->after('name');
            $table->string('reference_type')->nullable()->after('account_group_id');
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            $table->text('notes')->nullable()->after('reference_id');
            $table->foreign('account_group_id')->references('id')->on('account_groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['account_group_id']);
            $table->dropColumn(['account_group_id', 'reference_type', 'reference_id', 'notes']);
        });
    }
};
