<?php

namespace Tests\Unit;

use App\Models\Exam;
use App\Services\YearlyFinalReportService;
use Tests\TestCase;

class YearlyFinalReportServiceTest extends TestCase
{
    public function test_resolve_pair_weights_uses_first_pair_exam_weight()
    {
        $service = new YearlyFinalReportService();

        $exams = collect([
            (object) ['pair_no' => 1, 'pair_weight_percent' => 20],
            (object) ['pair_no' => 2, 'pair_weight_percent' => 20],
            (object) ['pair_no' => 3, 'pair_weight_percent' => 60],
        ]);

        $weights = $service->resolvePairWeights($exams);

        $this->assertEquals([1 => 20, 2 => 20, 3 => 60], $weights);
    }

    public function test_student_exam_total_sums_all_exam_marks_for_student()
    {
        $service = new YearlyFinalReportService();
        $exam = new Exam();
        $exam->id = 101;

        $marks = collect([
            (object) ['exam_id' => 101, 'student_id' => 1, 'total' => 50],
            (object) ['exam_id' => 101, 'student_id' => 1, 'total' => 30],
            (object) ['exam_id' => 101, 'student_id' => 2, 'total' => 70],
        ]);

        $this->assertEquals(80.0, $service->studentExamTotal(1, $exam, $marks));
        $this->assertEquals(70.0, $service->studentExamTotal(2, $exam, $marks));
        $this->assertEquals(0.0, $service->studentExamTotal(3, $exam, $marks));
    }

    public function test_assign_positions_applies_competition_rank_for_equal_totals()
    {
        $service = new YearlyFinalReportService();

        $rows = [
            ['student' => 'A', 'grand_total' => 94.0],
            ['student' => 'B', 'grand_total' => 94.0],
            ['student' => 'C', 'grand_total' => 89.0],
            ['student' => 'D', 'grand_total' => 80.0],
        ];

        $ranked = $service->assignPositions($rows);

        $this->assertEquals(1, $ranked[0]['position']);
        $this->assertEquals(1, $ranked[1]['position']);
        $this->assertEquals(3, $ranked[2]['position']);
        $this->assertEquals(4, $ranked[3]['position']);
    }
}
