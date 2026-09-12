<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Services\StudentMonthwisePaymentReportService;
use App\Services\StudentPaymentLedgerService;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class StudentLedgerReportController extends Controller
{
    public function __construct(private StudentPaymentLedgerService $ledgerService) {}

    public function index(Request $request, StudentMonthwisePaymentReportService $monthwiseService)
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes = SchoolClass::orderBy('order')->orderBy('id')->get();
        $sections = $request->filled('class_id')
            ? Section::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();
        $reports = collect();

        if ($request->filled('session_id')) {
            $students = Student::query()
                ->whereHas('academicInformations', function ($query) use ($request) {
                    $query->where('academic_session_id', $request->session_id)
                        ->when($request->filled('class_id'), fn ($q) => $q->where('school_class_id', $request->class_id))
                        ->when($request->filled('section_id'), fn ($q) => $q->where('section_id', $request->section_id));
                })
                ->when($request->filled('student_id'), function ($query) use ($request) {
                    $value = trim((string) $request->student_id);
                    $query->where(function ($q) use ($value) {
                        $q->where('student_cid', $value);
                        if (is_numeric($value)) $q->orWhere('id', (int) $value);
                    });
                })
                ->orderBy('full_name_en')
                ->get();

            $reports = $students->map(function ($student) use ($monthwiseService, $request) {
                return $monthwiseService->build(
                    $student->id,
                    (int) $request->session_id,
                    $request->filled('class_id') ? (int) $request->class_id : null,
                    $request->filled('section_id') ? (int) $request->section_id : null,
                );
            })->values();
        }

        return view('pages.student-ledger-report.index', compact('sessions', 'classes', 'sections', 'reports'));
    }

    public function show(Request $request, int $studentId)
    {
        $request->validate(['session_id' => 'required|integer|exists:academic_sessions,id']);

        $student = Student::with([
            'academicInformations' => fn($q) => $q
                ->where('academic_session_id', $request->session_id)
                ->with(['schoolClass', 'section', 'group']),
        ])->findOrFail($studentId);

        $session = AcademicSession::findOrFail($request->session_id);
        $ledger  = $this->ledgerService->build($student, (int) $request->session_id);

        if ($request->wantsJson()) {
            return response()->json($ledger);
        }

        return view('pages.student-ledger-report.show', array_merge($ledger, [
            'session' => $session,
        ]));
    }

    public function pdf(Request $request, int $studentId)
    {
        $request->validate(['session_id' => 'required|integer|exists:academic_sessions,id']);

        $student = Student::with([
            'academicInformations' => fn($q) => $q
                ->where('academic_session_id', $request->session_id)
                ->with(['schoolClass', 'section', 'group']),
        ])->findOrFail($studentId);

        $session = AcademicSession::findOrFail($request->session_id);
        $school  = SchoolSetting::current();
        $ledger  = $this->ledgerService->build($student, (int) $request->session_id);

        $html = view('pages.student-ledger-report.pdf', array_merge($ledger, [
            'session' => $session,
            'school'  => $school,
        ]))->render();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'margin_top' => 10, 'margin_bottom' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('student-ledger-' . $student->student_cid . '.pdf', 'D');
    }
}
