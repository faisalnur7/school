<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Services\YearlyFinalReportService;
use Illuminate\Http\Request;

class YearlyFinalReportController extends Controller
{
    public function index(YearlyFinalReportService $service)
    {
        return view('pages.yearly-final-report.index', [
            'sessions'     => AcademicSession::orderByDesc('id')->get(),
            'classes'      => SchoolClass::where('status', 1)->orderBy('id')->get(),
            'rows'         => collect(),
            'pairWeights'  => [],
            'highest'      => 0,
            'filters'      => [],
        ]);
    }

    public function show(Request $request, YearlyFinalReportService $service)
    {
        $filters = $request->validate([
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id'   => ['required', 'exists:school_classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'student_id' => ['nullable'],
        ]);

        $report = $service->buildReport(
            $filters['session_id'],
            $filters['class_id'],
            $filters['section_id'] ?? null,
            $filters['student_id'] ?? null,
        );

        return view('pages.yearly-final-report.index', array_merge($report, [
            'sessions' => AcademicSession::orderByDesc('id')->get(),
            'classes'  => SchoolClass::where('status', 1)->orderBy('id')->get(),
            'filters'  => $filters,
        ]));
    }

    public function pdf(Request $request, YearlyFinalReportService $service)
    {
        $filters = $request->validate([
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id'   => ['required', 'exists:school_classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'student_id' => ['nullable'],
        ]);

        $report = $service->buildReport(
            $filters['session_id'],
            $filters['class_id'],
            $filters['section_id'] ?? null,
            $filters['student_id'] ?? null,
        );

        $html = view('pages.yearly-final-report.print', array_merge($report, [
            'filters' => $filters,
        ]))->render();

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'margin_top' => 15, 'margin_bottom' => 15, 'margin_left' => 15, 'margin_right' => 15]);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))->header('Content-Type', 'application/pdf');
    }
}
