<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Group;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\Subject;
use App\Models\SubjectClassAssignment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ResultMarksImportService
{
    public function run(?int $sessionId = null, bool $reset = false, bool $all = false): array
    {
        $session = $this->resolveSession($sessionId);
        $exams = $this->sessionExams($session->id);

        $cohorts = $all ? $this->cohortsForSession($session->id) : $this->cohorts();
        $summary = [];

        foreach ($cohorts as $cohort) {
            $class = SchoolClass::findOrFail($cohort['class_id']);
            $section = Section::where('school_class_id', $class->id)
                ->findOrFail($cohort['section_id']);
            $group = ! empty($cohort['group_id'])
                ? Group::findOrFail($cohort['group_id'])
                : null;

            $students = $this->studentsForCohort($session->id, $class->id, $section->id, $group?->id);

            if ($reset) {
                $this->clearExistingMarks($session->id, $students->pluck('id'), $exams->pluck('id'));
            }

            $subjectCount = 0;
            if ($students->isNotEmpty()) {
                $subjectCount = $this->subjectsForStudent(
                    $class->id,
                    $students->first()->academicInformations->first(),
                    $students->first()
                )->count();
            }

            $created = 0;
            $updated = 0;
            $skipped = 0;
            DB::transaction(function () use (
                $session,
                $cohort,
                $class,
                $students,
                $exams,
                &$created,
                &$updated,
                &$skipped
            ) {
                foreach ($exams as $exam) {
                    foreach ($students->values() as $studentIndex => $student) {
                        $academicInfo = $student->academicInformations->first();
                        $subjects = $this->subjectsForStudent($class->id, $academicInfo, $student);

                        foreach ($subjects->values() as $subjectIndex => $subject) {
                            $row = $this->buildMarkRow(
                                session: $session,
                                cohortKey: $cohort['key'],
                                classId: $class->id,
                                exam: $exam,
                                student: $student,
                                studentIndex: $studentIndex,
                                subject: $subject,
                                subjectIndex: $subjectIndex,
                            );

                            $result = $this->persistMark($exam->id, $student->id, $subject->id, $row);
                            match ($result) {
                                'created' => $created++,
                                'updated' => $updated++,
                                default   => $skipped++,
                            };
                        }
                    }
                }
            });

            $summary[] = [
                'cohort' => $cohort['label'],
                'class' => $class->name_en,
                'section' => $section->name_en,
                'group' => $group?->name_en,
                'students' => $students->count(),
                'subjects' => $subjectCount,
                'exams' => $exams->count(),
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
            ];

            Log::info('Result marks import completed for cohort.', $summary[array_key_last($summary)] ?? []);
        }

        return [
            'session' => [
                'id' => $session->id,
                'name' => $session->name_en ?? $session->name_bn ?? (string) $session->id,
            ],
            'summary' => $summary,
        ];
    }

    public function sweep(?int $sessionId = null, bool $all = false): array
    {
        $session = $this->resolveSession($sessionId);
        $exams = $this->sessionExams($session->id);
        $summary = [];

        if ($all) {
            $deleted = ExamMark::query()
                ->whereIn('exam_id', $exams->pluck('id'))
                ->delete();

            return [
                'session' => [
                    'id' => $session->id,
                    'name' => $session->name_en ?? $session->name_bn ?? (string) $session->id,
                ],
                'summary' => [[
                    'cohort' => 'All session marks',
                    'students' => 0,
                    'subjects' => 0,
                    'exams' => $exams->count(),
                    'records_deleted' => $deleted,
                ]],
            ];
        }

        foreach ($this->cohorts() as $cohort) {
            $class = isset($cohort['class_id'])
                ? SchoolClass::findOrFail($cohort['class_id'])
                : SchoolClass::where('name_en', $cohort['class'])->firstOrFail();

            $section = Section::where('school_class_id', $class->id)
                ->when(
                    isset($cohort['section_id']),
                    fn ($query) => $query->whereKey($cohort['section_id']),
                    fn ($query) => $query->where('name_en', $cohort['section'])
                )
                ->firstOrFail();

            $group = array_key_exists('group_id', $cohort) && ! empty($cohort['group_id'])
                ? Group::findOrFail($cohort['group_id'])
                : (! empty($cohort['group'])
                    ? Group::where('name_en', $cohort['group'])->firstOrFail()
                    : null);

            $cohortLabel = $cohort['label'] ?? trim(sprintf(
                'Class %s Section %s%s',
                $class->name_en,
                $section->name_en,
                $group ? ' ' . $group->name_en : ''
            ));

            $students = $this->studentsForCohort($session->id, $class->id, $section->id, $group?->id);
            $subjectCount = 0;
            if ($students->isNotEmpty()) {
                $subjectCount = $this->subjectsForStudent(
                    $class->id,
                    $students->first()->academicInformations->first(),
                    $students->first()
                )->count();
            }

            $deleted = ExamMark::query()
                ->whereIn('exam_id', $exams->pluck('id'))
                ->whereIn('student_id', $students->pluck('id'))
                ->delete();

            $summary[] = [
                'cohort' => $cohortLabel,
                'class' => $class->name_en,
                'section' => $section->name_en,
                'group' => $group?->name_en,
                'students' => $students->count(),
                'subjects' => $subjectCount,
                'exams' => $exams->count(),
                'records_deleted' => $deleted,
            ];
        }

        return [
            'session' => [
                'id' => $session->id,
                'name' => $session->name_en ?? $session->name_bn ?? (string) $session->id,
            ],
            'summary' => $summary,
        ];
    }

    private function cohorts(): array
    {
        return [
            [
                'key' => 'play',
                'label' => 'Play Class Section A',
                'class_id' => SchoolClass::where('name_en', 'Play')->value('id'),
                'section_id' => Section::where('name_en', 'A')
                    ->whereHas('schoolClass', fn ($query) => $query->where('name_en', 'Play'))
                    ->value('id'),
                'group_id' => null,
            ],
            [
                'key' => 'nine-business',
                'label' => 'Class Nine Section A Business Studies',
                'class_id' => SchoolClass::where('name_en', 'Nine')->value('id'),
                'section_id' => Section::where('name_en', 'A')
                    ->whereHas('schoolClass', fn ($query) => $query->where('name_en', 'Nine'))
                    ->value('id'),
                'group_id' => Group::where('name_en', 'Business Studies')->value('id'),
            ],
        ];
    }

    private function cohortsForSession(int $sessionId): array
    {
        return StudentAcademicInformation::query()
            ->where('academic_session_id', $sessionId)
            ->where('is_current', true)
            ->where('academic_status', 'active')
            ->with([
                'schoolClass:id,name_en',
                'section:id,school_class_id,name_en',
                'group:id,name_en',
            ])
            ->get(['id', 'school_class_id', 'section_id', 'group_id'])
            ->groupBy(fn (StudentAcademicInformation $info) => implode('|', [
                $info->school_class_id,
                $info->section_id,
                $info->group_id ?? 'null',
            ]))
            ->map(function (Collection $infos) {
                $info = $infos->first();
                $class = $info?->schoolClass;
                $section = $info?->section;
                $group = $info?->group;

                if (! $class || ! $section) {
                    return null;
                }

                $label = trim(sprintf(
                    'Class %s Section %s%s',
                    $class->name_en,
                    $section->name_en,
                    $group ? ' ' . $group->name_en : ''
                ));

                return [
                    'key' => Str::slug($label . ' ' . $class->id . ' ' . $section->id . ' ' . ($group?->id ?? 'nogroup')),
                    'label' => $label,
                    'class_id' => $class->id,
                    'section_id' => $section->id,
                    'group_id' => $group?->id,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function resolveSession(?int $sessionId = null): AcademicSession
    {
        return $sessionId
            ? AcademicSession::findOrFail($sessionId)
            : AcademicSession::orderByDesc('id')->firstOrFail();
    }

    private function sessionExams(int $sessionId): Collection
    {
        return Exam::query()
            ->where('academic_session_id', $sessionId)
            ->where('status', Exam::STATUS_PUBLISHED)
            ->whereIn('type', [Exam::TYPE_TUTORIAL, Exam::TYPE_TERMINAL])
            ->whereNotNull('pair_no')
            ->get()
            ->sortBy(fn (Exam $exam) => ($exam->pair_no * 10) + ($exam->type === Exam::TYPE_TUTORIAL ? 0 : 1))
            ->values();
    }

    private function studentsForCohort(int $sessionId, int $classId, int $sectionId, ?int $groupId = null): Collection
    {
        return Student::query()
            ->where('status', 1)
            ->whereHas('academicInformations', function ($query) use ($sessionId, $classId, $sectionId, $groupId) {
                $query->where('academic_session_id', $sessionId)
                    ->where('school_class_id', $classId)
                    ->where('section_id', $sectionId)
                    ->where('is_current', true)
                    ->where('academic_status', 'active');

                if ($groupId) {
                    $query->where('group_id', $groupId);
                }
            })
            ->with([
                'academicInformations' => fn ($query) => $query
                    ->where('academic_session_id', $sessionId)
                    ->where('school_class_id', $classId)
                    ->where('section_id', $sectionId)
                    ->with(['section', 'group']),
            ])
            ->orderBy('full_name_en')
            ->get();
    }

    private function subjectsForStudent(int $classId, ?StudentAcademicInformation $academicInfo = null, ?Student $student = null): Collection
    {
        $groupId = $academicInfo?->group_id;

        $assignments = SubjectClassAssignment::query()
            ->where('school_class_id', $classId)
            ->where('is_active', true)
            ->where(function ($query) use ($groupId) {
                $query->whereNull('group_id');
                if ($groupId) {
                    $query->orWhere('group_id', $groupId);
                }
            })
            ->with(['subject' => fn ($query) => $query->with('papers')])
            ->get();

        $subjects = collect();

        foreach ($assignments as $assignment) {
            $subject = $assignment->subject;
            if (! $subject) {
                continue;
            }

            if ($student && ! $assignment->appliesToStudent($this->studentGenderToken($student), $this->studentReligionToken($student))) {
                continue;
            }

            if ($subject->is_parent && $subject->papers->isNotEmpty()) {
                foreach ($subject->papers as $paper) {
                    $subjects->push($paper);
                }
            } else {
                $subjects->push($subject);
            }
        }

        return $subjects->unique('id')->values();
    }

    private function persistMark(int $examId, int $studentId, int $subjectId, array $payload): string
    {
        $existing = ExamMark::query()
            ->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->first();

        if (! $existing) {
            ExamMark::create(array_merge([
                'exam_id' => $examId,
                'student_id' => $studentId,
                'subject_id' => $subjectId,
            ], $payload));

            return 'created';
        }

        if ($this->rowsMatch($existing, $payload)) {
            return 'skipped';
        }

        $existing->fill($payload);
        $existing->save();

        return 'updated';
    }

    private function rowsMatch(ExamMark $existing, array $payload): bool
    {
        foreach ($payload as $key => $value) {
            $current = $existing->getAttribute($key);

            if (is_bool($value)) {
                if ((bool) $current !== $value) {
                    return false;
                }
                continue;
            }

            if (is_null($value)) {
                if (! is_null($current)) {
                    return false;
                }
                continue;
            }

            if (is_numeric($value) || is_numeric($current)) {
                if (abs((float) $current - (float) $value) > 0.0001) {
                    return false;
                }
                continue;
            }

            if ((string) $current !== (string) $value) {
                return false;
            }
        }

        return true;
    }

    private function studentGenderToken(Student $student): string
    {
        return match ((int) $student->gender) {
            Student::MALE => 'male',
            Student::FEMALE => 'female',
            default => 'all',
        };
    }

    private function studentReligionToken(Student $student): string
    {
        return match ((int) $student->religion) {
            Student::ISLAM => 'islam',
            Student::HINDU => 'hindu',
            Student::CHRISTIAN => 'christian',
            Student::BUDDHIST => 'buddhist',
            default => 'all',
        };
    }

    private function buildMarkRow(
        AcademicSession $session,
        string $cohortKey,
        int $classId,
        Exam $exam,
        Student $student,
        int $studentIndex,
        Subject $subject,
        int $subjectIndex,
    ): array {
        $isAbsent = $this->isAbsent($cohortKey, $exam, $studentIndex, $subject);
        $isTutorial = $exam->type === Exam::TYPE_TUTORIAL;
        $config = $subject->getEffectiveMarksForClass($classId);

        if ($isTutorial) {
            $fullMarks = (float) ($config['tutorial_marks'] ?? $subject->tutorial_marks ?? 20);
            $total = $isAbsent
                ? 0.0
                : $this->roundToHalf($fullMarks * $this->targetPercentage(
                    cohortKey: $cohortKey,
                    exam: $exam,
                    student: $student,
                    studentIndex: $studentIndex,
                    subject: $subject,
                    subjectIndex: $subjectIndex,
                    classId: $classId,
                    tutorial: true,
                ));

            return [
                'cq_marks' => null,
                'mcq_marks' => null,
                'practical_marks' => null,
                'viva_marks' => null,
                'tutorial_marks' => $total,
                'total' => $total,
                'is_absent' => $isAbsent,
                'letter_grade' => null,
                'gpa' => null,
            ];
        }

        $fullMarks = (float) ($config['total_marks'] ?: 100);
        $targetTotal = $isAbsent
            ? 0.0
            : $this->roundToHalf($fullMarks * $this->targetPercentage(
                cohortKey: $cohortKey,
                exam: $exam,
                student: $student,
                studentIndex: $studentIndex,
                subject: $subject,
                subjectIndex: $subjectIndex,
                classId: $classId,
                tutorial: false,
            ));

        $components = $this->splitTerminalMarks($config, $targetTotal);
        $grade = $isAbsent ? ['letter' => 'AB', 'gpa' => 0] : GradingService::getGrade($targetTotal, $fullMarks);

        return [
            'cq_marks' => $components['cq_marks'],
            'mcq_marks' => $components['mcq_marks'],
            'practical_marks' => $components['practical_marks'],
            'viva_marks' => $components['viva_marks'],
            'tutorial_marks' => null,
            'total' => $targetTotal,
            'is_absent' => $isAbsent,
            'letter_grade' => $grade['letter'] ?? null,
            'gpa' => $grade['gpa'] ?? null,
        ];
    }

    private function targetPercentage(
        string $cohortKey,
        Exam $exam,
        Student $student,
        int $studentIndex,
        Subject $subject,
        int $subjectIndex,
        int $classId,
        bool $tutorial,
    ): float {
        $base = $this->baseSkill($cohortKey, $studentIndex);
        $examBoost = $this->examBoost($exam, $tutorial);
        $subjectBoost = $this->subjectBoost($cohortKey, $subject, $subjectIndex);
        $noiseSeed = crc32($exam->id . '|' . $student->id . '|' . $subject->id . '|' . $classId . '|' . $cohortKey);
        $noise = (($noiseSeed % 9) - 4) * 0.01;

        return $this->clamp($base + $examBoost + $subjectBoost + $noise, 0.22, 0.98);
    }

    private function baseSkill(string $cohortKey, int $studentIndex): float
    {
        $profiles = [
            'play' => [0.81, 0.77, 0.74, 0.71, 0.68, 0.65, 0.62, 0.59, 0.56, 0.53, 0.50, 0.47, 0.44, 0.41, 0.38, 0.35, 0.32],
            'nine-business' => [0.83, 0.76, 0.69, 0.62, 0.55],
        ];

        $list = $profiles[$cohortKey] ?? [0.70];

        return $list[$studentIndex] ?? end($list);
    }

    private function examBoost(Exam $exam, bool $tutorial): float
    {
        $pairBoost = [
            1 => $tutorial ? 0.00 : 0.05,
            2 => $tutorial ? 0.02 : 0.07,
            3 => $tutorial ? 0.04 : 0.09,
        ];

        return $pairBoost[$exam->pair_no] ?? ($tutorial ? 0.00 : 0.05);
    }

    private function subjectBoost(string $cohortKey, Subject $subject, int $subjectIndex): float
    {
        $name = Str::lower($subject->name);

        $common = [
            'bangla 1st' => 0.02,
            'bangla 2nd' => 0.00,
            'english 1st' => 0.01,
            'english 2nd' => 0.00,
            'mathematics' => -0.05,
            'religion & quran' => 0.02,
            'bangladesh & global studies' => 0.01,
            'ict' => 0.02,
            'physical education' => 0.05,
            'general knowledge' => 0.03,
            'accounting' => -0.01,
            'finance & banking' => -0.02,
            'business entrepreneurship' => 0.00,
            'agriculture' => 0.02,
            'drawing' => 0.08,
            'spoken' => 0.05,
            'bangla' => 0.02,
            'english' => 0.01,
        ];

        $boost = 0.00;
        foreach ($common as $needle => $value) {
            if (Str::contains($name, $needle)) {
                $boost = $value;
                break;
            }
        }

        if (Str::contains($name, 'paper')) {
            if (Str::contains($name, '1st')) {
                $boost += 0.01;
            } elseif (Str::contains($name, '2nd')) {
                $boost -= 0.01;
            }
        }

        if ($cohortKey === 'play') {
            return $boost;
        }

        return $boost;
    }

    private function isAbsent(string $cohortKey, Exam $exam, int $studentIndex, Subject $subject): bool
    {
        $subjectName = Str::lower($subject->name);

        $absenceMatrix = [
            'play' => [
                'tutorial' => [
                    1 => ['drawing' => [15]],
                ],
                'term' => [
                    2 => ['mathematics' => [16]],
                ],
            ],
            'nine-business' => [
                'tutorial' => [
                    2 => ['accounting' => [4]],
                ],
                'term' => [
                    3 => ['ict' => [4]],
                ],
            ],
        ];

        $typeKey = $exam->type === Exam::TYPE_TUTORIAL ? 'tutorial' : 'term';
        $rules = $absenceMatrix[$cohortKey][$typeKey][$exam->pair_no][$subjectName] ?? [];

        return in_array($studentIndex, $rules, true);
    }

    private function splitTerminalMarks(array $config, float $targetTotal): array
    {
        $components = [
            'cq_marks' => (float) ($config['creative_marks'] ?? 0),
            'mcq_marks' => (float) ($config['mcq_marks'] ?? 0),
            'practical_marks' => (float) ($config['practical_marks'] ?? 0),
            'viva_marks' => (float) ($config['viva_marks'] ?? 0),
        ];

        $active = array_filter($components, fn ($value) => $value > 0);

        if (count($active) <= 1) {
            foreach ($components as $key => $value) {
                if ($value > 0) {
                    $components[$key] = min($this->roundToHalf($targetTotal), $value);
                }
            }

            return $components;
        }

        $totalMarks = array_sum($components);
        $allocations = [];
        $fractions = [];
        $sum = 0.0;

        foreach ($components as $key => $max) {
            if ($max <= 0) {
                $allocations[$key] = 0.0;
                $fractions[$key] = 0.0;
                continue;
            }

            $raw = $targetTotal * ($max / $totalMarks);
            $roundedDown = floor($raw * 2) / 2;
            $allocations[$key] = min($roundedDown, $max);
            $fractions[$key] = $raw - $roundedDown;
            $sum += $allocations[$key];
        }

        $remaining = $this->roundToHalf(max(0.0, $targetTotal - $sum));
        arsort($fractions);

        while ($remaining > 0.0001) {
            $updated = false;
            foreach (array_keys($fractions) as $key) {
                if ($remaining <= 0.0001) {
                    break;
                }

                if ($components[$key] <= 0) {
                    continue;
                }

                if ($allocations[$key] + 0.5 <= $components[$key]) {
                    $allocations[$key] += 0.5;
                    $remaining = round($remaining - 0.5, 2);
                    $updated = true;
                }
            }

            if (! $updated) {
                break;
            }
        }

        foreach ($allocations as $key => $value) {
            $allocations[$key] = min($this->roundToHalf($value), $components[$key]);
        }

        return $allocations;
    }

    private function clearExistingMarks(int $sessionId, Collection $studentIds, Collection $examIds): void
    {
        if ($studentIds->isEmpty() || $examIds->isEmpty()) {
            return;
        }

        ExamMark::query()
            ->whereIn('exam_id', $examIds)
            ->whereIn('student_id', $studentIds)
            ->delete();
    }

    private function roundToHalf(float $value): float
    {
        return round($value * 2) / 2;
    }

    private function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }
}
