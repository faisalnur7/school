<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use Illuminate\Http\Request;

class TutorialReportController extends Controller
{
    public function index()
    {
        return view('pages.tutorial-report.index', [
            'sessions' => AcademicSession::orderByDesc('id')->get(),
            'classes'  => SchoolClass::all(),
            'exams'    => Exam::where('type', Exam::TYPE_TUTORIAL)->orderByDesc('id')->get(),
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

        if ($exam->type !== Exam::TYPE_TUTORIAL) {
            return back()->with('error', 'Selected exam is not a tutorial exam.');
        }

        $students = $this->getStudents($filters);

        $studentsData = $students->map(function ($student) use ($exam) {
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
                'rows'           => $rows,
                'total_obtained' => $totalObtained,
            ];
        });

        return view('pages.tutorial-report.results', compact('studentsData', 'exam', 'filters'));
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

        if ($exam->type !== Exam::TYPE_TUTORIAL) {
            return back()->with('error', 'Selected exam is not a tutorial exam.');
        }

        $students = $this->getStudents($filters);

        $studentsData = $students->map(function ($student) use ($exam) {
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
                'rows'           => $rows,
                'total_obtained' => $totalObtained,
            ];
        });

        $html = view('pages.tutorial-report.print', compact('studentsData', 'exam', 'filters'))->render();
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'margin_top' => 15, 'margin_bottom' => 15, 'margin_left' => 15, 'margin_right' => 15]);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))->header('Content-Type', 'application/pdf');
    }

    private function getStudents(array $filters)
    {
        if (! empty($filters['student_id'])) {
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
}

