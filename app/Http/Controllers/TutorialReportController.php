<?php

namespace App\Http\Controllers;

use App\Mail\StudentResultReportMail;
use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\AttendanceItem;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ResultEmailStatus;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\Subject;
use App\Models\SubjectClassAssignment;
use App\Services\GradingService;
use App\Services\ResultRankingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TutorialReportController extends Controller
{
    public function index(Request $request)
    {
        if ($this->hasCompleteFilters($request)) {
            return $this->show($request);
        }

        return view('pages.tutorial-report.index', [
            'sessions' => AcademicSession::orderByDesc('id')->get(),
            'classes'  => SchoolClass::all(),
            'exams'    => Exam::where('type', Exam::TYPE_TUTORIAL)->orderByDesc('id')->get(),
            'filters'  => $request->only(['session_id', 'class_id', 'section_id', 'exam_id', 'student_id', 'results_per_page']),
        ]);
    }

    public function show(Request $request)
    {
        $request->validate([
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id'   => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'exam_id'    => ['required', 'exists:exams,id'],
            'student_id' => ['nullable', 'string'],
            'subject_filter' => ['nullable', 'boolean'],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['integer'],
            'religion_subject' => ['nullable'],
            'results_per_page' => ['nullable', 'integer', 'in:1,2,3'],
        ]);

        $filters  = $request->only(['session_id', 'class_id', 'section_id', 'exam_id', 'student_id', 'subject_filter', 'subject_ids', 'religion_subject']);
        $filters['results_per_page'] = (int) $request->input('results_per_page', 2);
        $exam     = Exam::with('academicSession')->findOrFail($filters['exam_id']);
        $reportContext = $this->buildReportContext($filters);
        $sections = Section::where('school_class_id', $filters['class_id'])->orderBy('name_en')->get();
        $tutorialSubjects = $this->getAssignedTutorialSubjects((int) $filters['class_id']);
        $filters = $this->normalizeSubjectFilters($request, $filters, $tutorialSubjects);

        if ($exam->type !== Exam::TYPE_TUTORIAL) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'Selected exam is not a tutorial exam.'], 422);
            }
            return back()->with('error', 'Selected exam is not a tutorial exam.');
        }

        $students = $this->getStudents($filters);
        $studentSubjectIds = $students->mapWithKeys(fn ($student) => [
            $student->id => $this->getStudentTutorialSubjectIds((int) $filters['class_id'], $student),
        ]);
        $attendanceData = $this->getAttendanceData($exam, (int) $filters['class_id'], $students->pluck('id'));
        $highestMarks = ExamMark::where('exam_id', $exam->id)
            ->where('is_absent', false)
            ->when($filters['subject_filter'], fn ($query) => $query->whereIn('subject_id', $filters['subject_ids']))
            ->selectRaw('subject_id, MAX(total) as highest')
            ->groupBy('subject_id')
            ->pluck('highest', 'subject_id');
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes = SchoolClass::all();
        $exams = Exam::where('type', Exam::TYPE_TUTORIAL)->orderByDesc('id')->get();

        $studentsData = $students->map(function ($student) use ($exam, $attendanceData, $highestMarks, $filters, $studentSubjectIds) {
            $academicInfo = StudentAcademicInformation::with(['schoolClass', 'section', 'academicSession'])
                ->where('student_id', $student->id)
                ->where('academic_session_id', $exam->academic_session_id)
                ->first();

            $marks = ExamMark::with('subject')
                ->where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->get()
                ->filter(fn ($m) => $m->subject
                    && $m->subject->include_in_tutorial_exam
                    && ($studentSubjectIds[$student->id] === null || $studentSubjectIds[$student->id]->contains((int) $m->subject_id))
                    && (! $filters['subject_filter'] || in_array((int) $m->subject_id, $filters['subject_ids'], true)))
                ->sortBy(fn ($m) => sprintf('%d:%s', $m->subject->is_paper ? 0 : 1, mb_strtolower($m->subject->name)));

            $marks = app(\App\Services\SubjectOrderingService::class)->sortSubjects(
                $marks->map(fn ($mark) => $mark->subject)->unique('id')->values(),
                (int) $filters['class_id']
            )->map(fn ($subject) => $marks->firstWhere('subject_id', $subject->id))->filter()->values();

            $rows = $marks->map(function ($mark) use ($highestMarks, $filters) {
                $tutorialMarks = $mark->subject->getEffectiveMarksForClass((int) $filters['class_id'])['tutorial_marks']
                    ?? $mark->subject->tutorial_marks
                    ?? 0;

                return [
                    'subject_name' => $this->tutorialDisplaySubjectName($mark->subject),
                    'full_marks'   => (float) $tutorialMarks,
                    'obtained'     => $mark->is_absent ? null : (float) $mark->total,
                    'highest'      => (float) ($highestMarks[$mark->subject_id] ?? 0),
                    'is_absent'    => (bool) $mark->is_absent,
                ];
            })->values();

            $totalObtained = $rows->filter(fn ($r) => ! $r['is_absent'])->sum('obtained');
            $failedSubjectCount = $marks->filter(fn ($mark) =>
                $mark->is_absent || $mark->letter_grade === 'F'
            )->count();
            $gpa = GradingService::calculateGpa(
                $marks->map(fn ($mark) => $mark->is_absent ? 0 : (float) $mark->gpa)->values()->all(),
                $failedSubjectCount > 0,
            );
            $percentage = $rows->sum('full_marks') > 0
                ? round(($totalObtained / $rows->sum('full_marks')) * 100, 2)
                : 0.0;

            return [
                'student'        => $student,
                'academicInfo'   => $academicInfo,
                'rows'           => $rows,
                'total_obtained' => $totalObtained,
                'failed_subject_count' => $failedSubjectCount,
                'gpa'            => $gpa,
                'grade'          => GradingService::getGpaLabel($gpa),
                'comment'        => $this->tutorialComment($percentage, $failedSubjectCount),
                'attendance_present' => $attendanceData['present_by_student'][$student->id] ?? 0,
            ];
        });

        $studentsData = $this->rankStudentsData($studentsData);

        $statusMap = $this->buildStatusMap($studentsData->pluck('student.id')->all(), (int) $filters['exam_id']);
        $religionSubjects = $tutorialSubjects->filter(fn ($subject) => $this->isReligionSubject($subject))->values();
        $regularTutorialSubjects = $tutorialSubjects->reject(fn ($subject) => $this->isReligionSubject($subject))->values();
        $includedReligionSubjectIds = $religionSubjects
            ->filter(fn ($subject) => $subject->include_in_tutorial_exam)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        return view('pages.tutorial-report.results', compact(
            'studentsData', 'exam', 'filters', 'statusMap', 'reportContext', 'sections',
            'sessions', 'classes', 'exams', 'tutorialSubjects', 'regularTutorialSubjects',
            'religionSubjects', 'includedReligionSubjectIds'
        ));
    }

    public function pdf(Request $request)
    {
        $request->validate([
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id'   => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'exam_id'    => ['required', 'exists:exams,id'],
            'student_id' => ['nullable', 'string'],
            'subject_filter' => ['nullable', 'boolean'],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['integer'],
            'religion_subject' => ['nullable'],
            'results_per_page' => ['nullable', 'integer', 'in:1,2,3'],
        ]);

        $filters  = $request->only(['session_id', 'class_id', 'section_id', 'exam_id', 'student_id', 'subject_filter', 'subject_ids', 'religion_subject']);
        $filters['results_per_page'] = (int) $request->input('results_per_page', 2);
        $exam     = Exam::with('academicSession')->findOrFail($filters['exam_id']);
        $reportContext = $this->buildReportContext($filters);
        $tutorialSubjects = $this->getAssignedTutorialSubjects((int) $filters['class_id']);
        $filters = $this->normalizeSubjectFilters($request, $filters, $tutorialSubjects);

        if ($exam->type !== Exam::TYPE_TUTORIAL) {
            return back()->with('error', 'Selected exam is not a tutorial exam.');
        }

        $students = $this->getStudents($filters);
        $studentSubjectIds = $students->mapWithKeys(fn ($student) => [
            $student->id => $this->getStudentTutorialSubjectIds((int) $filters['class_id'], $student),
        ]);
        $attendanceData = $this->getAttendanceData($exam, (int) $filters['class_id'], $students->pluck('id'));
        $highestMarks = ExamMark::where('exam_id', $exam->id)
            ->where('is_absent', false)
            ->when($filters['subject_filter'], fn ($query) => $query->whereIn('subject_id', $filters['subject_ids']))
            ->selectRaw('subject_id, MAX(total) as highest')
            ->groupBy('subject_id')
            ->pluck('highest', 'subject_id');

        $studentsData = $students->map(function ($student) use ($exam, $attendanceData, $highestMarks, $filters, $studentSubjectIds) {
            $academicInfo = StudentAcademicInformation::with(['schoolClass', 'section', 'academicSession'])
                ->where('student_id', $student->id)
                ->where('academic_session_id', $exam->academic_session_id)
                ->first();

            $marks = ExamMark::with('subject')
                ->where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->get()
                ->filter(fn ($m) => $m->subject
                    && $m->subject->include_in_tutorial_exam
                    && ($studentSubjectIds[$student->id] === null || $studentSubjectIds[$student->id]->contains((int) $m->subject_id))
                    && (! $filters['subject_filter'] || in_array((int) $m->subject_id, $filters['subject_ids'], true)))
                ->sortBy(fn ($m) => sprintf('%d:%s', $m->subject->is_paper ? 0 : 1, mb_strtolower($m->subject->name)));

            $marks = app(\App\Services\SubjectOrderingService::class)->sortSubjects(
                $marks->map(fn ($mark) => $mark->subject)->unique('id')->values(),
                (int) $filters['class_id']
            )->map(fn ($subject) => $marks->firstWhere('subject_id', $subject->id))->filter()->values();

            $rows = $marks->map(function ($mark) use ($highestMarks, $filters) {
                $tutorialMarks = $mark->subject->getEffectiveMarksForClass((int) $filters['class_id'])['tutorial_marks']
                    ?? $mark->subject->tutorial_marks
                    ?? 0;

                return [
                    'subject_name' => $this->tutorialDisplaySubjectName($mark->subject),
                    'full_marks'   => (float) $tutorialMarks,
                    'obtained'     => $mark->is_absent ? null : (float) $mark->total,
                    'highest'      => (float) ($highestMarks[$mark->subject_id] ?? 0),
                    'is_absent'    => (bool) $mark->is_absent,
                ];
            })->values();

            $totalObtained = $rows->filter(fn ($r) => ! $r['is_absent'])->sum('obtained');
            $failedSubjectCount = $marks->filter(fn ($mark) =>
                $mark->is_absent || $mark->letter_grade === 'F'
            )->count();
            $gpa = GradingService::calculateGpa(
                $marks->map(fn ($mark) => $mark->is_absent ? 0 : (float) $mark->gpa)->values()->all(),
                $failedSubjectCount > 0,
            );
            $percentage = $rows->sum('full_marks') > 0
                ? round(($totalObtained / $rows->sum('full_marks')) * 100, 2)
                : 0.0;

            return [
                'student'        => $student,
                'academicInfo'   => $academicInfo,
                'rows'           => $rows,
                'total_obtained' => $totalObtained,
                'failed_subject_count' => $failedSubjectCount,
                'gpa'            => $gpa,
                'grade'          => GradingService::getGpaLabel($gpa),
                'comment'        => $this->tutorialComment($percentage, $failedSubjectCount),
                'attendance_present' => $attendanceData['present_by_student'][$student->id] ?? 0,
            ];
        });

        $studentsData = $this->rankStudentsData($studentsData);

        $resultsPerPage = $filters['results_per_page'];
        $html = view('pages.tutorial-report.print', compact('studentsData', 'exam', 'filters', 'reportContext', 'resultsPerPage'))->render();
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'margin_top' => 10, 'margin_bottom' => 10, 'margin_left' => 12, 'margin_right' => 12]);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))->header('Content-Type', 'application/pdf');
    }

    public function sendEmail(Request $request)
    {
        $data = $request->validate([
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id'   => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'exam_id'    => ['required', 'exists:exams,id'],
            'student_id' => ['required', 'exists:students,id'],
        ]);

        $exam = Exam::with('academicSession')->findOrFail($data['exam_id']);
        if ($exam->type !== Exam::TYPE_TUTORIAL) {
            return back()->with('error', 'Selected exam is not a tutorial exam.');
        }

        $student = Student::findOrFail($data['student_id']);
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

        $marks = ExamMark::with('subject')
            ->where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->get()
            ->filter(fn ($m) => $m->subject)
            ->sortBy(fn ($m) => sprintf('%d:%s', $m->subject->is_paper ? 0 : 1, mb_strtolower($m->subject->name)));

        $marks = app(\App\Services\SubjectOrderingService::class)->sortSubjects(
            $marks->map(fn ($mark) => $mark->subject)->unique('id')->values(),
            (int) $data['class_id']
        )->map(fn ($subject) => $marks->firstWhere('subject_id', $subject->id))->filter()->values();

        $rows = $marks->map(function ($mark) {
            return [
                'Subject' => $mark->subject->name,
                'Obtained' => $mark->is_absent ? 'AB' : number_format((float) $mark->total, 1),
            ];
        })->values()->all();

        $meta = [
            'Exam' => $exam->name,
            'Session' => $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? ''),
        ];

        foreach ($emails as $email) {
            Mail::to($email)->send(new StudentResultReportMail($student, 'Tutorial Exam Report', $meta, $rows));
        }

        $contextKey = $this->contextKey((int) $data['exam_id'], (int) $student->id);
        ResultEmailStatus::updateOrCreate(
            ['context_key' => $contextKey],
            [
                'report_type' => 'tutorial',
                'student_id' => $student->id,
                'exam_id' => (int) $data['exam_id'],
                'session_id' => (int) $data['session_id'],
                'class_id' => (int) $data['class_id'],
                'section_id' => (int) $data['section_id'],
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
        return "tutorial:exam:{$examId}:student:{$studentId}";
    }

    private function tutorialComment(float $percentage, int $failedSubjectCount): string
    {
        if ($failedSubjectCount > 0 || $percentage < 40.0) {
            return 'Needs Improvement';
        }

        return match (true) {
            $percentage >= 90.0 => 'Excellent',
            $percentage >= 70.0 => 'Good',
            default => 'Satisfactory',
        };
    }

    private function buildReportContext(array $filters): array
    {
        $session = AcademicSession::find($filters['session_id']);
        $class = SchoolClass::find($filters['class_id']);
        $section = Section::find($filters['section_id']);

        return [
            'session' => $session?->name_en ?? ($session?->name_bn ?? '—'),
            'class' => $class?->name_en ?? ($class?->name_bn ?? '—'),
            'section' => $section?->name_en ?? ($section?->name_bn ?? '—'),
        ];
    }

    private function getAssignedTutorialSubjects(int $classId)
    {
        $subjects = SubjectClassAssignment::query()
            ->with('subject')
            ->where('school_class_id', $classId)
            ->where('is_active', true)
            ->get()
            ->map(fn ($assignment) => $assignment->subject)
            ->filter(fn ($subject) => $subject?->is_active)
            ->unique('id')
            ->values();

        return app(\App\Services\SubjectOrderingService::class)
            ->sortSubjects($subjects, $classId);
    }

    private function normalizeSubjectFilters(Request $request, array $filters, $tutorialSubjects): array
    {
        $includedSubjects = $tutorialSubjects
            ->filter(fn ($subject) => $subject->include_in_tutorial_exam)
            ->values();
        $regularIncludedIds = $includedSubjects
            ->reject(fn ($subject) => $this->isReligionSubject($subject))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();
        $includedReligionIds = $includedSubjects
            ->filter(fn ($subject) => $this->isReligionSubject($subject))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();
        $filterActive = $request->has('subject_filter')
            || $request->has('subject_ids')
            || $request->has('religion_subject');
        $religionSelected = $filterActive
            ? in_array(strtolower((string) $request->input('religion_subject', '')), ['1', 'true', 'on', 'religion'], true)
            : $includedReligionIds->isNotEmpty();

        $filters['subject_filter'] = $filterActive;
        $selectedRegularIds = $filterActive
            ? collect($request->input('subject_ids', []))
                ->map(fn ($id) => (int) $id)
                ->intersect($regularIncludedIds)
                ->values()
            : $regularIncludedIds;
        $filters['subject_ids'] = $selectedRegularIds
            ->when($religionSelected, fn ($ids) => $ids->merge($includedReligionIds))
            ->unique()
            ->values()
            ->all();
        $filters['religion_subject'] = $religionSelected;

        return $filters;
    }

    private function isReligionSubject(?Subject $subject): bool
    {
        return $subject !== null
            && preg_match('/^(islam|hindu|christian|buddha|buddhist)\s+religion$/i', trim($subject->name)) === 1;
    }

    private function tutorialDisplaySubjectName(Subject $subject): string
    {
        return $this->isReligionSubject($subject) ? 'Religion' : $subject->name;
    }

    private function getStudentTutorialSubjectIds(int $classId, Student $student): ?\Illuminate\Support\Collection
    {
        $assignments = SubjectClassAssignment::query()
            ->with('subject')
            ->where('school_class_id', $classId)
            ->where('is_active', true)
            ->get();

        if ($assignments->isEmpty()) {
            return null;
        }

        return $assignments
            ->filter(fn ($assignment) => $assignment->subject?->is_active
                && $assignment->subject->include_in_tutorial_exam
                && $assignment->appliesToStudent($student->gender, $student->religion))
            ->pluck('subject_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    private function hasCompleteFilters(Request $request): bool
    {
        return $request->filled('session_id')
            && $request->filled('class_id')
            && $request->filled('section_id')
            && $request->filled('exam_id');
    }

    private function buildStatusMap(array $studentIds, int $examId): array
    {
        if (empty($studentIds)) {
            return [];
        }

        return ResultEmailStatus::query()
            ->where('report_type', 'tutorial')
            ->where('exam_id', $examId)
            ->whereIn('student_id', $studentIds)
            ->pluck('is_sent', 'student_id')
            ->map(fn ($v) => (bool) $v)
            ->all();
    }

    private function rankStudentsData(\Illuminate\Support\Collection $studentsData): \Illuminate\Support\Collection
    {
        $ranked = app(ResultRankingService::class)->rank(
            $studentsData->values()->all(),
            fn (array $row) => [
                'failed_subjects' => $row['failed_subject_count'] ?? 0,
                'total' => $row['total_obtained'] ?? 0,
                'gpa' => $row['gpa'] ?? 0,
                'attendance' => $row['attendance_present'] ?? 0,
            ],
            fn (array $row) => $row['student']->student_cid ?? $row['student']->id,
        );

        return collect($ranked);
    }

    private function getAttendanceData(Exam $exam, int $classId, $studentIds): array
    {
        if (! $exam->start_date || ! $exam->academic_session_id || $studentIds->isEmpty()) {
            return ['present_by_student' => []];
        }

        $periodStart = Carbon::create((int) ($exam->year ?: $exam->start_date->year), 1, 1)->startOfDay();
        $pairNo = (int) ($exam->pair_no ?: 1);
        if ($pairNo > 1) {
            $previousExam = Exam::query()
                ->where('academic_session_id', $exam->academic_session_id)
                ->where('type', Exam::TYPE_TUTORIAL)
                ->where('pair_no', $pairNo - 1)
                ->first();
            if (! $previousExam?->end_date) {
                return ['present_by_student' => []];
            }
            $periodStart = $previousExam->end_date->copy()->addDay()->startOfDay();
        }

        $periodEnd = $exam->start_date->copy()->subDay()->endOfDay();
        if ($periodStart->greaterThan($periodEnd)) {
            return ['present_by_student' => []];
        }

        $attendanceIds = Attendance::query()
            ->where('session_id', $exam->academic_session_id)
            ->where('class_id', $classId)
            ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->pluck('id');

        return [
            'present_by_student' => AttendanceItem::query()
                ->whereIn('attendance_id', $attendanceIds)
                ->whereIn('student_id', $studentIds)
                ->where('status', 'present')
                ->selectRaw('student_id, COUNT(*) as present_days')
                ->groupBy('student_id')
                ->pluck('present_days', 'student_id')
                ->map(fn ($days) => (int) $days)
                ->all(),
        ];
    }

    private function getStudents(array $filters)
    {
        if (! empty($filters['student_id'])) {
            return Student::where(function ($query) use ($filters) {
                    $query->where('id', $filters['student_id'])
                        ->orWhere('student_cid', $filters['student_id']);
                })
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

        return Student::whereIn('id', $ids)->orderBy('id')->get();
    }
}
