<?php

use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\StudentAcademicInformation;
use App\Services\GradingService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Recalculate stored letter grades and GPA values from the saved marks.
     * Tutorial marks are excluded because tutorial exams intentionally do not
     * store letter grades or GPA values.
     */
    public function up(): void
    {
        $academicInfoCache = [];

        ExamMark::query()
            ->with(['exam', 'subject'])
            ->whereHas('exam', fn ($query) => $query->where('type', Exam::TYPE_TERMINAL))
            ->chunkById(500, function ($marks) use (&$academicInfoCache): void {
                foreach ($marks as $mark) {
                    if (! $mark->subject || $mark->total === null) {
                        continue;
                    }

                    $academicInfoKey = $mark->student_id . ':' . $mark->exam->academic_session_id;
                    $classId = $academicInfoCache[$academicInfoKey] ??= StudentAcademicInformation::query()
                        ->where('student_id', $mark->student_id)
                        ->where('academic_session_id', $mark->exam->academic_session_id)
                        ->where('is_current', true)
                        ->value('school_class_id');

                    $fullMarks = (float) $mark->subject
                        ->getEffectiveMarksForClass($classId)['total_marks'];

                    if ($mark->is_absent) {
                        $mark->update([
                            'letter_grade' => 'AB',
                            'gpa' => 0,
                        ]);

                        continue;
                    }

                    $grade = GradingService::getGrade((float) $mark->total, $fullMarks);

                    $mark->update([
                        'letter_grade' => $grade['letter'],
                        'gpa' => $grade['gpa'],
                    ]);
                }
            });
    }

    /**
     * The previous stored grades cannot be reconstructed safely.
     */
    public function down(): void
    {
        // This data migration intentionally has no automatic rollback.
    }
};
