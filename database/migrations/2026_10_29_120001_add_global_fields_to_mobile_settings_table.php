<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mobile_settings', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('id');
            $table->text('maintenance_message')->nullable()->after('is_active');
            $table->foreignId('updated_by')->nullable()->after('maintenance_message')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mobile_settings', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['is_active', 'maintenance_message', 'updated_by']);
        });
    }
};
