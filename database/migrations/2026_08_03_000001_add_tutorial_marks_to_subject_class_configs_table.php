<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subject_class_configs', function (Blueprint $table) {
            $table->decimal('tutorial_marks', 5, 2)->nullable()->after('viva_marks')->comment('Tutorial marks for this class');
        });

        DB::table('subject_class_configs')
            ->whereNull('tutorial_marks')
            ->whereNotNull('subject_id')
            ->update([
                'tutorial_marks' => DB::raw('(SELECT tutorial_marks FROM subjects WHERE subjects.id = subject_class_configs.subject_id)'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_class_configs', function (Blueprint $table) {
            $table->dropColumn('tutorial_marks');
        });
    }
};
