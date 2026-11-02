<?php

namespace Tests\Unit;

use App\Models\ExamMark;
use App\Models\Subject;
use App\Services\SubjectResultEvaluator;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class SubjectResultEvaluatorTest extends TestCase
{
    private SubjectResultEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluator = new SubjectResultEvaluator();
    }

    public function test_combined_single_subject_passes_when_total_reaches_pass_mark(): void
    {
        $result = $this->evaluator->evaluatePaper(
            new Subject(),
            $this->config('combined'),
            $this->mark(cq: 10, mcq: 23, practical: 0),
        );

        $this->assertTrue($result['passed']);
        $this->assertSame('D', $result['letter_grade']);
        $this->assertSame(1.0, $result['gpa']);
    }

    public function test_individual_single_subject_requires_each_enabled_component(): void
    {
        $result = $this->evaluator->evaluatePaper(
            new Subject(),
            $this->config('individual'),
            $this->mark(cq: 16.5, mcq: 8.25, practical: 8.25),
        );

        $this->assertTrue($result['passed']);
        $this->assertSame(16.5, $result['components']['cq']['pass_mark']);
        $this->assertSame(8.25, $result['components']['mcq']['pass_mark']);
        $this->assertSame(8.25, $result['components']['practical']['pass_mark']);
    }

    public function test_zero_full_mark_components_are_ignored(): void
    {
        $result = $this->evaluator->evaluatePaper(
            new Subject(),
            ['creative_marks' => 100, 'mcq_marks' => 0, 'practical_marks' => 0, 'viva_marks' => 0, 'total_marks' => 100, 'pass_mark' => 33, 'component_pass_mode' => 'individual'],
            $this->mark(cq: 33, mcq: 0, practical: 0),
        );

        $this->assertTrue($result['passed']);
        $this->assertSame([], $result['failed_components']);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('paperFailureProvider')]
    public function test_any_failed_component_in_any_paper_fails_the_individual_subject(string $failedPaper, string $failedComponent): void
    {
        $papers = collect([
            $this->paper(1),
            $this->paper(2),
        ]);
        $marks = collect([
            1 => $this->mark(cq: $failedPaper === 'first' && $failedComponent === 'cq' ? 22 : 30, mcq: $failedPaper === 'first' && $failedComponent === 'mcq' ? 9 : 20),
            2 => $this->mark(cq: $failedPaper === 'second' && $failedComponent === 'cq' ? 22 : 30, mcq: $failedPaper === 'second' && $failedComponent === 'mcq' ? 9 : 20),
        ]);

        $result = $this->evaluator->evaluateSubject(
            new Subject(),
            ['creative_marks' => 140, 'mcq_marks' => 60, 'practical_marks' => 0, 'viva_marks' => 0, 'total_marks' => 200, 'pass_mark' => 66, 'component_pass_mode' => 'individual'],
            $papers,
            $marks,
        );

        $this->assertFalse($result['passed']);
        $this->assertSame('F', $result['letter_grade']);
        $this->assertSame(0.0, $result['gpa']);
    }

    public function test_combined_multi_paper_mode_uses_aggregate_total_only(): void
    {
        $result = $this->evaluator->evaluateSubject(
            new Subject(['is_parent' => true]),
            ['creative_marks' => 200, 'mcq_marks' => 0, 'practical_marks' => 0, 'viva_marks' => 0, 'total_marks' => 200, 'pass_mark' => 66, 'component_pass_mode' => 'combined'],
            collect([$this->paper(1), $this->paper(2)]),
            collect([1 => $this->mark(cq: 30, mcq: 0), 2 => $this->mark(cq: 40, mcq: 0)]),
        );

        $this->assertTrue($result['passed']);
        $this->assertSame(70.0, $result['obtained']);
    }

    public static function paperFailureProvider(): array
    {
        return [
            'first paper mcq' => ['first', 'mcq'],
            'first paper written' => ['first', 'cq'],
            'second paper mcq' => ['second', 'mcq'],
            'second paper written' => ['second', 'cq'],
        ];
    }

    public function test_all_paper_components_pass_but_combined_total_below_parent_threshold_fails(): void
    {
        $result = $this->evaluator->evaluateSubject(
            new Subject(),
            ['creative_marks' => 140, 'mcq_marks' => 60, 'practical_marks' => 0, 'viva_marks' => 0, 'total_marks' => 200, 'pass_mark' => 101, 'component_pass_mode' => 'combined'],
            collect([$this->paper(1), $this->paper(2)]),
            collect([1 => $this->mark(cq: 35, mcq: 15), 2 => $this->mark(cq: 35, mcq: 15)]),
        );

        $this->assertFalse($result['passed']);
        $this->assertSame(100.0, $result['obtained']);
        $this->assertSame('F', $result['letter_grade']);
    }

    public function test_all_paper_components_pass_and_combined_total_reaches_a_plus(): void
    {
        $result = $this->evaluator->evaluateSubject(
            new Subject(),
            ['creative_marks' => 140, 'mcq_marks' => 60, 'practical_marks' => 0, 'viva_marks' => 0, 'total_marks' => 200, 'pass_mark' => 66, 'component_pass_mode' => 'combined'],
            collect([$this->paper(1), $this->paper(2)]),
            collect([1 => $this->mark(cq: 70, mcq: 30), 2 => $this->mark(cq: 70, mcq: 30)]),
        );

        $this->assertTrue($result['passed']);
        $this->assertSame(200.0, $result['obtained']);
        $this->assertSame('A+', $result['letter_grade']);
        $this->assertSame(5.0, $result['gpa']);
    }

    public function test_missing_or_absent_marks_fail_safely(): void
    {
        $missing = $this->evaluator->evaluatePaper(new Subject(), $this->config('combined'), null);
        $absent = $this->evaluator->evaluatePaper(new Subject(), $this->config('combined'), $this->mark(cq: 0, mcq: 0, practical: 0, absent: true));

        $this->assertFalse($missing['passed']);
        $this->assertFalse($absent['passed']);
        $this->assertSame('AB', $absent['letter_grade']);
        $this->assertSame(0.0, $absent['gpa']);
    }

    private function config(string $mode): array
    {
        return ['creative_marks' => 50, 'mcq_marks' => 25, 'practical_marks' => 25, 'viva_marks' => 0, 'total_marks' => 100, 'pass_mark' => 33, 'component_pass_mode' => $mode];
    }

    private function paper(int $id): Subject
    {
        $paper = new Subject(['creative_marks' => 70, 'mcq_marks' => 30, 'practical_marks' => 0, 'viva_marks' => 0, 'total_marks' => 100, 'pass_mark' => 33]);
        $paper->id = $id;

        return $paper;
    }

    private function mark(float $cq, float $mcq, float $practical = 0, bool $absent = false): ExamMark
    {
        return new ExamMark(['cq_marks' => $cq, 'mcq_marks' => $mcq, 'practical_marks' => $practical, 'viva_marks' => 0, 'total' => $cq + $mcq + $practical, 'is_absent' => $absent]);
    }
}
