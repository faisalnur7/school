<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Services\StudentDueSummaryService;
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
}
