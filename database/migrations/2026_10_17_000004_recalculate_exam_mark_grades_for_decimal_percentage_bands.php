<?php

use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\StudentAcademicInformation;
use App\Services\GradingService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Refresh stored terminal grades after correcting decimal percentage bands.
     * Marks and attendance data are not changed.
     */
    public function up(): void
    {
        $classCache = [];

        ExamMark::query()
            ->with(['exam', 'subject'])
            ->whereHas('exam', fn ($query) => $query->where('type', Exam::TYPE_TERMINAL))
            ->whereNotNull('total')
            ->chunkById(500, function ($marks) use (&$classCache): void {
                foreach ($marks as $mark) {
                    if (! $mark->subject) {
                        continue;
                    }

                    $cacheKey = $mark->student_id . ':' . $mark->exam->academic_session_id;
                    $classId = $classCache[$cacheKey] ??= StudentAcademicInformation::query()
                        ->where('student_id', $mark->student_id)
                        ->where('academic_session_id', $mark->exam->academic_session_id)
                        ->where('is_current', true)
                        ->value('school_class_id');

                    $fullMarks = (float) $mark->subject
                        ->getEffectiveMarksForClass($classId)['total_marks'];

                    $grade = $mark->is_absent
                        ? ['letter' => 'AB', 'gpa' => 0]
                        : GradingService::getGrade((float) $mark->total, $fullMarks);

                    if ($mark->letter_grade !== $grade['letter'] || (float) $mark->gpa !== (float) $grade['gpa']) {
                        $mark->update([
                            'letter_grade' => $grade['letter'],
                            'gpa' => $grade['gpa'],
                        ]);
                    }
                }
            });
    }

    /**
     * Derived grades cannot be safely restored to their previous values.
     */
    public function down(): void
    {
        // This data migration intentionally has no automatic rollback.
    }
};
