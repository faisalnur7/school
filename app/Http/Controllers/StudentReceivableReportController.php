<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeSet;
use App\Models\SchoolClass;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class StudentReceivableReportController extends Controller
{
    public function index(Request $request)
    {
        [$sessions, $classes, $sections, $rows, $months, $categories, $availableCategories, $totals, $fromDate, $toDate, $selectedCategoryKeys] = $this->buildData($request);

        return view('pages.student-receivable-report.index', compact(
            'sessions', 'classes', 'sections', 'rows', 'months', 'categories', 'availableCategories', 'totals', 'fromDate', 'toDate', 'selectedCategoryKeys'
        ));
    }

    public function pdf(Request $request)
    {
        [, , , $rows, $months, $categories, , $totals, $fromDate, $toDate] = $this->buildData($request);

        $html = view('pages.student-receivable-report.pdf', compact(
            'rows', 'months', 'categories', 'totals', 'fromDate', 'toDate'
        ))->render();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 'margin_top' => 10, 'margin_bottom' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('student-receivable-report.pdf', 'D');
    }

    public function buildData(Request $request): array
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes  = SchoolClass::orderBy('id')->get();
        $sections = $request->filled('class_id')
            ? Section::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();

        $rows       = collect();
        $months     = [];
        $categories = collect();
        $availableCategories = collect();
        $selectedCategoryKeys = [];
        $totals     = ['months' => [], 'categories' => [], 'total' => 0.0];
        $fromDate   = $request->filled('from_date') ? Carbon::parse($request->from_date) : null;
        $toDate     = $request->filled('to_date')   ? Carbon::parse($request->to_date)   : null;

        if (!$fromDate || !$toDate) {
            return [$sessions, $classes, $sections, $rows, $months, $categories, $availableCategories, $totals,
                $fromDate?->toDateString(), $toDate?->toDateString(), $selectedCategoryKeys];
        }

        if ($toDate->lt($fromDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        // Build month columns
        $current = $fromDate->copy()->startOfMonth();
        $end     = $toDate->copy()->startOfMonth();
        while ($current->lte($end)) {
            $key = $current->format('Y-m');
            $months[$key] = $current->format('M-y');
            $totals['months'][$key] = 0.0;
            $current->addMonth();
        }

        if (empty($months)) {
            return [$sessions, $classes, $sections, $rows, $months, $categories, $availableCategories, $totals,
                $fromDate->toDateString(), $toDate->toDateString(), $selectedCategoryKeys];
        }

        // Collect all fee categories defined for the selected scope.
        $categoryQuery = FeeSet::with('items.category');
        if ($request->filled('session_id')) {
            $categoryQuery->where('academic_session_id', $request->session_id);
        }
        if ($request->filled('class_id')) {
            $categoryQuery->where('school_class_id', $request->class_id);
        }

        $allCategories = collect();
        foreach ($categoryQuery->get() as $feeSet) {
            foreach ($feeSet->items as $item) {
                if ($item->category && $item->category->status) {
                    $allCategories->put($item->category->id, $item->category);
                }
            }
        }
        $availableCategories = $allCategories->sortBy('name')->values();
        $selectedCategoryKeys = $this->resolveSelectedReceivableReportColumns($request, $availableCategories);
        $selectedCategoryLookup = array_flip($selectedCategoryKeys);
        $categories = $availableCategories
            ->filter(fn ($category) => isset($selectedCategoryLookup[$category->id]))
            ->values();

        // Query active fees with due_date in range
        $feesQuery = Fee::with([
                'student.academicInformations.schoolClass',
                'student.academicInformations.section',
                'feeSet.items.category',
            ])
            ->where('is_active', 1)
            ->whereBetween('due_date', [$fromDate->toDateString(), $toDate->toDateString()]);

        if ($request->filled('student_id')) {
            $studentId = trim((string) $request->student_id);
            $feesQuery->whereHas('student', function ($q) use ($studentId) {
                $q->where('student_cid', $studentId);
                if (is_numeric($studentId)) {
                    $q->orWhere('id', $studentId);
                }
            });
        }

        if ($request->filled('session_id')) {
            $feesQuery->whereHas('feeSet', fn($q) => $q->where('academic_session_id', $request->session_id));
        }

        $fees = $feesQuery->get();

        // Init category totals
        foreach ($categories as $cat) {
            $totals['categories'][$cat->id] = array_fill_keys(array_keys($months), 0.0);
        }

        // Pre-load all academic info counts per student to determine new vs old
        $studentAcademicCounts = \App\Models\StudentAcademicInformation::selectRaw('student_id, COUNT(*) as total')
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        // Build student map: [studentId => [monthKey => [catId => amount]]]
        $studentMap = [];

        foreach ($fees as $fee) {
            $student = $fee->student;
            if (!$student) continue;

            $academicInfo = $student->academicInformations
                ->when($request->filled('session_id'), fn($c) => $c->where('academic_session_id', $request->session_id))
                ->first() ?? $student->academicInformations->first();

            if ($request->filled('session_id') && !$academicInfo) continue;
            if ($request->filled('class_id') && $academicInfo?->school_class_id != $request->class_id) continue;
            if ($request->filled('section_id') && $academicInfo?->section_id != $request->section_id) continue;

            $monthKey = Carbon::parse($fee->due_date)->format('Y-m');
            if (!isset($months[$monthKey])) continue;

            $feeSetItems = $fee->feeSet->items;
            if ($feeSetItems->isEmpty()) continue;

            $sid = $student->id;
            $isNewStudent = ($studentAcademicCounts[$sid] ?? 1) <= 1;

            $applicableItems = [];
            $applicableTotal = 0.0;

            foreach ($feeSetItems as $item) {
                $cat = $item->category;
                if (!$cat || !$cat->status) {
                    continue;
                }

                $studentType = $cat->student_type ?? 'both';
                if ($isNewStudent && $studentType === 'old') continue;
                if (!$isNewStudent && $studentType === 'new') continue;
                if (!isset($selectedCategoryLookup[(string) $cat->id])) continue;

                $baseAmount = (float) $item->amount;
                if ($baseAmount <= 0) {
                    continue;
                }

                $applicableItems[] = ['cat' => $cat, 'base_amount' => $baseAmount];
                $applicableTotal += $baseAmount;
            }

            if (empty($applicableItems) || $applicableTotal <= 0) {
                continue;
            }

            $adjustedLines = [];
            $scholarshipDiscount = (float) $fee->scholarship_discount;
            foreach ($applicableItems as $applicableItem) {
                $amount = $applicableItem['base_amount'];
                if ($scholarshipDiscount > 0) {
                    $amount -= $scholarshipDiscount * ($applicableItem['base_amount'] / $applicableTotal);
                }
                $amount = max(0, $amount);
                if ($amount <= 0) {
                    continue;
                }

                $adjustedLines[] = [
                    'cat' => $applicableItem['cat'],
                    'amount' => $amount,
                ];
            }

            $netTotal = array_sum(array_column($adjustedLines, 'amount'));
            if ($netTotal <= 0) {
                continue;
            }

            $paidTotal = min((float) $fee->paid_amount, $netTotal);

            if (!isset($studentMap[$sid])) {
                $studentMap[$sid] = [
                    'student_id'    => $sid,
                    'student_cid'   => $student->student_cid,
                    'student_name'  => $student->full_name_en,
                    'class_name'    => $academicInfo?->schoolClass?->name_en ?? '—',
                    'section_name'  => $academicInfo?->section?->name_en ?? '—',
                'is_new'        => $isNewStudent,
                'months'        => array_fill_keys(array_keys($months), 0.0),
                'paidMonths'    => array_fill_keys(array_keys($months), 0.0),
                'dueMonths'     => array_fill_keys(array_keys($months), 0.0),
            'categories'    => collect($categories)->mapWithKeys(
                    fn ($cat) => [$cat->id => array_fill_keys(array_keys($months), 0.0)]
                )->all(),
                'total'         => 0.0,
                'paid_total'    => 0.0,
                'due_total'     => 0.0,
            ];
            }

            foreach ($adjustedLines as $line) {
                $cat = $line['cat'];
                $catId  = $cat->id;
                $amount = $line['amount'];
                $share = $netTotal > 0 ? ($amount / $netTotal) : 0;
                $paidAmount = $paidTotal * $share;
                $dueAmount = max(0, $amount - $paidAmount);

                $studentMap[$sid]['categories'][$catId][$monthKey]  += $amount;
                $studentMap[$sid]['months'][$monthKey]               += $amount;
                $studentMap[$sid]['paidMonths'][$monthKey]            += $paidAmount;
                $studentMap[$sid]['dueMonths'][$monthKey]             += $dueAmount;
                $studentMap[$sid]['total']                           += $amount;
                $studentMap[$sid]['paid_total']                      += $paidAmount;
                $studentMap[$sid]['due_total']                       += $dueAmount;
                $totals['categories'][$catId][$monthKey]             += $amount;
                $totals['months'][$monthKey]                         += $amount;
                $totals['total']                                     += $amount;
            }
        }

        $rows = collect(array_values($studentMap))
            ->map(fn($s) => (object) $s)
            ->sortBy('student_name')
            ->values();

        return [$sessions, $classes, $sections, $rows, $months, $categories, $availableCategories, $totals,
            $fromDate->toDateString(), $toDate->toDateString(), $selectedCategoryKeys];
    }

    private function resolveSelectedReceivableReportColumns(Request $request, $availableCategories): array
    {
        $selectionWasSubmitted = $request->has('columns_present');
        $selected = array_values(array_filter((array) $request->input('columns', []), function ($value) {
            return is_numeric($value) || (is_string($value) && $value !== '');
        }));

        $validKeys = $availableCategories->pluck('id')->map(fn ($id) => (string) $id)->all();
        $selected = array_values(array_unique(array_values(array_filter(array_map(function ($value) use ($validKeys) {
            $mapped = (string) $value;

            return in_array($mapped, $validKeys, true) ? $mapped : null;
        }, $selected)))));

        if (! $selectionWasSubmitted && empty($selected)) {
            return $validKeys;
        }

        return $selected;
    }
}
