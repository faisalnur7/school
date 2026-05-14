<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
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

        return view('pages.progress-report.results', compact('studentsData', 'exam', 'school', 'gradeScale', 'filters'));
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
            $fullMarks   = $examSubject ? (float) $examSubject->full_marks : 0;
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
                $parentGroups[$subject->parent_id]['papers'][] = $row;
            } else {
                $subjectRows[$subject->id] = $row;
            }
        }

        // Merge paper groups into parent rows
        foreach ($parentGroups as $parentId => $group) {
            $papers    = $group['papers'];
            $anyFail   = collect($papers)->contains(fn($p) => $p['paper_fail'] || $p['is_absent']);
            $totalFull = collect($papers)->sum('full_marks');
            $totalObt  = $anyFail ? null : collect($papers)->sum('obtained');
            $highest   = collect($papers)->max('highest');
            $gpas      = collect($papers)->filter(fn($p) => !$p['is_absent'])->pluck('gpa')->toArray();
            $gpa       = $anyFail ? 0 : GradingService::calculateGpa($gpas);
            $grade     = $anyFail ? 'F' : GradingService::getGpaLabel($gpa);

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
                'paper_fail'   => $anyFail,
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
            'subjectRows'       => array_values($subjectRows),
            'summary'           => compact('fullMarks', 'obtained', 'percentage', 'gpa', 'grade'),
            'attendancePresent' => $attendancePresent,
            'attendanceTotal'   => $attendanceTotal,
        ];
    }
}
