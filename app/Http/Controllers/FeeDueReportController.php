<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\FeeCategory;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class FeeDueReportController extends Controller
{
    public function index(Request $request)
    {
        [$sessions, $classes, $classSections, $categories, $grandTotals] = $this->buildData($request);

        return view('pages.fee-due-report.index', compact('sessions', 'classes', 'classSections', 'categories', 'grandTotals'));
    }

    public function pdf(Request $request)
    {
        [, , $classSections, $categories, $grandTotals] = $this->buildData($request);

        $session = AcademicSession::find($request->session_id);
        $month   = $request->filled('month') ? date('F', mktime(0, 0, 0, $request->month, 1)) : null;

        $html = view('pages.fee-due-report.pdf', compact('classSections', 'categories', 'grandTotals', 'session', 'month'))->render();

        $mpdf = new Mpdf(['margin_top' => 10, 'margin_bottom' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('fee-due-report.pdf', 'D');
    }

    private function buildData(Request $request): array
    {
        $sessions   = AcademicSession::orderByDesc('id')->get();
        $classes    = SchoolClass::get();
        $classSections = collect();
        $categories = collect();
        $grandTotals = ['fees' => 0, 'paid' => 0, 'due' => 0];

        if (!$request->filled('session_id')) {
            return [$sessions, $classes, $classSections, $categories, $grandTotals];
        }

        $students = Student::query()
            ->with([
                'academicInformations' => fn($q) => $q
                    ->where('academic_session_id', $request->session_id)
                    ->with(['schoolClass', 'section']),
                'fees' => fn($q) => $q
                    ->whereHas('feeSet', fn($fs) => $fs->where('academic_session_id', $request->session_id))
                    ->when($request->filled('month'), fn($q) =>
                        $q->whereMonth('due_date', $request->month)
                    )
                    ->where('is_active', 1)
                    ->with([
                        'paymentItems',
                        'feeSet.items.category',
                    ]),
            ])
            ->whereHas('academicInformations', fn($q) =>
                $q->where('academic_session_id', $request->session_id)
            )
            ->whereHas('fees', fn($q) =>
                $q->whereHas('feeSet', fn($fs) => $fs->where('academic_session_id', $request->session_id))
                  ->where('is_active', 1)
            )
            ->get();

        // Collect all unique categories used across all fee sets
        $allCategories = collect();
        foreach ($students as $student) {
            foreach ($student->fees as $fee) {
                foreach ($fee->feeSet->items as $item) {
                    if ($item->category) {
                        $allCategories->put($item->category->id, $item->category);
                    }
                }
            }
        }
        $categories = $allCategories->sortBy('name');

        // Group students by class+section
        $grouped = $students->groupBy(function ($s) {
            $ai = $s->academicInformations->first();
            return ($ai?->school_class_id ?? 0) . '|' . ($ai?->section_id ?? 0);
        });

        $classSections = $grouped->map(function ($sectionStudents) use ($categories) {
            $ai = $sectionStudents->first()->academicInformations->first();

            // Per-category totals for this class+section
            $catTotals = [];
            foreach ($categories as $cat) {
                $catTotals[$cat->id] = ['fees' => 0, 'paid' => 0, 'due' => 0];
            }

            $totalFees = 0;
            $totalPaid = 0;

            foreach ($sectionStudents as $student) {
                foreach ($student->fees as $fee) {
                    $netAmount = (float)$fee->amount - (float)$fee->scholarship_discount;
                    $paidAmount = $fee->paymentItems->sum('amount');
                    $totalFees += $netAmount;
                    $totalPaid += $paidAmount;

                    // Distribute paid proportionally across categories in this fee set
                    $feeSetItems = $fee->feeSet->items;
                    $feeSetTotal = $feeSetItems->sum('amount');

                    foreach ($feeSetItems as $item) {
                        if (!$item->category) continue;
                        $catId = $item->category->id;
                        $catShare = $feeSetTotal > 0 ? ($item->amount / $feeSetTotal) : 0;
                        $catFee = $netAmount * $catShare;
                        $catPaid = $paidAmount * $catShare;
                        $catTotals[$catId]['fees'] += $catFee;
                        $catTotals[$catId]['paid'] += $catPaid;
                        $catTotals[$catId]['due']  += max(0, $catFee - $catPaid);
                    }
                }
            }

            $due = max(0, $totalFees - $totalPaid);

            return (object)[
                'class_id'     => $ai?->school_class_id,
                'class_name'   => $ai?->schoolClass?->name_en ?? '—',
                'section_id'   => $ai?->section_id,
                'section_name' => $ai?->section?->name_en ?? '—',
                'total_fees'   => $totalFees,
                'total_paid'   => $totalPaid,
                'due'          => $due,
                'cat_totals'   => $catTotals,
            ];
        })
        ->sortBy(fn($r) => $r->class_name . $r->section_name)
        ->values();

        $grandTotals = [
            'fees' => $classSections->sum('total_fees'),
            'paid' => $classSections->sum('total_paid'),
            'due'  => $classSections->sum('due'),
        ];

        return [$sessions, $classes, $classSections, $categories, $grandTotals];
    }
}
