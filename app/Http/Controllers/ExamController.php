<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\ExamMark;
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
            'type'                => 'required|in:term,tutorial',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'year'                => 'required|digits:4',
            'start_date'          => 'nullable|date',
            'end_date'            => 'nullable|date|after_or_equal:start_date',
            'status'              => 'in:draft,published',
        ]);

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
            'type'                => 'required|in:term,tutorial',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'year'                => 'required|digits:4',
            'start_date'          => 'nullable|date',
            'end_date'            => 'nullable|date|after_or_equal:start_date',
            'status'              => 'in:draft,published',
        ]);

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

    /**
     * Marks entry: pick class → pick subject → enter marks for students of that class
     */
    public function marksEntry(Request $request, Exam $exam)
    {
        $classId   = $request->class_id;
        $subjectId = $request->subject_id;

        $classes = SchoolClass::where('status', 1)->orderBy('id')->get();

        $subjects  = collect();
        $subject   = null;
        $students  = collect();
        $existingMarks  = [];
        $subjectConfig  = null;
        $selectedClass  = null;

        if ($classId) {
            $selectedClass = SchoolClass::find($classId);

            $subjects = $this->getSubjectsForClass($classId);

            if (! $subjectId && $subjects->isNotEmpty()) {
                $subjectId = $subjects->first()->id;
            }

            $subject = $subjectId ? Subject::find($subjectId) : null;

            // Students in this class for this session
            $students = $this->getStudentsForClass($exam, $classId);

            if ($subject) {
                ExamMark::where('exam_id', $exam->id)
                    ->where('subject_id', $subject->id)
                    ->whereIn('student_id', $students->pluck('id'))
                    ->get()
                    ->each(fn($m) => $existingMarks[$m->student_id] = $m);

                $subjectConfig = $subject->getEffectiveMarksForClass($classId);
            }
        }

        return view('pages.exams.marks-entry', compact(
            'exam', 'classes', 'selectedClass', 'subjects', 'subject',
            'students', 'existingMarks', 'subjectConfig', 'classId', 'subjectId'
        ));
    }

    /**
     * Save marks for a class + subject
     */
    public function saveMarks(Request $request, Exam $exam)
    {
        $request->validate([
            'class_id'           => 'required|exists:school_classes,id',
            'subject_id'         => 'required|exists:subjects,id',
            'marks'              => 'array',
            'marks.*.student_id' => 'required|exists:students,id',
        ]);

        $classId   = $request->class_id;
        $subjectId = $request->subject_id;
        $subject   = Subject::findOrFail($subjectId);
        $config    = $subject->getEffectiveMarksForClass($classId);

        DB::transaction(function () use ($request, $exam, $subjectId, $classId, $config) {
            foreach ($request->marks as $row) {
                $studentId = $row['student_id'];
                $isAbsent  = ! empty($row['is_absent']);

                $cq        = $isAbsent ? 0 : (float) ($row['cq_marks'] ?? 0);
                $mcq       = $isAbsent ? 0 : (float) ($row['mcq_marks'] ?? 0);
                $practical = $isAbsent ? 0 : (float) ($row['practical_marks'] ?? 0);
                $viva      = $isAbsent ? 0 : (float) ($row['viva_marks'] ?? 0);
                $total     = $cq + $mcq + $practical + $viva;
                $fullMarks = (float) ($config['total_marks'] ?: 100);

                $grade = GradingService::getGrade($total, $fullMarks);

                ExamMark::updateOrCreate(
                    ['exam_id' => $exam->id, 'student_id' => $studentId, 'subject_id' => $subjectId],
                    [
                        'cq_marks'        => $cq,
                        'mcq_marks'       => $mcq,
                        'practical_marks' => $practical,
                        'viva_marks'      => $viva,
                        'total'           => $total,
                        'is_absent'       => $isAbsent,
                        'letter_grade'    => $isAbsent ? 'AB' : $grade['letter'],
                        'gpa'             => $isAbsent ? 0 : $grade['gpa'],
                    ]
                );
            }
        });

        return redirect()->route('exams.marks-entry', [
            'exam'       => $exam->id,
            'class_id'   => $classId,
            'subject_id' => $subjectId,
        ])->with('success', 'Marks saved successfully.');
    }

    /**
     * Preview: marks for a class + subject with filters
     */
    public function preview(Request $request, Exam $exam)
    {
        $classId   = $request->class_id;
        $subjectId = $request->subject_id;
        $filter    = $request->filter ?? 'all';

        $classes  = SchoolClass::where('status', 1)->orderBy('id')->get();
        $subjects = collect();
        $subject  = null;
        $marks    = collect();
        $passMark = 33;
        $selectedClass = null;

        if ($classId) {
            $selectedClass = SchoolClass::find($classId);

            $subjects = $this->getSubjectsForClass($classId);

            if (! $subjectId && $subjects->isNotEmpty()) {
                $subjectId = $subjects->first()->id;
            }

            $subject = $subjectId ? Subject::find($subjectId) : null;

            if ($subject) {
                $config   = $subject->getEffectiveMarksForClass($classId);
                $passMark = (float) ($config['pass_mark'] ?? 33);

                $studentIds = $this->getStudentsForClass($exam, $classId)->pluck('id');

                $marks = ExamMark::where('exam_id', $exam->id)
                    ->where('subject_id', $subjectId)
                    ->whereIn('student_id', $studentIds)
                    ->with('student')
                    ->get();

                $marks = match ($filter) {
                    'passed'  => $marks->filter(fn($m) => ! $m->is_absent && $m->total >= $passMark),
                    'failed'  => $marks->filter(fn($m) => $m->is_absent || $m->total < $passMark),
                    'highest' => $marks->sortByDesc('total'),
                    default   => $marks->sortBy(fn($m) => $m->student->full_name_en),
                };
            }
        }

        return view('pages.exams.preview', compact(
            'exam', 'classes', 'selectedClass', 'subjects', 'subject',
            'marks', 'filter', 'passMark', 'classId', 'subjectId'
        ));
    }

    /**
     * Terminal result: all classes, all subjects, grading + ranking per class
     */
    public function terminalResult(Request $request, Exam $exam)
    {
        $exam->load(['academicSession']);

        $classId       = $request->class_id;
        $filter        = $request->filter ?? 'all';
        $classes       = SchoolClass::where('status', 1)->orderBy('id')->get();
        $selectedClass = $classId ? SchoolClass::find($classId) : null;

        $results  = [];
        $subjects = collect();

        if ($classId) {
            $subjects = $this->getSubjectsForClass($classId);

            $students = $this->getStudentsForClass($exam, $classId);
            $studentIds = $students->pluck('id');

            $allMarks = ExamMark::where('exam_id', $exam->id)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->groupBy('student_id');

            foreach ($students as $student) {
                $studentMarks   = $allMarks->get($student->id, collect());
                $subjectResults = [];
                $totalObtained  = 0;
                $totalFull      = 0;
                $gpas           = [];
                $hasFailed      = false;

                foreach ($subjects as $subject) {
                    $config    = $subject->getEffectiveMarksForClass($classId);
                    $fullMarks = (float) ($config['total_marks'] ?: 100);
                    $passMark  = (float) ($config['pass_mark'] ?? 33);
                    $mark      = $studentMarks->firstWhere('subject_id', $subject->id);
                    $obtained  = $mark ? (float) $mark->total : 0;
                    $isAbsent  = $mark ? $mark->is_absent : false;
                    $grade     = GradingService::getGrade($obtained, $fullMarks);

                    if ($grade['letter'] === 'F' || $isAbsent) {
                        $hasFailed = true;
                    }

                    $subjectResults[$subject->id] = [
                        'obtained'     => $obtained,
                        'full_marks'   => $fullMarks,
                        'pass_mark'    => $passMark,
                        'letter_grade' => $isAbsent ? 'AB' : $grade['letter'],
                        'gpa'          => $isAbsent ? 0 : $grade['gpa'],
                        'is_absent'    => $isAbsent,
                        'passed'       => ! $isAbsent && $obtained >= $passMark,
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
                    'has_failed'      => $hasFailed,
                    'status'          => $hasFailed ? 'Failed' : 'Passed',
                ];
            }

            // Sort by total descending and assign rank
            uasort($results, fn($a, $b) => $b['total_obtained'] <=> $a['total_obtained']);
            $rank = 1;
            $prevTotal = null;
            $sameCount = 0;
            foreach ($results as &$row) {
                if ($prevTotal !== null && $row['total_obtained'] === $prevTotal) {
                    $row['rank'] = $rank - $sameCount;
                    $sameCount++;
                } else {
                    $row['rank'] = $rank;
                    $sameCount  = 1;
                }
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
            'exam', 'classes', 'selectedClass', 'subjects', 'displayResults', 'results', 'filter', 'classId'
        ));
    }

    /**
     * PDF: subject marks preview
     */
    public function previewPdf(Request $request, Exam $exam)
    {
        $classId   = $request->class_id;
        $subjectId = $request->subject_id;
        $subject   = Subject::find($subjectId);
        $config    = $subject ? $subject->getEffectiveMarksForClass($classId) : [];
        $passMark  = (float) ($config['pass_mark'] ?? 33);

        $studentIds = $this->getStudentsForClass($exam, $classId)->pluck('id');

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
        $classId  = $request->class_id;
        $exam->load(['academicSession']);

        $selectedClass = SchoolClass::find($classId);

        $subjects = $this->getSubjectsForClass($classId);

        $students   = $this->getStudentsForClass($exam, $classId);
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
            $hasFailed      = false;

            foreach ($subjects as $subject) {
                $config    = $subject->getEffectiveMarksForClass($classId);
                $fullMarks = (float) ($config['total_marks'] ?: 100);
                $passMark  = (float) ($config['pass_mark'] ?? 33);
                $mark      = $studentMarks->firstWhere('subject_id', $subject->id);
                $obtained  = $mark ? (float) $mark->total : 0;
                $isAbsent  = $mark ? $mark->is_absent : false;
                $grade     = GradingService::getGrade($obtained, $fullMarks);

                if ($grade['letter'] === 'F' || $isAbsent) $hasFailed = true;

                $subjectResults[$subject->id] = [
                    'obtained'     => $obtained,
                    'full_marks'   => $fullMarks,
                    'letter_grade' => $isAbsent ? 'AB' : $grade['letter'],
                    'gpa'          => $isAbsent ? 0 : $grade['gpa'],
                    'is_absent'    => $isAbsent,
                    'passed'       => ! $isAbsent && $obtained >= $passMark,
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
                'has_failed'      => $hasFailed,
                'status'          => $hasFailed ? 'Failed' : 'Passed',
            ];
        }

        uasort($results, fn($a, $b) => $b['total_obtained'] <=> $a['total_obtained']);
        $rank = 1;
        foreach ($results as &$row) { $row['rank'] = $rank++; }
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

    /**
     * Get subjects for a class, expanding parent subjects into their individual papers.
     * e.g. "Bangla" (parent) → ["Bangla 1st Paper", "Bangla 2nd Paper"]
     */
    private function getSubjectsForClass(int $classId): \Illuminate\Support\Collection
    {
        $assignments = SubjectClassAssignment::where('school_class_id', $classId)
            ->where('is_active', true)
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

    private function getStudentsForClass(Exam $exam, int $classId)
    {
        return Student::where('status', 1)
            ->whereHas('academicInformations', function ($q) use ($exam, $classId) {
                $q->where('school_class_id', $classId);
                if ($exam->academic_session_id) {
                    $q->where('academic_session_id', $exam->academic_session_id);
                }
            })
            ->with(['academicInformations' => fn($q) => $q->where('school_class_id', $classId)
                ->with(['section', 'group'])])
            ->orderBy('full_name_en')
            ->get();
    }
}
