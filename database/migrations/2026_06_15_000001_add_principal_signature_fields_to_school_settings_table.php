<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('school_settings', 'principal_designation')) {
                $table->string('principal_designation')->nullable()->after('letter_head');
            }
            if (! Schema::hasColumn('school_settings', 'principal_name')) {
                $table->string('principal_name')->nullable()->after('principal_designation');
            }
            if (! Schema::hasColumn('school_settings', 'principal_school_name')) {
                $table->string('principal_school_name')->nullable()->after('principal_name');
            }
            if (! Schema::hasColumn('school_settings', 'principal_phone')) {
                $table->string('principal_phone')->nullable()->after('principal_school_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn([
                'principal_designation',
                'principal_name',
                'principal_school_name',
                'principal_phone',
            ]);
        });
    }
};
