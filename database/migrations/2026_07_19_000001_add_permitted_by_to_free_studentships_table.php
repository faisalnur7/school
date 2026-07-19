<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('free_studentships', function (Blueprint $table) {
            if (!Schema::hasColumn('free_studentships', 'permitted_by')) {
                $table->string('permitted_by')->nullable()->after('percentage');
            }
        });
    }

    public function down(): void
    {
        Schema::table('free_studentships', function (Blueprint $table) {
            if (Schema::hasColumn('free_studentships', 'permitted_by')) {
                $table->dropColumn('permitted_by');
            }
        });
    }
};
