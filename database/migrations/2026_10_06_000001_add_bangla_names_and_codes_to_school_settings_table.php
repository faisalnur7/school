<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('name_bn')->nullable()->after('name');
            $table->text('address_bn')->nullable()->after('address');
            $table->string('ipemis_code')->nullable()->after('eiin');
            $table->string('school_code')->nullable()->after('ipemis_code');
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['name_bn', 'address_bn', 'ipemis_code', 'school_code']);
        });
    }
};
