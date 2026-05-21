<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password_change_verification_code')->nullable()->after('login_verification_expires_at');
            $table->timestamp('password_change_verification_expires_at')->nullable()->after('password_change_verification_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'password_change_verification_code',
                'password_change_verification_expires_at',
            ]);
        });
    }
};
