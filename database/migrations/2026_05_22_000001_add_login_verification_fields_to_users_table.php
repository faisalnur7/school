<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('login_verification_enabled')->default(false)->after('remember_token');
            $table->string('login_verification_code')->nullable()->after('login_verification_enabled');
            $table->timestamp('login_verification_expires_at')->nullable()->after('login_verification_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'login_verification_enabled',
                'login_verification_code',
                'login_verification_expires_at',
            ]);
        });
    }
};
