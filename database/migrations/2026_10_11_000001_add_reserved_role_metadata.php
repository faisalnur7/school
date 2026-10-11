<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('system_key', 50)->nullable()->unique()->after('name');
            $table->boolean('is_reserved')->default(false)->after('system_key');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->boolean('exposed_to_roles')->default(true)->after('category_id');
        });

        DB::table('roles')->where('name', 'Super Admin')->update([
            'system_key' => 'super_admin',
            'is_reserved' => true,
        ]);
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('exposed_to_roles');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique(['system_key']);
            $table->dropColumn(['system_key', 'is_reserved']);
        });
    }
};
