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

        DB::statement(
            'UPDATE subject_class_configs scc
             INNER JOIN subjects s ON s.id = scc.subject_id
             SET scc.tutorial_marks = s.tutorial_marks
             WHERE scc.tutorial_marks IS NULL'
        );
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
