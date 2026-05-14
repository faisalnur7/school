<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Services\StudentPaymentLedgerService;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class StudentLedgerReportController extends Controller
{
    public function __construct(private StudentPaymentLedgerService $ledgerService) {}

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
