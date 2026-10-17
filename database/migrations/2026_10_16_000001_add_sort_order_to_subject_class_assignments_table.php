<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subject_class_assignments', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->nullable()->after('is_active');
            $table->index(['school_class_id', 'group_id', 'sort_order'], 'subject_assignment_order_index');
        });

        DB::table('subject_class_assignments as assignments')
            ->join('subjects', 'subjects.id', '=', 'assignments.subject_id')
            ->select('assignments.id', 'assignments.school_class_id', 'assignments.group_id', 'subjects.name')
            ->orderBy('assignments.school_class_id')
            ->orderBy('assignments.group_id')
            ->orderBy('subjects.name')
            ->orderBy('assignments.id')
            ->get()
            ->groupBy(fn ($assignment) => $assignment->school_class_id.'|'.($assignment->group_id ?? 'common'))
            ->each(function ($assignments) {
                foreach ($assignments->values() as $index => $assignment) {
                    DB::table('subject_class_assignments')
                        ->where('id', $assignment->id)
                        ->update(['sort_order' => $index + 1]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('subject_class_assignments', function (Blueprint $table) {
            $table->dropIndex('subject_assignment_order_index');
            $table->dropColumn('sort_order');
        });
    }
};
