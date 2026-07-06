<?php

namespace App\Http\Controllers;

use App\Mail\StudentResultReportMail;
use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ResultEmailStatus;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
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
            'filters'  => $request->only(['session_id', 'class_id', 'section_id', 'exam_id', 'student_id']),
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
        ]);

        $filters  = $request->only(['session_id', 'class_id', 'section_id', 'exam_id', 'student_id']);
        $exam     = Exam::with('academicSession')->findOrFail($filters['exam_id']);
        $reportContext = $this->buildReportContext($filters);
        $sections = Section::where('school_class_id', $filters['class_id'])->orderBy('name_en')->get();

        if ($exam->type !== Exam::TYPE_TUTORIAL) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'Selected exam is not a tutorial exam.'], 422);
            }
            return back()->with('error', 'Selected exam is not a tutorial exam.');
        }

        $students = $this->getStudents($filters);
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes = SchoolClass::all();
        $exams = Exam::where('type', Exam::TYPE_TUTORIAL)->orderByDesc('id')->get();

        $studentsData = $students->map(function ($student) use ($exam) {
            $academicInfo = StudentAcademicInformation::with(['schoolClass', 'section', 'academicSession'])
                ->where('student_id', $student->id)
                ->where('academic_session_id', $exam->academic_session_id)
                ->first();

            $marks = ExamMark::with('subject')
                ->where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->get()
                ->filter(fn ($m) => $m->subject)
                ->sortBy(fn ($m) => $m->subject->name);

            $rows = $marks->map(function ($mark) {
                return [
                    'subject_name' => $mark->subject->name,
                    'obtained'     => $mark->is_absent ? null : (float) $mark->total,
                    'is_absent'    => (bool) $mark->is_absent,
                ];
            })->values();

            $totalObtained = $rows->filter(fn ($r) => ! $r['is_absent'])->sum('obtained');

            return [
                'student'        => $student,
                'academicInfo'   => $academicInfo,
                'rows'           => $rows,
                'total_obtained' => $totalObtained,
            ];
        });

        $statusMap = $this->buildStatusMap($studentsData->pluck('student.id')->all(), (int) $filters['exam_id']);

        return view('pages.tutorial-report.results', compact('studentsData', 'exam', 'filters', 'statusMap', 'reportContext', 'sections', 'sessions', 'classes', 'exams'));
    }

    public function pdf(Request $request)
    {
        $request->validate([
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id'   => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'exam_id'    => ['required', 'exists:exams,id'],
            'student_id' => ['nullable', 'string'],
        ]);

        $filters  = $request->only(['session_id', 'class_id', 'section_id', 'exam_id', 'student_id']);
        $exam     = Exam::with('academicSession')->findOrFail($filters['exam_id']);
        $reportContext = $this->buildReportContext($filters);

        if ($exam->type !== Exam::TYPE_TUTORIAL) {
            return back()->with('error', 'Selected exam is not a tutorial exam.');
        }

        $students = $this->getStudents($filters);

        $studentsData = $students->map(function ($student) use ($exam) {
            $academicInfo = StudentAcademicInformation::with(['schoolClass', 'section', 'academicSession'])
                ->where('student_id', $student->id)
                ->where('academic_session_id', $exam->academic_session_id)
                ->first();

            $marks = ExamMark::with('subject')
                ->where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->get()
                ->filter(fn ($m) => $m->subject)
                ->sortBy(fn ($m) => $m->subject->name);

            $rows = $marks->map(function ($mark) {
                return [
                    'subject_name' => $mark->subject->name,
                    'obtained'     => $mark->is_absent ? null : (float) $mark->total,
                    'is_absent'    => (bool) $mark->is_absent,
                ];
            })->values();

            $totalObtained = $rows->filter(fn ($r) => ! $r['is_absent'])->sum('obtained');

            return [
                'student'        => $student,
                'academicInfo'   => $academicInfo,
                'rows'           => $rows,
                'total_obtained' => $totalObtained,
            ];
        });

        $html = view('pages.tutorial-report.print', compact('studentsData', 'exam', 'filters', 'reportContext'))->render();
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'margin_top' => 15, 'margin_bottom' => 15, 'margin_left' => 15, 'margin_right' => 15]);
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
            ->sortBy(fn ($m) => $m->subject->name);

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
