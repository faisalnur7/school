<?php

namespace App\Services;

use App\Models\ExamMark;
use App\Models\Subject;
use Illuminate\Support\Collection;

class SubjectResultEvaluator
{
    private const COMPONENTS = [
        'cq' => ['full' => 'creative_marks', 'obtained' => 'cq_marks', 'label' => 'CQ'],
        'mcq' => ['full' => 'mcq_marks', 'obtained' => 'mcq_marks', 'label' => 'MCQ'],
        'practical' => ['full' => 'practical_marks', 'obtained' => 'practical_marks', 'label' => 'Practical'],
        'viva' => ['full' => 'viva_marks', 'obtained' => 'viva_marks', 'label' => 'Viva'],
    ];

    public function evaluatePaper(Subject $subject, array $config, ?ExamMark $mark): array
    {
        $config = $this->normalizeConfig($config, $subject);
        $components = [];
        $failedComponents = [];
        $obtained = 0.0;
        $fullMarks = (float) $config['total_marks'];
        $passMark = (float) $config['pass_mark'];
        $passRate = $fullMarks > 0 ? $passMark / $fullMarks : 0.0;

        foreach (self::COMPONENTS as $key => $definition) {
            $full = (float) $config[$definition['full']];
            $value = $mark ? (float) ($mark->{$definition['obtained']} ?? 0) : 0.0;
            $componentPassMark = round($full * $passRate, 2);
            $enabled = $full > 0;
            $passed = ! $enabled || ($mark && ! $mark->is_absent && $value >= $componentPassMark);

            if ($enabled) {
                $obtained += $value;
                if (! $passed) {
                    $failedComponents[] = $key;
                }
            }

            $components[$key] = [
                'label' => $definition['label'],
                'full_marks' => $full,
                'obtained' => $value,
                'pass_mark' => $componentPassMark,
                'enabled' => $enabled,
                'passed' => (bool) $passed,
            ];
        }

        $obtained = round($obtained, 2);
        $isAbsent = (bool) ($mark?->is_absent ?? false);
        $individualPass = empty($failedComponents);
        $totalPass = ! $isAbsent && $obtained >= $passMark;
        $passed = $config['component_pass_mode'] === 'individual'
            ? $individualPass && $totalPass
            : $totalPass;

        if ($isAbsent) {
            $passed = false;
        }

        $grade = GradingService::getGrade($obtained, $fullMarks);

        return [
            'subject' => $subject,
            'obtained' => $obtained,
            'full_marks' => $fullMarks,
            'pass_mark' => $passMark,
            'component_pass_mode' => $config['component_pass_mode'],
            'components' => $components,
            'failed_components' => $failedComponents,
            'component_passed' => $individualPass && ! $isAbsent,
            'is_absent' => $isAbsent,
            'passed' => $passed,
            'letter_grade' => $isAbsent ? 'AB' : ($passed ? $grade['letter'] : 'F'),
            'gpa' => $isAbsent || ! $passed ? 0.0 : $grade['gpa'],
        ];
    }

    public function evaluateSubject(Subject $subject, array $config, Collection $papers, Collection $marksBySubjectId): array
    {
        if ($papers->isEmpty()) {
            return $this->evaluatePaper($subject, $config, $marksBySubjectId->get($subject->id));
        }

        $paperResults = $papers->map(function (Subject $paper) use ($config, $marksBySubjectId): array {
            $classId = isset($config['class_id']) ? (int) $config['class_id'] : null;
            $paperConfig = $paper->getEffectiveMarksForClass($classId);
            $paperConfig['component_pass_mode'] = 'individual';

            return $this->evaluatePaper($paper, $paperConfig, $marksBySubjectId->get($paper->id));
        })->values();

        $obtained = round($paperResults->sum('obtained'), 2);
        $fullMarks = (float) ($config['total_marks'] ?? $paperResults->sum('full_marks'));
        $passMark = (float) ($config['pass_mark'] ?? 0);
        $isAbsent = $paperResults->contains(fn (array $paper) => $paper['is_absent']);
        $componentsPassed = $paperResults->every(fn (array $paper) => $paper['component_passed']);
        $totalPassed = ! $isAbsent && $obtained >= $passMark;
        $passed = $componentsPassed && $totalPassed;
        $grade = GradingService::getGrade($obtained, $fullMarks);

        return [
            'subject' => $subject,
            'obtained' => $obtained,
            'full_marks' => $fullMarks,
            'pass_mark' => $passMark,
            'component_pass_mode' => 'individual',
            'components' => $this->aggregateComponents($paperResults),
            'failed_components' => $paperResults->flatMap(fn (array $paper) => $paper['failed_components'])->unique()->values()->all(),
            'component_passed' => $componentsPassed,
            'is_absent' => $isAbsent,
            'passed' => $passed,
            'letter_grade' => $isAbsent ? 'AB' : ($passed ? $grade['letter'] : 'F'),
            'gpa' => $isAbsent || ! $passed ? 0.0 : $grade['gpa'],
            'papers' => $paperResults->all(),
        ];
    }

    private function normalizeConfig(array $config, Subject $subject): array
    {
        $values = [];
        foreach (self::COMPONENTS as $definition) {
            $values[$definition['full']] = (float) ($config[$definition['full']] ?? $subject->{$definition['full']} ?? 0);
        }

        $values['total_marks'] = (float) ($config['total_marks'] ?? array_sum($values));
        $values['pass_mark'] = (float) ($config['pass_mark'] ?? $subject->pass_mark ?? 0);
        $values['component_pass_mode'] = ($config['component_pass_mode'] ?? 'combined') === 'individual' ? 'individual' : 'combined';

        return $values;
    }

    private function aggregateComponents(Collection $paperResults): array
    {
        return $paperResults
            ->flatMap(fn (array $paper) => $paper['components'])
            ->groupBy('label')
            ->map(fn (Collection $components) => [
                'label' => $components->first()['label'],
                'full_marks' => round($components->sum('full_marks'), 2),
                'obtained' => round($components->sum('obtained'), 2),
                'pass_mark' => round($components->sum('pass_mark'), 2),
                'enabled' => $components->contains(fn (array $component) => $component['enabled']),
                'passed' => $components->every(fn (array $component) => ! $component['enabled'] || $component['passed']),
            ])
            ->values()
            ->all();
    }
}
