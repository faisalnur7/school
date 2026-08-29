<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('admission_admit_cards', 'roll_number')) {
            Schema::table('admission_admit_cards', function (Blueprint $table) {
                $table->string('roll_number')->nullable()->after('admit_card_number');
            });
        }

        if (! Schema::hasColumn('admission_admit_cards', 'candidate_id')) {
            Schema::table('admission_admit_cards', function (Blueprint $table) {
                $table->string('candidate_id')->nullable()->unique()->after('roll_number');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('admission_admit_cards', 'candidate_id')) {
            Schema::table('admission_admit_cards', function (Blueprint $table) {
                $table->dropUnique(['candidate_id']);
                $table->dropColumn('candidate_id');
            });
        }

        if (Schema::hasColumn('admission_admit_cards', 'roll_number')) {
            Schema::table('admission_admit_cards', function (Blueprint $table) {
                $table->dropColumn('roll_number');
            });
        }
    }
};
