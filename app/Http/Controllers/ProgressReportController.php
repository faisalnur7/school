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
use App\Models\StudentAcademicInformation;
use App\Services\GradingService;
use Illuminate\Support\Facades\Mail;

class ProgressReportController extends Controller
{
    public function index()
    {
        return view('pages.progress-report.index', [
            'sessions' => AcademicSession::orderByDesc('id')->get(),
            'classes'  => SchoolClass::all(),
            'exams'    => Exam::orderByDesc('id')->get(),
        ]);
    }

    public function show(Request $request)
    {
        $request->validate([
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id'   => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'exam_id'    => ['required', 'exists:exams,id'],
            'student_id' => ['nullable'],
        ]);

        $filters  = $request->only(['session_id', 'class_id', 'section_id', 'exam_id', 'student_id']);
        $exam     = Exam::with('academicSession')->findOrFail($filters['exam_id']);
        $school   = SchoolSetting::current();
        $gradeScale = GradingService::allGrades();

        $students = $this->getStudents($filters);
        $studentsData = $students->map(fn($s) => $this->buildStudentData($s, $exam, $filters));
        $statusMap = $this->buildStatusMap($studentsData->pluck('student.id')->all(), (int) $filters['exam_id']);

        return view('pages.progress-report.results', compact('studentsData', 'exam', 'school', 'gradeScale', 'filters', 'statusMap'));
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
        $exam     = Exam::with('academicSession')->findOrFail($filters['exam_id']);
        $school   = SchoolSetting::current();
        $gradeScale = GradingService::allGrades();

        $students = $this->getStudents($filters);
        $studentsData = $students->map(fn($s) => $this->buildStudentData($s, $exam, $filters));

        $html = view('pages.progress-report.print', compact('studentsData', 'exam', 'school', 'gradeScale', 'filters'))->render();

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'margin_top' => 15, 'margin_bottom' => 15, 'margin_left' => 15, 'margin_right' => 15]);
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

    private function getStudents(array $filters)
    {
        if (!empty($filters['student_id'])) {
            return Student::where('id', $filters['student_id'])
                ->orWhere('student_cid', $filters['student_id'])
                ->get();
        }

        $ids = StudentAcademicInformation::where('academic_session_id', $filters['session_id'])
            ->where('school_class_id', $filters['class_id'])
            ->where('section_id', $filters['section_id'])
            ->pluck('student_id');

        return Student::whereIn('id', $ids)->orderBy('id')->get();
    }

    private function buildStudentData(Student $student, Exam $exam, array $filters): array
    {
        $academicInfo = StudentAcademicInformation::with(['schoolClass', 'section', 'academicSession'])
            ->where('student_id', $student->id)
            ->where('academic_session_id', $filters['session_id'])
            ->first();

        $marks = ExamMark::with(['subject'])
            ->where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->get();

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
            $highest     = (float) ($highestMarks[$subject->id] ?? 0);

            $row = [
                'subject_id'   => $subject->id,
                'subject_name' => $subject->name,
                'is_paper'     => (bool) $subject->is_paper,
                'parent_id'    => $subject->parent_id,
                'full_marks'   => $fullMarks,
                'obtained'     => $mark->is_absent ? null : (float) $mark->total,
                'highest'      => $highest,
                'grade'        => $mark->is_absent ? 'AB' : $mark->letter_grade,
                'gpa'          => $mark->is_absent ? null : (float) $mark->gpa,
                'is_absent'    => (bool) $mark->is_absent,
                'paper_fail'   => !$mark->is_absent && ($mark->gpa == 0 || $mark->letter_grade === 'F'),
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
        $gpas       = $validRows->filter(fn($r) => !($r['paper_fail'] ?? false))->pluck('gpa')->toArray();
        $gpa        = GradingService::calculateGpa($gpas);
        $grade      = GradingService::getGpaLabel($gpa);

        // Attendance
        $attendanceIds = Attendance::where('session_id', $filters['session_id'])
            ->where('class_id', $filters['class_id'])
            ->where('section_id', $filters['section_id'])
            ->pluck('id');

        $attendanceTotal   = $attendanceIds->count();
        $attendancePresent = AttendanceItem::whereIn('attendance_id', $attendanceIds)
            ->where('student_id', $student->id)
            ->where('status', 'present')
            ->count();

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
}
