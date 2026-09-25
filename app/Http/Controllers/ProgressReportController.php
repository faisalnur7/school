<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\StudentResultReportMail;
use App\Models\Student;
use App\Models\ResultEmailStatus;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamSubject;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\AttendanceItem;
use App\Models\SchoolSetting;
use App\Models\ProgressReportTemplateSetting;
use App\Models\StudentAcademicInformation;
use App\Models\Subject;
use App\Models\SubjectClassAssignment;
use App\Services\GradingService;
use App\Services\ResultRankingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ProgressReportController extends Controller
{
    public function index(Request $request)
    {
        if ($this->hasCompleteFilters($request)) {
            return $this->show($request);
        }

        return view('pages.progress-report.index', $this->buildFilterPageData($request));
    }

    public function show(Request $request)
    {
        $request->validate([
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id'   => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'exam_id'    => ['required', 'exists:exams,id'],
            'student_id' => ['nullable'],
            'preview'    => ['nullable'],
        ]);

        $filters  = $request->only(['session_id', 'class_id', 'section_id', 'exam_id', 'student_id']);
        $request->validate(['subject_ids' => ['sometimes', 'array'], 'subject_ids.*' => ['integer']]);
        $isPreview = $request->boolean('preview');
        $exam     = Exam::with('academicSession')
            ->where('type', Exam::TYPE_TERMINAL)
            ->findOrFail($filters['exam_id']);
        $school   = SchoolSetting::current();
        $templateSettings = ProgressReportTemplateSetting::current();
        $gradeScale = GradingService::allGrades();
        $sections = Section::where('school_class_id', $filters['class_id'])->get();

        $cohortFilters = $filters;
        unset($cohortFilters['student_id']);

        $students = $this->getStudents($cohortFilters);
        $availableSubjects = $this->availableSubjectsForSelection((int) $filters['class_id']);
        $filters['subject_settings_applied'] = $request->boolean('subject_settings_applied');
        $filters['subject_ids'] = $this->selectedSubjectIds($request, $availableSubjects);
        if ($isPreview) {
            $students = $students->take(1);
        }
        $attendanceData = $this->getTerminalAttendanceData($exam, (int) $filters['class_id'], $students->pluck('id'));
        $studentsData = $this->rankProgressReports(
            $students->map(fn($s) => $this->buildStudentData($s, $exam, $filters, $attendanceData))
        );

        if (! empty($filters['student_id'])) {
            $studentsData = $studentsData->filter(function ($data) use ($filters) {
                return $this->matchesStudentIdentifier($data['student'], $filters['student_id']);
            })->values();
        }

        $statusMap = $this->buildStatusMap($studentsData->pluck('student.id')->all(), (int) $filters['exam_id']);

        return view('pages.progress-report.results', compact('studentsData', 'exam', 'school', 'gradeScale', 'filters', 'statusMap', 'sections', 'templateSettings', 'isPreview', 'availableSubjects'))
            ->with([
                'sessions' => AcademicSession::orderByDesc('id')->get(),
                'classes'  => SchoolClass::all(),
                'exams'    => Exam::where('type', Exam::TYPE_TERMINAL)->orderByDesc('id')->get(),
            ]);
    }

    public function pdf(Request $request)
    {
        $request->validate([
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id'   => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'exam_id'    => ['required', 'exists:exams,id'],
            'student_id' => ['nullable'],
        ]);

        $filters  = $request->only(['session_id', 'class_id', 'section_id', 'exam_id', 'student_id']);
        $request->validate(['subject_ids' => ['sometimes', 'array'], 'subject_ids.*' => ['integer']]);
        $exam     = Exam::with('academicSession')
            ->where('type', Exam::TYPE_TERMINAL)
            ->findOrFail($filters['exam_id']);
        $school   = SchoolSetting::current();
        $templateSettings = ProgressReportTemplateSetting::current();
        $gradeScale = GradingService::allGrades();

        $cohortFilters = $filters;
        unset($cohortFilters['student_id']);

        $students = $this->getStudents($cohortFilters);
        $availableSubjects = $this->availableSubjectsForSelection((int) $filters['class_id']);
        $filters['subject_settings_applied'] = $request->boolean('subject_settings_applied');
        $filters['subject_ids'] = $this->selectedSubjectIds($request, $availableSubjects);
        $attendanceData = $this->getTerminalAttendanceData($exam, (int) $filters['class_id'], $students->pluck('id'));
        $studentsData = $this->rankProgressReports(
            $students->map(fn($s) => $this->buildStudentData($s, $exam, $filters, $attendanceData))
        );

        if (! empty($filters['student_id'])) {
            $studentsData = $studentsData->filter(function ($data) use ($filters) {
                return $this->matchesStudentIdentifier($data['student'], $filters['student_id']);
            })->values();
        }

        $html = view('pages.progress-report.print', compact('studentsData', 'exam', 'school', 'gradeScale', 'filters', 'templateSettings'))->render();

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'orientation' => strtolower((string) $templateSettings->paper_orientation) === 'landscape' ? 'L' : 'P',
            'margin_top' => $templateSettings->margin_top_mm * 10,
            'margin_bottom' => $templateSettings->margin_bottom_mm * 10,
            'margin_left' => $templateSettings->margin_left_mm * 10,
            'margin_right' => $templateSettings->margin_right_mm * 10,
        ]);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))->header('Content-Type', 'application/pdf');
    }

    public function sendEmail(Request $request)
    {
        $filters = $request->validate([
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id'   => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'exam_id'    => ['required', 'exists:exams,id'],
            'student_id' => ['required', 'exists:students,id'],
        ]);

        $exam = Exam::with('academicSession')->findOrFail($filters['exam_id']);
        $student = Student::findOrFail($filters['student_id']);
        $emails = collect([$student->father_email, $student->mother_email])
            ->filter(fn ($email) => is_string($email) && trim($email) !== '')
            ->map(fn ($email) => trim($email))
            ->unique()
            ->values();

        if ($emails->isEmpty()) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'No parent email found for this student.'], 422);
            }
            return back()->with('error', 'No parent email found for this student.');
        }

        $studentData = $this->buildStudentData($student, $exam, $filters);
        $rows = collect($studentData['subjectRows'])->map(function ($row) {
            return [
                'Subject' => $row['subject_name'],
                'Obtained' => is_null($row['obtained']) ? 'AB' : number_format((float) $row['obtained'], 0),
                'Grade' => $row['grade'],
                'GPA' => number_format((float) $row['gpa'], 1),
            ];
        })->values()->all();

        $summary = $studentData['summary'];
        $meta = [
            'Exam' => $exam->name,
            'Session' => $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? ''),
            'Total Marks' => number_format((float) $summary['obtained'], 0) . '/' . number_format((float) $summary['fullMarks'], 0),
            'Percentage' => number_format((float) $summary['percentage'], 2) . '%',
            'Final Grade' => (string) $summary['grade'],
            'Final GPA' => number_format((float) $summary['gpa'], 2),
        ];

        foreach ($emails as $email) {
            Mail::to($email)->send(new StudentResultReportMail($student, 'Terminal Exam Report', $meta, $rows));
        }

        $contextKey = $this->contextKey((int) $filters['exam_id'], (int) $student->id);
        ResultEmailStatus::updateOrCreate(
            ['context_key' => $contextKey],
            [
                'report_type' => 'progress',
                'student_id' => $student->id,
                'exam_id' => (int) $filters['exam_id'],
                'session_id' => (int) $filters['session_id'],
                'class_id' => (int) $filters['class_id'],
                'section_id' => (int) $filters['section_id'],
                'is_sent' => true,
                'sent_at' => now(),
            ]
        );

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Result email sent to parent(s).']);
        }

        return back()->with('success', 'Result email sent to parent(s).');
    }

    private function contextKey(int $examId, int $studentId): string
    {
        return "progress:exam:{$examId}:student:{$studentId}";
    }

    private function matchesStudentIdentifier(Student $student, mixed $identifier): bool
    {
        if (is_null($identifier) || $identifier === '') {
            return false;
        }

        $identifier = (string) $identifier;

        return (string) $student->id === $identifier
            || (string) ($student->student_cid ?? '') === $identifier;
    }

    private function rankProgressReports(\Illuminate\Support\Collection $studentsData): \Illuminate\Support\Collection
    {
        $rows = $studentsData->map(function (array $row) {
            $failedSubjectCount = collect($row['subjectRows'] ?? [])
                ->filter(fn ($subjectRow) => (bool) ($subjectRow['paper_fail'] ?? false))
                ->count();

            $row['failed_subject_count'] = $failedSubjectCount;
            $row['has_failed'] = $failedSubjectCount > 0;
            $row['status'] = $failedSubjectCount > 0 ? 'Failed' : 'Passed';
            $row['rank'] = null;

            return $row;
        })->values()->all();

        $rows = app(ResultRankingService::class)->rank(
            $rows,
            fn (array $row) => [
                'failed_subjects' => $row['failed_subject_count'] ?? 0,
                'total' => data_get($row, 'summary.obtained', 0),
                'gpa' => data_get($row, 'summary.gpa', 0),
                'attendance' => $row['attendancePresent'] ?? 0,
            ],
            fn (array $row) => $row['student']->student_cid ?? $row['student']->id,
        );

        return collect($rows);
    }

    private function hasCompleteFilters(Request $request): bool
    {
        return $request->filled(['session_id', 'class_id', 'section_id', 'exam_id']);
    }

    private function buildFilterPageData(Request $request): array
    {
        $filters = $request->only(['session_id', 'class_id', 'section_id', 'exam_id', 'student_id']);

        $sections = ! empty($filters['class_id'])
            ? Section::where('school_class_id', (int) $filters['class_id'])->orderBy('name_en')->get()
            : collect();

        return [
            'sessions' => AcademicSession::orderByDesc('id')->get(),
            'classes' => SchoolClass::all(),
            'sections' => $sections,
            'exams' => Exam::where('type', Exam::TYPE_TERMINAL)->orderByDesc('id')->get(),
            'filters' => $filters,
        ];
    }

    private function buildStatusMap(array $studentIds, int $examId): array
    {
        if (empty($studentIds)) {
            return [];
        }

        return ResultEmailStatus::query()
            ->where('report_type', 'progress')
            ->where('exam_id', $examId)
            ->whereIn('student_id', $studentIds)
            ->pluck('is_sent', 'student_id')
            ->map(fn ($v) => (bool) $v)
            ->all();
    }

    private function availableSubjectsForSelection(int $classId): \Illuminate\Support\Collection
    {
        return SubjectClassAssignment::with('subject')
            ->where('school_class_id', $classId)
            ->where('is_active', true)
            ->get()
            ->map(fn (SubjectClassAssignment $assignment) => $assignment->subject)
            ->filter()
            ->unique('id')
            ->sortBy(fn (Subject $subject) => $subject->name)
            ->values();
    }

    private function selectedSubjectIds(Request $request, \Illuminate\Support\Collection $availableSubjects): array
    {
        $availableIds = $availableSubjects->pluck('id')->map(fn ($id) => (int) $id);

        if (! $request->boolean('subject_settings_applied')) {
            return $availableIds->all();
        }

        return collect($request->input('subject_ids', []))
            ->map(fn ($id) => (int) $id)
            ->intersect($availableIds)
            ->values()
            ->all();
    }

    private function getStudents(array $filters)
    {
        if (!empty($filters['student_id'])) {
            return Student::where(function ($query) use ($filters) {
                    $query->where('id', $filters['student_id'])
                        ->orWhere('student_cid', $filters['student_id']);
                })
                ->where('status', 1)
                ->whereHas('academicInformations', function ($query) use ($filters) {
                    $query->where('academic_session_id', $filters['session_id'])
                        ->when($filters['class_id'] ?? null, fn ($q) => $q->where('school_class_id', $filters['class_id']))
                        ->when($filters['section_id'] ?? null, fn ($q) => $q->where('section_id', $filters['section_id']))
                        ->where('is_current', true)
                        ->where('academic_status', 'active');
                })
                ->get();
        }

        $ids = StudentAcademicInformation::where('academic_session_id', $filters['session_id'])
            ->where('school_class_id', $filters['class_id'])
            ->where('section_id', $filters['section_id'])
            ->where('is_current', true)
            ->where('academic_status', 'active')
            ->pluck('student_id');

        return Student::whereIn('id', $ids)->where('status', 1)->orderBy('full_name_en')->get();
    }

    private function buildStudentData(Student $student, Exam $exam, array $filters, array $attendanceData = []): array
    {
        $academicInfo = StudentAcademicInformation::with(['schoolClass', 'section', 'academicSession'])
            ->where('student_id', $student->id)
            ->where('academic_session_id', $filters['session_id'])
            ->first();

        $applicableSubjectIds = SubjectClassAssignment::query()
            ->where('school_class_id', $filters['class_id'])
            ->where('is_active', true)
            ->get()
            ->filter(function (SubjectClassAssignment $assignment) use ($student, $academicInfo) {
                return ($assignment->group_id === null || (int) $assignment->group_id === (int) ($academicInfo?->group_id))
                    && $assignment->appliesToStudent($student->gender, $student->religion);
            })
            ->pluck('subject_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $marks = ExamMark::with(['subject'])
            ->where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->get()
            ->filter(fn (ExamMark $mark) => in_array((int) $mark->subject_id, $applicableSubjectIds, true)
                && in_array((int) $mark->subject_id, $filters['subject_ids'] ?? [], true));

        $examSubjects = ExamSubject::with('subject')
            ->where('exam_id', $exam->id)
            ->get()
            ->keyBy('subject_id');

        // Highest marks per subject
        $highestMarks = ExamMark::where('exam_id', $exam->id)
            ->selectRaw('subject_id, MAX(total) as highest')
            ->groupBy('subject_id')
            ->pluck('highest', 'subject_id');

        // Build subject rows with paper grouping
        $subjectRows = [];
        $parentGroups = [];

        foreach ($marks as $mark) {
            $subject = $mark->subject;
            if (!$subject) continue;

            $examSubject = $examSubjects[$subject->id] ?? null;
            $fullMarks   = $subject ? (float) $subject->total_marks : 0;
            $passMark    = (float) ($subject->getEffectiveMarksForClass((int) $filters['class_id'])['pass_mark'] ?? 33);
            $highest     = (float) ($highestMarks[$subject->id] ?? 0);

            $obtained = $mark->is_absent ? null : (float) $mark->total;
            $passed = ! $mark->is_absent && $obtained >= $passMark && $mark->letter_grade !== 'F';

            $row = [
                'subject_id'   => $subject->id,
                'subject_name' => $subject->name,
                'is_paper'     => (bool) $subject->is_paper,
                'parent_id'    => $subject->parent_id,
                'full_marks'   => $fullMarks,
                'obtained'     => $obtained,
                'highest'      => $highest,
                'pass_mark'    => $passMark,
                'grade'        => $mark->is_absent ? 'AB' : $mark->letter_grade,
                'gpa'          => $mark->is_absent ? null : (float) $mark->gpa,
                'is_absent'    => (bool) $mark->is_absent,
                'paper_fail'   => ! $passed,
            ];

            if ($subject->is_paper && $subject->parent_id) {
                // dd($row, $subject);
                $parentGroups[$subject->parent_id]['papers'][] = $row;
            } else {
                $subjectRows[$subject->id] = $row;
            }
        }

        // Merge paper groups into parent rows
        foreach ($parentGroups as $parentId => $group) {
            $papers     = $group['papers'];
            $anyFail    = collect($papers)->contains(fn($p) => $p['paper_fail']);
            $anyAbsent  = collect($papers)->contains(fn($p) => $p['is_absent']);
            $totalFull  = collect($papers)->sum('full_marks');
            $totalObt   = collect($papers)->sum(fn($p) => $p['obtained'] ?? 0);
            $highest    = collect($papers)->max('highest');
            $combined   = GradingService::getGrade($totalObt, $totalFull);
            $grade      = $anyAbsent ? 'AB' : ($anyFail ? 'F' : $combined['letter']);
            $gpa        = $anyAbsent ? 0 : ($anyFail ? 0 : $combined['gpa']);

            // Get parent subject name
            $parentSubject = \App\Models\Subject::find($parentId);

            $subjectRows[$parentId] = [
                'subject_id'   => $parentId,
                'subject_name' => $parentSubject?->name ?? 'Combined',
                'is_paper'     => false,
                'parent_id'    => null,
                'full_marks'   => $totalFull,
                'obtained'     => $totalObt,
                'highest'      => $highest,
                'grade'        => $grade,
                'gpa'          => $gpa,
                'is_absent'    => false,
                'paper_fail'   => $anyFail || $anyAbsent,
                'papers'       => $papers,
            ];
        }

        // Summary
        $validRows  = collect($subjectRows)->filter(fn($r) => !$r['is_absent']);
        $fullMarks  = collect($subjectRows)->sum('full_marks');
        $obtained   = $validRows->sum('obtained');
        $percentage = $fullMarks > 0 ? round(($obtained / $fullMarks) * 100, 2) : 0;
        $gpas       = collect($subjectRows)->map(
            fn ($r) => ($r['is_absent'] || ($r['paper_fail'] ?? false)) ? 0 : (float) $r['gpa']
        )->values()->toArray();
        $hasFailedSubject = collect($subjectRows)->contains(
            fn ($r) => (bool) ($r['is_absent'] || ($r['paper_fail'] ?? false))
        );
        $gpa        = GradingService::calculateGpa($gpas, $hasFailedSubject);
        $grade      = GradingService::getGpaLabel($gpa);

        // Use the same period as the terminal-result ranking.
        $attendanceTotal = (int) ($attendanceData['working_days'] ?? 0);
        $attendancePresent = (int) ($attendanceData['present_by_student'][$student->id] ?? 0);

        return [
            'student'           => $student,
            'academicInfo'      => $academicInfo,
            'subjectRows' => array_values(
                                array_merge(
                                    array_filter($subjectRows, fn($r) => !empty($r['papers'])),
                                    array_filter($subjectRows, fn($r) =>  empty($r['papers'])),
                                )
                            ),
            'summary'           => compact('fullMarks', 'obtained', 'percentage', 'gpa', 'grade'),
            'attendancePresent' => $attendancePresent,
            'attendanceTotal'   => $attendanceTotal,
        ];
    }

    /**
     * Get school-opened days and student present days immediately before a terminal exam.
     */
    private function getTerminalAttendanceData(Exam $exam, int $classId, $studentIds): array
    {
        $empty = ['working_days' => 0, 'present_by_student' => []];

        if (! $exam->start_date || ! $exam->academic_session_id) {
            return $empty;
        }

        $year = (int) ($exam->year ?: $exam->start_date->year);
        $pairNo = (int) ($exam->pair_no ?: 1);
        $periodStart = Carbon::create($year, 1, 1)->startOfDay();

        if ($pairNo > 1) {
            $previousExam = Exam::query()
                ->where('academic_session_id', $exam->academic_session_id)
                ->where('type', Exam::TYPE_TERMINAL)
                ->where('pair_no', $pairNo - 1)
                ->first();

            if (! $previousExam?->end_date) {
                return $empty;
            }

            $periodStart = $previousExam->end_date->copy()->addDay()->startOfDay();
        }

        $periodEnd = $exam->start_date->copy()->subDay()->endOfDay();
        if ($periodStart->greaterThan($periodEnd)) {
            return $empty;
        }

        $attendanceQuery = Attendance::query()
            ->where('session_id', $exam->academic_session_id)
            ->where('class_id', $classId)
            ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()]);
        $attendanceIds = (clone $attendanceQuery)->pluck('id');
        $workingDays = (clone $attendanceQuery)->distinct('date')->count('date');

        if ($attendanceIds->isEmpty() || $studentIds->isEmpty()) {
            return ['working_days' => $workingDays, 'present_by_student' => []];
        }

        $presentByStudent = AttendanceItem::query()
            ->whereIn('attendance_id', $attendanceIds)
            ->whereIn('student_id', $studentIds)
            ->where('status', 'present')
            ->selectRaw('student_id, COUNT(*) as present_days')
            ->groupBy('student_id')
            ->pluck('present_days', 'student_id')
            ->map(fn ($days) => (int) $days)
            ->all();

        return ['working_days' => $workingDays, 'present_by_student' => $presentByStudent];
    }
}
