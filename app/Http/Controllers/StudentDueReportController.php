<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Services\StudentDueSummaryService;
use App\Services\StudentMonthwisePaymentReportService;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class StudentDueReportController extends Controller
{
    public function index(Request $request, StudentDueSummaryService $service)
    {
        [$sessions, $classes, $sections, $rows, $totals] = $service->build($request);

        return view('pages.student-due-report.index', compact(
            'sessions', 'classes', 'sections', 'rows', 'totals'
        ));
    }

    public function pdf(Request $request, StudentDueSummaryService $service)
    {
        [, , , $rows, $totals] = $service->build($request);

        $session = AcademicSession::find($request->session_id);

        $html = view('pages.student-due-report.pdf', compact('rows', 'session', 'totals'))->render();

        $mpdf = new Mpdf(['orientation' => 'L', 'margin_top' => 10, 'margin_bottom' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('students-due-report.pdf', 'D');
    }

    public function monthwisePdf(Request $request, StudentMonthwisePaymentReportService $service)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'session_id' => 'required|exists:academic_sessions,id',
            'class_id' => 'nullable|exists:school_classes,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $report = $service->build(
            (int) $validated['student_id'],
            (int) $validated['session_id'],
            isset($validated['class_id']) ? (int) $validated['class_id'] : null,
            isset($validated['section_id']) ? (int) $validated['section_id'] : null,
        );

        $html = view('pages.student-due-report.monthwise-pdf', $report)->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 9,
            'margin_right' => 9,
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('student-monthwise-payment-report.pdf', 'D');
    }
}
