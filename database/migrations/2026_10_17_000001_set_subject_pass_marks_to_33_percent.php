<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Set every subject and class-wise configuration pass mark to 33% of
     * the application's generated full marks.
     */
    public function up(): void
    {
        DB::table('subjects')->update([
            'pass_mark' => DB::raw('ROUND((COALESCE(creative_marks, 0) + COALESCE(mcq_marks, 0) + COALESCE(practical_marks, 0) + COALESCE(viva_marks, 0)) * 0.33, 2)'),
        ]);

        DB::table('subject_class_configs')->update([
            'pass_mark' => DB::raw('ROUND((COALESCE(creative_marks, 0) + COALESCE(mcq_marks, 0) + COALESCE(practical_marks, 0) + COALESCE(viva_marks, 0)) * 0.33, 2)'),
        ]);
    }

    /**
     * This data migration intentionally has no automatic rollback because the
     * previous pass-mark values are not derivable from the new values.
     */
    public function down(): void
    {
        // The original pass-mark values cannot be reconstructed safely.
    }
};
