<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use Illuminate\Support\Collection;

class YearlyFinalReportService
{
    public function getStudents(int $sessionId, int $classId, ?int $sectionId = null, ?int $studentId = null): Collection
    {
        if ($studentId) {
            return Student::where('id', $studentId)
                ->orWhere('student_cid', $studentId)
                ->get();
        }

        return Student::where('status', 1)
            ->whereHas('academicInformations', function ($query) use ($sessionId, $classId, $sectionId) {
                $query->where('academic_session_id', $sessionId)
                    ->where('school_class_id', $classId);

                if ($sectionId) {
                    $query->where('section_id', $sectionId);
                }
            })
            ->orderBy('full_name_en')
            ->get();
    }

    public function getSessionPairExams(int $sessionId): Collection
    {
        return Exam::where('academic_session_id', $sessionId)
            ->whereIn('exam_category', ['tutorial', 'terminal'])
            ->whereNotNull('pair_no')
            ->orderBy('pair_no')
            ->get();
    }

    public function buildReport(int $sessionId, int $classId, ?int $sectionId = null, ?int $studentId = null): array
    {
        $students = $this->getStudents($sessionId, $classId, $sectionId, $studentId);
        $exams = $this->getSessionPairExams($sessionId);
        $pairWeights = $this->resolvePairWeights($exams);

        $examIds = $exams->pluck('id')->all();
        $studentIds = $students->pluck('id')->all();

        $marks = ExamMark::whereIn('exam_id', $examIds)
            ->whereIn('student_id', $studentIds)
            ->get();

        $pairExamMap = $exams->groupBy(function (Exam $exam) {
            return $exam->exam_category . ':' . $exam->pair_no;
        });

        $rows = [];
        foreach ($students as $student) {
            $totals = [];
            $grandTotal = 0;
            for ($pairNo = 1; $pairNo <= 3; $pairNo++) {
                $tutorialExam = $pairExamMap->get('tutorial:' . $pairNo)?->first();
                $terminalExam = $pairExamMap->get('terminal:' . $pairNo)?->first();

                $tutorialTotal = $this->studentExamTotal($student->id, $tutorialExam, $marks);
                $terminalTotal = $this->studentExamTotal($student->id, $terminalExam, $marks);

                $pairTotal = $tutorialTotal + $terminalTotal;
                $weight = $pairWeights[$pairNo] ?? 0;
                $weighted = round($pairTotal * $weight / 100, 2);

                $totals[$pairNo] = [
                    'tutorial' => $tutorialTotal,
                    'terminal' => $terminalTotal,
                    'total'    => $pairTotal,
                    'weight'   => $weight,
                    'weighted' => $weighted,
                ];

                $grandTotal += $weighted;
            }

            $rows[] = [
                'student'    => $student,
                'totals'     => $totals,
                'grand_total' => round($grandTotal, 2),
            ];
        }

        $rows = $this->assignPositions($rows);
        $highest = $this->highestGrandTotal($rows);

        return compact('rows', 'pairWeights', 'highest');
    }

    public function resolvePairWeights(Collection $exams): array
    {
        $weights = [];
        $pairGroups = $exams->groupBy('pair_no');

        foreach ($pairGroups as $pairNo => $pairExams) {
            $weights[$pairNo] = $pairExams->first()->pair_weight_percent ?? 0;
        }

        return $weights;
    }

    public function studentExamTotal(int $studentId, ?Exam $exam, Collection $marks): float
    {
        if (! $exam) {
            return 0.0;
        }

        return (float) $marks
            ->where('exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->sum('total');
    }

    public function assignPositions(array $rows): array
    {
        usort($rows, fn ($a, $b) => $b['grand_total'] <=> $a['grand_total']);

        $position = 1;
        $prevTotal = null;
        $sameCount = 0;

        foreach ($rows as &$row) {
            if ($prevTotal !== null && $row['grand_total'] === $prevTotal) {
                $row['position'] = $position - $sameCount;
                $sameCount++;
            } else {
                $row['position'] = $position;
                $sameCount = 1;
            }

            $prevTotal = $row['grand_total'];
            $position++;
        }
        unset($row);

        return $rows;
    }

    public function highestGrandTotal(array $rows): float
    {
        return collect($rows)->pluck('grand_total')->max() ?: 0.0;
    }
}
