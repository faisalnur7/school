<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class FeeDueReportController extends Controller
{
    public function index(Request $request)
    {
        [$sessions, $classes, $rows, $mode] = $this->buildData($request);

        return view('pages.fee-due-report.index', compact('sessions', 'classes', 'rows', 'mode'));
    }

    public function pdf(Request $request)
    {
        [, , $rows, $mode] = $this->buildData($request);

        $session = AcademicSession::find($request->session_id);
        $month   = $request->filled('month') ? date('F', mktime(0, 0, 0, $request->month, 1)) : null;

        $html = view('pages.fee-due-report.pdf', compact('rows', 'mode', 'session', 'month'))->render();

        $mpdf = new Mpdf(['margin_top' => 10, 'margin_bottom' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('fee-due-report.pdf', 'D');
    }

    private function buildData(Request $request): array
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes  = SchoolClass::orderBy('name_en')->get();
        $rows     = collect();
        $mode     = null;

        if (!$request->filled('session_id')) {
            return [$sessions, $classes, $rows, $mode];
        }

        $mode = $request->filled('month') ? 'monthly' : 'yearly';

        $students = Student::query()
            ->with([
                'academicInformations' => fn($q) => $q
                    ->where('academic_session_id', $request->session_id)
                    ->with(['schoolClass', 'section', 'group']),
                'fees' => fn($q) => $q
                    ->whereHas('feeSet', fn($fs) => $fs->where('academic_session_id', $request->session_id))
                    ->when($request->filled('month'), fn($q) =>
                        $q->whereMonth('due_date', $request->month)
                    )
                    ->where('is_active', 1)
                    ->with('paymentItems'),
            ])
            ->whereHas('academicInformations', fn($q) =>
                $q->where('academic_session_id', $request->session_id)
            )
            ->whereHas('fees', fn($q) =>
                $q->whereHas('feeSet', fn($fs) => $fs->where('academic_session_id', $request->session_id))
                  ->where('is_active', 1)
            )
            ->get();

        $studentData = $students->map(function (Student $s) {
            $ai = $s->academicInformations->first();
            $totalFees = $s->fees->sum(fn($f) => (float)$f->amount - (float)$f->scholarship_discount);
            $totalPaid = $s->fees->sum(fn($f) => $f->paymentItems->sum('amount'));
            return (object)[
                'class_id'     => $ai?->school_class_id,
                'class_name'   => $ai?->schoolClass?->name_en ?? '—',
                'section_id'   => $ai?->section_id,
                'section_name' => $ai?->section?->name_en ?? '—',
                'group_id'     => $ai?->group_id,
                'group_name'   => $ai?->group?->name_en ?? '—',
                'total_fees'   => $totalFees,
                'total_paid'   => $totalPaid,
                'due'          => max(0, $totalFees - $totalPaid),
            ];
        });

        if ($mode === 'yearly') {
            $rows = $studentData
                ->groupBy('class_id')
                ->map(fn($g) => (object)[
                    'class_name'   => $g->first()->class_name,
                    'section_name' => null,
                    'group_name'   => null,
                    'total_fees'   => $g->sum('total_fees'),
                    'total_paid'   => $g->sum('total_paid'),
                    'due'          => $g->sum('due'),
                ])
                ->sortBy('class_name')
                ->values();
        } else {
            $rows = $studentData
                ->groupBy(fn($s) => $s->class_id . '|' . $s->section_id . '|' . $s->group_id)
                ->map(fn($g) => (object)[
                    'class_name'   => $g->first()->class_name,
                    'section_name' => $g->first()->section_name,
                    'group_name'   => $g->first()->group_name,
                    'total_fees'   => $g->sum('total_fees'),
                    'total_paid'   => $g->sum('total_paid'),
                    'due'          => $g->sum('due'),
                ])
                ->sortBy(fn($r) => $r->class_name . $r->section_name . $r->group_name)
                ->values();
        }

        return [$sessions, $classes, $rows, $mode];
    }
}
