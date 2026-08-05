<?php

namespace App\Http\Controllers;

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
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with(['academicSession'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('session_id')) {
            $query->where('academic_session_id', $request->session_id);
        }

        $exams    = $query->paginate(15)->withQueryString();
        $sessions = AcademicSession::orderByDesc('id')->get();

        return view('pages.exams.index', compact('exams', 'sessions'));
    }

    public function create()
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        return view('pages.exams.create', compact('sessions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                => 'required|string|max:255',
            'exam_category'       => 'required|in:tutorial,terminal',
            'type'                => 'nullable|in:term,tutorial',
            'pair_no'             => 'required|integer|between:1,3',
            'pair_weight_percent' => 'required|integer|min:0|max:100',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'year'                => 'required|digits:4',
            'start_date'          => 'nullable|date',
            'end_date'            => 'nullable|date|after_or_equal:start_date',
            'status'              => 'in:draft,published',
        ]);

        $data['type'] = $data['exam_category'] === 'terminal'
            ? Exam::TYPE_TERMINAL
            : Exam::TYPE_TUTORIAL;

        $this->validateExamPairConfiguration($data);

        $exam = Exam::create($data);

        return redirect()->route('exams.show', $exam)->with('success', 'Exam created successfully.');
    }

    public function show(Exam $exam)
    {
        $exam->load(['academicSession']);

        // All active classes
        $classes = SchoolClass::where('status', 1)->orderBy('id')->get();

        // Count total students across all classes for this session
        $studentCount = Student::where('status', 1)
            ->when($exam->academic_session_id, fn($q) => $q->whereHas('academicInformations',
                fn($q2) => $q2->where('academic_session_id', $exam->academic_session_id)
            ))
            ->count();

        return view('pages.exams.show', compact('exam', 'classes', 'studentCount'));
    }

    public function edit(Exam $exam)
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        return view('pages.exams.edit', compact('exam', 'sessions'));
    }

    public function update(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'name'                => 'required|string|max:255',
            'exam_category'       => 'required|in:tutorial,terminal',
            'type'                => 'nullable|in:term,tutorial',
            'pair_no'             => 'required|integer|between:1,3',
            'pair_weight_percent' => 'required|integer|min:0|max:100',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'year'                => 'required|digits:4',
            'start_date'          => 'nullable|date',
            'end_date'            => 'nullable|date|after_or_equal:start_date',
            'status'              => 'in:draft,published',
        ]);

        $data['type'] = $data['exam_category'] === 'terminal'
            ? Exam::TYPE_TERMINAL
            : Exam::TYPE_TUTORIAL;

        $this->validateExamPairConfiguration($data, $exam);

        $exam->update($data);

        return redirect()->route('exams.show', $exam)->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam)
    {
        $exam->marks()->delete();
        $exam->examSubjects()->delete();
        $exam->delete();

        return redirect()->route('exams.index')->with('success', 'Exam deleted.');
    }

    private function validateExamPairConfiguration(array $data, ?Exam $exam = null): void
    {
        $pairQuery = Exam::where('academic_session_id', $data['academic_session_id'])
            ->where('exam_category', $data['exam_category'])
            ->where('pair_no', $data['pair_no']);

        if ($exam) {
            $pairQuery->where('id', '<>', $exam->id);
        }

        if ($pairQuery->exists()) {
            abort(422, 'This pair index is already used for the selected session and exam category.');
        }

        $weights = Exam::where('academic_session_id', $data['academic_session_id'])
            ->where('exam_category', $data['exam_category'])
            ->whereNotNull('pair_no')
            ->pluck('pair_weight_percent', 'pair_no')
            ->toArray();

        $weights[$data['pair_no']] = $data['pair_weight_percent'];

        if (count(array_filter($weights, fn($weight) => $weight !== null)) === 3 && array_sum($weights) !== 100) {
            abort(422, 'The configured pair weights for this session and category must sum to 100%.');
        }
    }

    /**
     * Marks entry: pick class -> section -> group -> subject -> enter marks
     */
    public function marksEntry(Request $request, Exam $exam)
    {
        $classId = $request->integer('class_id') ?: null;
        $sectionId = $request->integer('section_id') ?: null;
        $groupId = $request->integer('group_id') ?: null;
        $subjectId = $request->integer('subject_id') ?: null;

        $classes = SchoolClass::where('status', 1)->orderBy('id')->get();
        $sections = collect();
        $groups = collect();
        $subjects = collect();
        $subject = null;
        $students = collect();
        $existingMarks = [];
        $subjectConfig = null;
        $selectedClass = null;
        $selectedSection = null;
        $selectedGroup = null;
        $cohortReady = false;

        if ($classId) {
            $selectedClass = SchoolClass::find($classId);

            if ($selectedClass) {
                $sections = $this->getSectionsForClass($classId);
                $selectedSection = $sectionId ? $sections->firstWhere('id', $sectionId) : null;
                $groups = $selectedSection ? $this->getGroupsForClassAndSection($exam, $classId, $selectedSection->id) : collect();
                $selectedGroup = $groupId ? $groups->firstWhere('id', $groupId) : null;

                $cohortReady = true;
                if ($sections->isNotEmpty() && ! $selectedSection) {
                    $cohortReady = false;
                } elseif ($groups->isNotEmpty() && ! $selectedGroup) {
                    $cohortReady = false;
                }

                if ($cohortReady) {
                    $subjects = $this->getSubjectsForClass($classId, $selectedGroup?->id);

                    if (! $subjectId && $subjects->isNotEmpty()) {
                        $subjectId = $subjects->first()->id;
                    }

                    $subject = $subjectId ? $subjects->firstWhere('id', $subjectId) : null;
                    $students = $this->getStudentsForClass($exam, $classId, $sectionId, $groupId, $subjectId);

                    if ($subject) {
                        ExamMark::where('exam_id', $exam->id)
                            ->where('subject_id', $subject->id)
                            ->whereIn('student_id', $students->pluck('id'))
                            ->get()
                            ->each(function ($mark) use (&$existingMarks) {
                                $existingMarks[$mark->student_id] = $mark;
                            });

                        $subjectConfig = $subject->getEffectiveMarksForClass($classId);
                    }
                }
            }
        }

        return view('pages.exams.marks-entry', compact(
            'exam', 'classes', 'selectedClass', 'sections', 'groups', 'selectedSection',
            'selectedGroup', 'subjects', 'subject', 'students', 'existingMarks',
            'subjectConfig', 'classId', 'sectionId', 'groupId', 'subjectId', 'cohortReady'
        ));
    }

    /**
     * Save marks for a class + subject
     */
    public function saveMarks(Request $request, Exam $exam)
    {
        $request->validate([
            'class_id'           => 'required|exists:school_classes,id',
            'section_id'         => 'nullable|exists:sections,id',
            'group_id'           => 'nullable|exists:groups,id',
            'subject_id'         => 'required|exists:subjects,id',
            'marks'              => 'array',
            'marks.*.student_id' => 'required|exists:students,id',
        ]);

        $classId = (int) $request->class_id;
        $sectionId = $request->filled('section_id') ? (int) $request->section_id : null;
        $groupId = $request->filled('group_id') ? (int) $request->group_id : null;
        $subjectId = (int) $request->subject_id;
        $subject = Subject::findOrFail($subjectId);
        $config = $subject->getEffectiveMarksForClass($classId);
        $isTutorial = $exam->type === Exam::TYPE_TUTORIAL;
        $submittedStudentIds = collect($request->input('marks', []))
            ->pluck('student_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
        $allowedStudentIds = $this->getStudentsForClass($exam, $classId, $sectionId, $groupId, $subjectId)
            ->pluck('id')
            ->all();

        abort_unless(empty(array_diff($submittedStudentIds, $allowedStudentIds)), 422, 'One or more submitted marks do not belong to the selected class/section/group.');

        DB::transaction(function () use ($request, $exam, $subject, $subjectId, $classId, $config, $isTutorial) {
            foreach ($request->marks as $row) {
                $studentId = $row['student_id'];
                $isAbsent  = ! empty($row['is_absent']);

                $cq = null;
                $mcq = null;
                $practical = null;
                $viva = null;
                $tutorial = null;

                if ($isTutorial) {
                    $tutorial  = $isAbsent ? 0 : (float) ($row['tutorial_marks'] ?? 0);
                    $total     = $tutorial;
                    $fullMarks = (float) ($config['tutorial_marks'] ?? $subject->tutorial_marks ?? 0);
                } else {
                    $cq        = $isAbsent ? 0 : (float) ($row['cq_marks'] ?? 0);
                    $mcq       = $isAbsent ? 0 : (float) ($row['mcq_marks'] ?? 0);
                    $practical = $isAbsent ? 0 : (float) ($row['practical_marks'] ?? 0);
                    $viva      = $isAbsent ? 0 : (float) ($row['viva_marks'] ?? 0);
                    $total     = $cq + $mcq + $practical + $viva;
                    $fullMarks = (float) ($config['total_marks'] ?: 100);
                }

                $grade = (!$isTutorial && $fullMarks > 0)
                    ? GradingService::getGrade($total, $fullMarks)
                    : null;

                ExamMark::updateOrCreate(
                    ['exam_id' => $exam->id, 'student_id' => $studentId, 'subject_id' => $subjectId],
                    [
                        'cq_marks'        => $cq,
                        'mcq_marks'       => $mcq,
                        'practical_marks' => $practical,
                        'viva_marks'      => $viva,
                        'tutorial_marks'  => $tutorial,
                        'total'           => $total,
                        'is_absent'       => $isAbsent,
                        'letter_grade'    => $isAbsent ? 'AB' : ($grade['letter'] ?? null),
                        'gpa'             => $isAbsent ? 0 : ($grade['gpa'] ?? null),
                    ]
                );
            }
        });

        return redirect()->route('exams.marks-entry', [
            'exam'       => $exam->id,
            'class_id'   => $classId,
            'section_id' => $sectionId,
            'group_id'   => $groupId,
            'subject_id' => $subjectId,
        ])->with('success', 'Marks saved successfully.');
    }

    /**
     * Preview: marks for a class + subject with filters
     */
    public function preview(Request $request, Exam $exam)
    {
        $classId = $request->integer('class_id') ?: null;
        $sectionId = $request->integer('section_id') ?: null;
        $groupId = $request->integer('group_id') ?: null;
        $subjectId = $request->integer('subject_id') ?: null;
        $filter = $request->filter ?? 'all';

        $classes = SchoolClass::where('status', 1)->orderBy('id')->get();
        $sections = collect();
        $groups = collect();
        $subjects = collect();
        $subject = null;
        $marks = collect();
        $passMark = 33;
        $selectedClass = null;
        $selectedSection = null;
        $selectedGroup = null;

        if ($classId) {
            $selectedClass = SchoolClass::find($classId);

            if ($selectedClass) {
                $sections = $this->getSectionsForClass($classId);
                $selectedSection = $sectionId ? $sections->firstWhere('id', $sectionId) : null;
                $groups = $selectedSection ? $this->getGroupsForClassAndSection($exam, $classId, $selectedSection->id) : collect();
                $selectedGroup = $groupId ? $groups->firstWhere('id', $groupId) : null;

                if (($sections->isEmpty() || $selectedSection) && ($groups->isEmpty() || $selectedGroup)) {
                    $subjects = $this->getSubjectsForClass($classId, $selectedGroup?->id);

                    if (! $subjectId && $subjects->isNotEmpty()) {
                        $subjectId = $subjects->first()->id;
                    }

                    $subject = $subjectId ? $subjects->firstWhere('id', $subjectId) : null;

                    if ($subject) {
                        $config = $subject->getEffectiveMarksForClass($classId);
                        $passMark = (float) ($config['pass_mark'] ?? 33);

                        $studentIds = $this->getStudentsForClass($exam, $classId, $sectionId, $groupId, $subjectId)->pluck('id');

                        $marks = ExamMark::where('exam_id', $exam->id)
                            ->where('subject_id', $subjectId)
                            ->whereIn('student_id', $studentIds)
                            ->with('student')
                            ->get();

                        if ($exam->type === Exam::TYPE_TUTORIAL) {
                            $passMark = 0;
                            $marks = match ($filter) {
                                'highest' => $marks->sortByDesc('total'),
                                default   => $marks->sortBy(fn($m) => $m->student->full_name_en),
                            };
                        } else {
                            $marks = match ($filter) {
                                'passed'  => $marks->filter(fn($m) => ! $m->is_absent && $m->total >= $passMark),
                                'failed'  => $marks->filter(fn($m) => $m->is_absent || $m->total < $passMark),
                                'highest' => $marks->sortByDesc('total'),
                                default   => $marks->sortBy(fn($m) => $m->student->full_name_en),
                            };
                        }
                    }
                }
            }
        }

        return view('pages.exams.preview', compact(
            'exam', 'classes', 'selectedClass', 'sections', 'groups', 'selectedSection', 'selectedGroup',
            'subjects', 'subject', 'marks', 'filter', 'passMark', 'classId', 'sectionId', 'groupId', 'subjectId'
        ));
    }

    /**
     * Terminal result: all classes, all subjects, grading + ranking per class
     */
    public function terminalResult(Request $request, Exam $exam)
    {
        $exam->load(['academicSession']);

        $classId = $request->integer('class_id') ?: null;
        $sectionId = $request->integer('section_id') ?: null;
        $groupId = $request->integer('group_id') ?: null;
        $subjectId = null;
        $filter = $request->filter ?? 'all';
        $classes = SchoolClass::where('status', 1)->orderBy('id')->get();
        $selectedClass = $classId ? SchoolClass::find($classId) : null;
        $sections = collect();
        $groups = collect();
        $selectedSection = null;
        $selectedGroup = null;

        $results = [];
        $subjects = collect();

        if ($classId) {
            $sections = $this->getSectionsForClass($classId);
            $selectedSection = $sectionId ? $sections->firstWhere('id', $sectionId) : null;
            $groups = $selectedSection ? $this->getGroupsForClassAndSection($exam, $classId, $selectedSection->id) : collect();
            $selectedGroup = $groupId ? $groups->firstWhere('id', $groupId) : null;
            $subjects = $this->getSubjectsForClass($classId, $selectedGroup?->id);

            $students = $this->getStudentsForClass($exam, $classId, $sectionId, $groupId, $subjectId);
            $studentIds = $students->pluck('id');

            $allMarks = ExamMark::where('exam_id', $exam->id)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->groupBy('student_id');

            foreach ($students as $student) {
                $studentMarks = $allMarks->get($student->id, collect());
                $subjectResults = [];
                $totalObtained = 0;
                $totalFull = 0;
                $gpas = [];
                $failedSubjectCount = 0;

                foreach ($subjects as $subject) {
                    $config = $subject->getEffectiveMarksForClass($classId);
                    $fullMarks = (float) ($config['total_marks'] ?: 100);
                    $passMark = (float) ($config['pass_mark'] ?? 33);
                    $mark = $studentMarks->firstWhere('subject_id', $subject->id);
                    $obtained = $mark ? (float) $mark->total : 0;
                    $isAbsent = $mark ? $mark->is_absent : false;
                    $grade = GradingService::getGrade($obtained, $fullMarks);
                    $passed = ! $isAbsent && $obtained >= $passMark;

                    if (! $passed) {
                        $failedSubjectCount++;
                    }

                    $subjectResults[$subject->id] = [
                        'obtained'     => $obtained,
                        'full_marks'   => $fullMarks,
                        'pass_mark'    => $passMark,
                        'letter_grade' => $isAbsent ? 'AB' : $grade['letter'],
                        'gpa'          => $isAbsent ? 0 : $grade['gpa'],
                        'is_absent'    => $isAbsent,
                        'passed'       => $passed,
                    ];

                    $totalObtained += $obtained;
                    $totalFull += $fullMarks;
                    $gpas[] = $isAbsent ? 0 : $grade['gpa'];
                }

                $avgGpa = count($gpas) > 0 ? round(array_sum($gpas) / count($gpas), 2) : 0;

                $results[$student->id] = [
                    'student'         => $student,
                    'subject_results' => $subjectResults,
                    'total_obtained'  => $totalObtained,
                    'total_full'      => $totalFull,
                    'percentage'      => $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0,
                    'gpa'             => $avgGpa,
                    'gpa_label'       => GradingService::getGpaLabel($avgGpa),
                    'failed_subject_count' => $failedSubjectCount,
                    'has_failed'      => $failedSubjectCount > 0,
                    'status'          => $failedSubjectCount > 0 ? 'Failed' : 'Passed',
                ];
            }

            // Merit order: all-passed students first, then 1 failed subject, 2 failed subjects, etc.
            uasort($results, function ($a, $b) {
                $failedCompare = ($a['failed_subject_count'] ?? 0) <=> ($b['failed_subject_count'] ?? 0);
                if ($failedCompare !== 0) {
                    return $failedCompare;
                }

                $totalCompare = ($b['total_obtained'] ?? 0) <=> ($a['total_obtained'] ?? 0);
                if ($totalCompare !== 0) {
                    return $totalCompare;
                }

                return ($b['percentage'] ?? 0) <=> ($a['percentage'] ?? 0);
            });
            $rank = 1;
            $prevFailedCount = null;
            $prevTotal = null;
            foreach ($results as &$row) {
                if (
                    $prevFailedCount !== null
                    && (int) $row['failed_subject_count'] === (int) $prevFailedCount
                    && (float) $row['total_obtained'] === (float) $prevTotal
                ) {
                    $row['rank'] = $rank - 1;
                } else {
                    $row['rank'] = $rank;
                }
                $prevFailedCount = $row['failed_subject_count'];
                $prevTotal = $row['total_obtained'];
                $rank++;
            }
            unset($row);
        }

        $displayResults = match ($filter) {
            'passed' => array_filter($results, fn($r) => ! $r['has_failed']),
            'failed' => array_filter($results, fn($r) => $r['has_failed']),
            default  => $results,
        };

        return view('pages.exams.terminal-result', compact(
            'exam', 'classes', 'selectedClass', 'sections', 'groups', 'selectedSection', 'selectedGroup',
            'subjects', 'displayResults', 'results', 'filter', 'classId', 'sectionId', 'groupId'
        ));
    }

    /**
     * PDF: subject marks preview
     */
    public function previewPdf(Request $request, Exam $exam)
    {
        $classId = $request->integer('class_id') ?: null;
        $sectionId = $request->integer('section_id') ?: null;
        $groupId = $request->integer('group_id') ?: null;
        $subjectId = $request->integer('subject_id') ?: null;
        $subject = Subject::find($subjectId);
        $config = $subject ? $subject->getEffectiveMarksForClass($classId) : [];
        $passMark = (float) ($config['pass_mark'] ?? 33);

        $studentIds = $this->getStudentsForClass($exam, $classId, $sectionId, $groupId)->pluck('id');

        $marks = ExamMark::where('exam_id', $exam->id)
            ->where('subject_id', $subjectId)
            ->whereIn('student_id', $studentIds)
            ->with('student')
            ->get()
            ->sortByDesc('total');

        $selectedClass = SchoolClass::find($classId);

        $mpdf = new \Mpdf\Mpdf(['margin_top' => 15, 'margin_bottom' => 15]);
        $html = view('pages.exams.pdf.preview', compact('exam', 'subject', 'marks', 'passMark', 'selectedClass'))->render();
        $mpdf->WriteHTML($html);
        $mpdf->Output('marks_preview.pdf', 'D');
        exit;
    }

    /**
     * PDF: terminal result
     */
    public function terminalResultPdf(Request $request, Exam $exam)
    {
        $classId  = $request->integer('class_id') ?: null;
        $sectionId = $request->integer('section_id') ?: null;
        $groupId = $request->integer('group_id') ?: null;
        $subjectId = null;
        $exam->load(['academicSession']);

        $selectedClass = SchoolClass::find($classId);

        $sections = $this->getSectionsForClass($classId);
        $selectedSection = $sectionId ? $sections->firstWhere('id', $sectionId) : null;
        $groups = $selectedSection ? $this->getGroupsForClassAndSection($exam, $classId, $selectedSection->id) : collect();
        $selectedGroup = $groupId ? $groups->firstWhere('id', $groupId) : null;
        $subjects = $this->getSubjectsForClass($classId, $selectedGroup?->id);

        $students   = $this->getStudentsForClass($exam, $classId, $sectionId, $groupId, $subjectId);
        $studentIds = $students->pluck('id');

        $allMarks = ExamMark::where('exam_id', $exam->id)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->groupBy('student_id');

        $results = [];
        foreach ($students as $student) {
            $studentMarks   = $allMarks->get($student->id, collect());
            $subjectResults = [];
            $totalObtained  = 0;
            $totalFull      = 0;
            $gpas           = [];
            $failedSubjectCount = 0;

            foreach ($subjects as $subject) {
                $config    = $subject->getEffectiveMarksForClass($classId);
                $fullMarks = (float) ($config['total_marks'] ?: 100);
                $passMark  = (float) ($config['pass_mark'] ?? 33);
                $mark      = $studentMarks->firstWhere('subject_id', $subject->id);
                $obtained  = $mark ? (float) $mark->total : 0;
                $isAbsent  = $mark ? $mark->is_absent : false;
                $grade     = GradingService::getGrade($obtained, $fullMarks);
                $passed    = ! $isAbsent && $obtained >= $passMark;

                if (! $passed) {
                    $failedSubjectCount++;
                }

                $subjectResults[$subject->id] = [
                    'obtained'     => $obtained,
                    'full_marks'   => $fullMarks,
                    'letter_grade' => $isAbsent ? 'AB' : $grade['letter'],
                    'gpa'          => $isAbsent ? 0 : $grade['gpa'],
                    'is_absent'    => $isAbsent,
                    'passed'       => $passed,
                ];

                $totalObtained += $obtained;
                $totalFull     += $fullMarks;
                $gpas[]         = $isAbsent ? 0 : $grade['gpa'];
            }

            $avgGpa = count($gpas) > 0 ? round(array_sum($gpas) / count($gpas), 2) : 0;
            $results[$student->id] = [
                'student'         => $student,
                'subject_results' => $subjectResults,
                'total_obtained'  => $totalObtained,
                'total_full'      => $totalFull,
                'percentage'      => $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0,
                'gpa'             => $avgGpa,
                'gpa_label'       => GradingService::getGpaLabel($avgGpa),
                'failed_subject_count' => $failedSubjectCount,
                'has_failed'      => $failedSubjectCount > 0,
                'status'          => $failedSubjectCount > 0 ? 'Failed' : 'Passed',
            ];
        }

        uasort($results, function ($a, $b) {
            $failedCompare = ($a['failed_subject_count'] ?? 0) <=> ($b['failed_subject_count'] ?? 0);
            if ($failedCompare !== 0) {
                return $failedCompare;
            }

            $totalCompare = ($b['total_obtained'] ?? 0) <=> ($a['total_obtained'] ?? 0);
            if ($totalCompare !== 0) {
                return $totalCompare;
            }

            return ($b['percentage'] ?? 0) <=> ($a['percentage'] ?? 0);
        });
        $rank = 1;
        $prevFailedCount = null;
        $prevTotal = null;
        foreach ($results as &$row) {
            if (
                $prevFailedCount !== null
                && (int) $row['failed_subject_count'] === (int) $prevFailedCount
                && (float) $row['total_obtained'] === (float) $prevTotal
            ) {
                $row['rank'] = $rank - 1;
            } else {
                $row['rank'] = $rank;
            }
            $prevFailedCount = $row['failed_subject_count'];
            $prevTotal = $row['total_obtained'];
            $rank++;
        }
        unset($row);

        $mpdf = new \Mpdf\Mpdf(['orientation' => 'L', 'margin_top' => 15, 'margin_bottom' => 15]);
        $html = view('pages.exams.pdf.terminal-result', compact('exam', 'subjects', 'results', 'selectedClass'))->render();
        $mpdf->WriteHTML($html);
        $mpdf->Output('terminal_result.pdf', 'D');
        exit;
    }

    /**
     * AJAX: sections by class (kept for other uses)
     */
    public function getSectionsByClass(Request $request)
    {
        $sections = Section::where('school_class_id', $request->class_id)->get(['id', 'name_en']);
        return response()->json($sections);
    }

    private function getSectionsForClass(int $classId): \Illuminate\Support\Collection
    {
        return Section::where('school_class_id', $classId)
            ->orderBy('name_en')
            ->get(['id', 'school_class_id', 'name_en']);
    }

    private function getGroupsForClassAndSection(Exam $exam, int $classId, int $sectionId): \Illuminate\Support\Collection
    {
        $groupIds = StudentAcademicInformation::query()
            ->where('school_class_id', $classId)
            ->where('section_id', $sectionId)
            ->when($exam->academic_session_id, fn ($query) => $query->where('academic_session_id', $exam->academic_session_id))
            ->where('is_current', true)
            ->whereNotNull('group_id')
            ->distinct()
            ->pluck('group_id');

        if ($groupIds->isEmpty()) {
            return collect();
        }

        return Group::whereIn('id', $groupIds)
            ->where('status', 1)
            ->orderBy('name_en')
            ->get(['id', 'name_en', 'status']);
    }

    /**
     * Get subjects for a class, expanding parent subjects into their individual papers.
     * e.g. "Bangla" (parent) → ["Bangla 1st Paper", "Bangla 2nd Paper"]
     */
    private function getSubjectsForClass(int $classId, ?int $groupId = null): \Illuminate\Support\Collection
    {
        $assignments = SubjectClassAssignment::where('school_class_id', $classId)
            ->where('is_active', true)
            ->when($groupId, fn ($query) => $query->where(function ($subQuery) use ($groupId) {
                $subQuery->whereNull('group_id')->orWhere('group_id', $groupId);
            }), fn ($query) => $query->whereNull('group_id'))
            ->with(['subject' => fn($q) => $q->with('papers')])
            ->get();

        $subjects = collect();
        foreach ($assignments as $assignment) {
            $subject = $assignment->subject;
            if (! $subject) continue;

            if ($subject->is_parent && $subject->papers->isNotEmpty()) {
                // Replace parent with its individual papers
                foreach ($subject->papers as $paper) {
                    $subjects->push($paper);
                }
            } else {
                $subjects->push($subject);
            }
        }

        return $subjects->unique('id')->values();
    }

    private function getStudentsForClass(
        Exam $exam,
        int $classId,
        ?int $sectionId = null,
        ?int $groupId = null,
        ?int $subjectId = null
    )
    {
        $students = Student::where('status', 1)
            ->whereHas('academicInformations', function ($q) use ($exam, $classId, $sectionId, $groupId) {
                $q->where('school_class_id', $classId);
                if ($exam->academic_session_id) {
                    $q->where('academic_session_id', $exam->academic_session_id);
                }
                if ($sectionId) {
                    $q->where('section_id', $sectionId);
                }
                if ($groupId) {
                    $q->where('group_id', $groupId);
                }
            })
            ->with(['academicInformations' => fn($q) => $q->where('school_class_id', $classId)
                ->when($sectionId, fn ($subQuery) => $subQuery->where('section_id', $sectionId))
                ->when($groupId, fn ($subQuery) => $subQuery->where('group_id', $groupId))
                ->with(['section', 'group'])])
            ->orderBy('full_name_en')
            ->get();

        if (! $subjectId) {
            return $students;
        }

        $assignments = SubjectClassAssignment::query()
            ->where('subject_id', $subjectId)
            ->where('school_class_id', $classId)
            ->where('is_active', true)
            ->when($groupId, fn ($query) => $query->where(function ($subQuery) use ($groupId) {
                $subQuery->whereNull('group_id')->orWhere('group_id', $groupId);
            }), fn ($query) => $query->whereNull('group_id'))
            ->get();

        if ($assignments->isEmpty()) {
            return collect();
        }

        return $students->filter(function (Student $student) use ($assignments, $classId, $sectionId, $groupId) {
            $academicInfo = $student->academicInformations->first(function (StudentAcademicInformation $info) use ($classId, $sectionId, $groupId) {
                if ((int) $info->school_class_id !== $classId) {
                    return false;
                }

                if ($sectionId && (int) $info->section_id !== $sectionId) {
                    return false;
                }

                if ($groupId && (int) $info->group_id !== $groupId) {
                    return false;
                }

                return true;
            });

            if (! $academicInfo) {
                return false;
            }

            foreach ($assignments as $assignment) {
                if ($this->studentMatchesSubjectAssignment($student, $academicInfo, $assignment)) {
                    return true;
                }
            }

            return false;
        })->values();
    }

    private function studentMatchesSubjectAssignment(
        Student $student,
        StudentAcademicInformation $academicInfo,
        SubjectClassAssignment $assignment
    ): bool {
        if ($assignment->group_id && (int) $academicInfo->group_id !== (int) $assignment->group_id) {
            return false;
        }

        if ($assignment->gender !== 'all') {
            $expectedGender = $assignment->gender === 'male' ? Student::MALE : Student::FEMALE;

            if ((int) $student->gender !== $expectedGender) {
                return false;
            }
        }

        if ($assignment->religion !== 'all') {
            $studentReligion = Student::religionTokenFromId($student->religion);

            if ($studentReligion !== $assignment->religion) {
                return false;
            }
        }

        return true;
    }
}
