<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_trails', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('action_name')->after('user_id');
            $table->text('important_description')->after('action_name');
            $table->string('username')->after('important_description');
            $table->date('action_date')->after('username');
            $table->time('action_time')->after('action_date');
            $table->string('route_name')->nullable()->after('action_time');
            $table->string('http_method', 10)->nullable()->after('route_name');
            $table->string('ip_address', 45)->nullable()->after('http_method');
        });
    }

    public function down(): void
    {
        Schema::table('audit_trails', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn([
                'action_name',
                'important_description',
                'username',
                'action_date',
                'action_time',
                'route_name',
                'http_method',
                'ip_address',
            ]);
        });
    }
};
