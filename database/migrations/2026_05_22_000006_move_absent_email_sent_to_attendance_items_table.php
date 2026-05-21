<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_items', function (Blueprint $table) {
            if (! Schema::hasColumn('attendance_items', 'is_absent_email_sent')) {
                $table->boolean('is_absent_email_sent')->default(false)->after('status');
            }
        });

        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'is_absent_email_sent')) {
                $table->dropColumn('is_absent_email_sent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (! Schema::hasColumn('attendances', 'is_absent_email_sent')) {
                $table->boolean('is_absent_email_sent')->default(false)->after('taken_by');
            }
        });

        Schema::table('attendance_items', function (Blueprint $table) {
            if (Schema::hasColumn('attendance_items', 'is_absent_email_sent')) {
                $table->dropColumn('is_absent_email_sent');
            }
        });
    }
};
