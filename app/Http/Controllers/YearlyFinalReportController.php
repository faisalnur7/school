<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\StudentResultReportMail;
use App\Models\AcademicSession;
use App\Models\ResultEmailStatus;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\YearlyFinalReportTemplateSetting;
use App\Services\YearlyFinalReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
            'templateSettings' => YearlyFinalReportTemplateSetting::current(),
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
            'templateSettings' => YearlyFinalReportTemplateSetting::current(),
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
            'templateSettings' => YearlyFinalReportTemplateSetting::current(),
        ]))->render();

        $templateSettings = YearlyFinalReportTemplateSetting::current();
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'orientation' => $templateSettings->paper_orientation === 'landscape' ? 'L' : 'P',
            'margin_top' => $templateSettings->margin_top_mm,
            'margin_bottom' => $templateSettings->margin_bottom_mm,
            'margin_left' => $templateSettings->margin_left_mm,
            'margin_right' => $templateSettings->margin_right_mm,
        ]);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))->header('Content-Type', 'application/pdf');
    }

    public function sendEmail(Request $request, YearlyFinalReportService $service)
    {
        $filters = $request->validate([
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id'   => ['required', 'exists:school_classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'student_id' => ['required', 'exists:students,id'],
        ]);

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

        $report = $service->buildReport(
            $filters['session_id'],
            $filters['class_id'],
            $filters['section_id'] ?? null,
            $filters['student_id']
        );

        $row = collect($report['rows'] ?? [])->firstWhere('student.id', $student->id);
        if (! $row) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'No yearly result row found for this student.'], 422);
            }
            return back()->with('error', 'No yearly result row found for this student.');
        }

        $rows = [[
            'Pair 1 Total' => data_get($row, 'totals.1.total', 0),
            'Pair 1 Weighted' => data_get($row, 'totals.1.weighted', 0),
            'Pair 2 Total' => data_get($row, 'totals.2.total', 0),
            'Pair 2 Weighted' => data_get($row, 'totals.2.weighted', 0),
            'Pair 3 Total' => data_get($row, 'totals.3.total', 0),
            'Pair 3 Weighted' => data_get($row, 'totals.3.weighted', 0),
            'Grand Total' => $row['grand_total'] ?? 0,
            'Position' => $row['position'] ?? '-',
        ]];

        $meta = [
            'Highest Grand Total' => $report['highest'] ?? 0,
        ];

        foreach ($emails as $email) {
            Mail::to($email)->send(new StudentResultReportMail($student, 'Yearly Final Report', $meta, $rows));
        }

        $contextKey = $this->contextKey($filters, (int) $student->id);
        ResultEmailStatus::updateOrCreate(
            ['context_key' => $contextKey],
            [
                'report_type' => 'yearly_final',
                'student_id' => $student->id,
                'exam_id' => null,
                'session_id' => (int) $filters['session_id'],
                'class_id' => (int) $filters['class_id'],
                'section_id' => ! empty($filters['section_id']) ? (int) $filters['section_id'] : null,
                'is_sent' => true,
                'sent_at' => now(),
            ]
        );

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Result email sent to parent(s).']);
        }

        return back()->with('success', 'Result email sent to parent(s).');
    }

    private function contextKey(array $filters, int $studentId): string
    {
        $sectionId = $filters['section_id'] ?? 'all';
        return "yearly_final:session:{$filters['session_id']}:class:{$filters['class_id']}:section:{$sectionId}:student:{$studentId}";
    }

    private function buildStatusMap(array $studentIds, array $filters): array
    {
        if (empty($studentIds)) {
            return [];
        }

        $query = ResultEmailStatus::query()
            ->where('report_type', 'yearly_final')
            ->where('session_id', (int) $filters['session_id'])
            ->where('class_id', (int) $filters['class_id'])
            ->whereIn('student_id', $studentIds);

        if (! empty($filters['section_id'])) {
            $query->where('section_id', (int) $filters['section_id']);
        } else {
            $query->whereNull('section_id');
        }

        return $query->pluck('is_sent', 'student_id')
            ->map(fn ($v) => (bool) $v)
            ->all();
    }
}
