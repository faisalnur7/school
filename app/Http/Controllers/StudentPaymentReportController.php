<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\FeeCategory;
use App\Models\InventoryCategory;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class StudentPaymentReportController extends Controller
{
    public function index(Request $request)
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes = SchoolClass::orderBy('order')->orderBy('id')->get();
        $sections = $request->filled('class_id')
            ? Section::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();

        [$categories, $availableCategories, $rows, $dateLabel, $fromDate, $toDate, $selectedCategoryKeys] = $this->buildData($request);

        return view('pages.student-payment-report.index',
            compact('sessions', 'classes', 'sections', 'categories', 'availableCategories', 'rows', 'dateLabel', 'fromDate', 'toDate', 'selectedCategoryKeys'));
    }

    public function pdf(Request $request)
    {
        [$categories, , $rows, $dateLabel] = $this->buildData($request);
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes = SchoolClass::orderBy('order')->orderBy('id')->get();
        $sections = $request->filled('class_id')
            ? Section::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();

        $activeScopePills = $this->buildActiveScopePills($request, $sessions, $classes, $sections);

        $html = view('pages.student-payment-report.pdf',
            compact('categories', 'rows', 'dateLabel', 'activeScopePills'))->render();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 'margin_top' => 10, 'margin_bottom' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('student-payment-report.pdf', 'D');
    }

    public function receiveIndex(Request $request)
    {
        [$sessions, $classes, $sections, $rows, $months, $totals, $fromDate, $toDate] = $this->buildReceiveData($request);

        return view('pages.student-receive-report.index', compact(
            'sessions', 'classes', 'sections', 'rows', 'months', 'totals', 'fromDate', 'toDate'
        ));
    }

    public function receivePdf(Request $request)
    {
        [, , , $rows, $months, $totals, $fromDate, $toDate] = $this->buildReceiveData($request);

        $html = view('pages.student-receive-report.pdf', compact(
            'rows', 'months', 'totals', 'fromDate', 'toDate'
        ))->render();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 'margin_top' => 10, 'margin_bottom' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('student-receive-report.pdf', 'D');
    }

    public function buildReceiveData(Request $request): array
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes = SchoolClass::orderBy('order')->orderBy('id')->get();
        $sections = $request->filled('class_id')
            ? Section::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();
        $rows = collect();
        $months = [];
        $totals = ['months' => [], 'total' => 0.0];
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : null;
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date) : null;

        if (!$fromDate || !$toDate) {
            return [$sessions, $classes, $sections, $rows, $months, $totals, $fromDate?->toDateString(), $toDate?->toDateString()];
        }

        if ($toDate->lt($fromDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        $periodStart = $fromDate->copy()->startOfMonth();
        $periodEnd = $toDate->copy()->startOfMonth();
        $current = $periodStart->copy();

        while ($current->lte($periodEnd)) {
            $key = $current->format('Y-m');
            $months[$key] = $current->format('M-y');
            $totals['months'][$key] = 0.0;
            $current->addMonth();
        }

        if (empty($months)) {
            return [$sessions, $classes, $sections, $rows, $months, $totals, $fromDate->toDateString(), $toDate->toDateString()];
        }

        $studentIdFilter = trim((string) $request->input('student_id', ''));
        $studentMap = [];

        $payments = Payment::with([
                'student.academicInformations.schoolClass',
                'student.academicInformations.section',
            ])
            ->when($studentIdFilter !== '', function ($q) use ($studentIdFilter) {
                $q->whereHas('student', function ($studentQuery) use ($studentIdFilter) {
                    $studentQuery->where('student_cid', $studentIdFilter);
                    if (is_numeric($studentIdFilter)) {
                        $studentQuery->orWhere('id', $studentIdFilter);
                    }
                });
            })
            ->whereBetween('payment_date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->orderBy('payment_date')
            ->orderBy('id')
            ->get();

        foreach ($payments as $payment) {
            $student = $payment->student;
            if (!$student) {
                continue;
            }

            if ($studentIdFilter !== '' && ! $this->studentMatchesFilter($student, $studentIdFilter)) {
                continue;
            }

            $academicInfo = $this->resolveStudentAcademicInfo($student);

            if (! $this->matchesAcademicFilters($academicInfo, $request)) {
                continue;
            }

            $monthKey = Carbon::parse($payment->payment_date)->format('Y-m');
            if (! isset($months[$monthKey])) {
                continue;
            }

            $studentKey = $student->id;

            if (! isset($studentMap[$studentKey])) {
                $studentMap[$studentKey] = [
                    'student_id' => $studentKey,
                    'student_cid' => $student->student_cid,
                    'student_name' => $student->full_name_en,
                    'class_name' => $academicInfo?->schoolClass?->name_en ?? '—',
                    'section_name' => $academicInfo?->section?->name_en ?? '—',
                    'monthTotals' => array_fill_keys(array_keys($months), 0.0),
                    'student_total' => 0.0,
                    'lines' => [],
                ];
            }

            $lineKey = 'payment:' . $payment->id;
            if (! isset($studentMap[$studentKey]['lines'][$lineKey])) {
                $lineDescription = trim(
                    ($payment->receipt_no ? 'Receipt ' . $payment->receipt_no : 'Payment')
                    . ($payment->description ? ' - ' . $payment->description : '')
                );

                $studentMap[$studentKey]['lines'][$lineKey] = [
                    'acc_code' => $payment->receipt_no ?? $payment->id,
                    'description' => $lineDescription ?: 'Payment',
                    'monthTotals' => array_fill_keys(array_keys($months), 0.0),
                    'total' => 0.0,
                ];
            }

            $amount = (float) $payment->amount;
            if ($amount <= 0) {
                continue;
            }

            $studentMap[$studentKey]['lines'][$lineKey]['monthTotals'][$monthKey] += $amount;
            $studentMap[$studentKey]['lines'][$lineKey]['total'] += $amount;
            $studentMap[$studentKey]['monthTotals'][$monthKey] += $amount;
            $studentMap[$studentKey]['student_total'] += $amount;
            $totals['months'][$monthKey] += $amount;
            $totals['total'] += $amount;
        }

        foreach ($studentMap as &$student) {
            $student['lines'] = collect($student['lines'])->values();
        }
        unset($student);

        $rows = collect(array_values($studentMap))
            ->map(fn($student) => (object) array_merge($student, [
                'lines' => collect($student['lines'])->map(fn($line) => (object) $line),
            ]))
            ->sortBy('student_name')
            ->values();

        return [$sessions, $classes, $sections, $rows, $months, $totals, $fromDate->toDateString(), $toDate->toDateString()];
    }

    public function buildData(Request $request): array
    {
        $availableCategories = $this->buildMergedCategories();
        $categoryKeyMap = $this->buildCategoryKeyMap($availableCategories);
        $selectedCategoryKeys = $this->resolveSelectedPaymentReportColumns($request, $availableCategories, $categoryKeyMap);
        $selectedCategoryLookup = array_flip($selectedCategoryKeys);
        $studentIdFilter = trim((string) $request->input('student_id', ''));
        $categories = $availableCategories
            ->filter(fn ($category) => isset($selectedCategoryLookup[$category->column_key]))
            ->values();
        $rows = collect();
        $dateLabel = null;

        $fromDate = $this->resolvePaymentReportDate(
            $request->input('from_date')
            ?: $request->input('date')
            ?: $request->input('to_date')
        );
        $toDate = $this->resolvePaymentReportDate(
            $request->input('to_date')
            ?: $request->input('date')
            ?: $request->input('from_date')
        );

        if (! $fromDate && ! $toDate) {
            return [$categories, $availableCategories, $rows, $dateLabel, null, null, $selectedCategoryKeys];
        }

        $fromDate = $fromDate ?? $toDate;
        $toDate = $toDate ?? $fromDate;

        if ($toDate->lt($fromDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        $studentMap = [];

        $payments = Payment::with([
                'student.academicInformations.schoolClass',
                'student.academicInformations.section',
                'items.fee.feeSet.items.category',
            ])
            ->when($request->filled('student_id'), function ($q) use ($request) {
                $studentId = trim((string) $request->student_id);
                $q->whereHas('student', function ($studentQuery) use ($studentId) {
                    $studentQuery->where('student_cid', $studentId);
                    if (is_numeric($studentId)) {
                        $studentQuery->orWhere('id', $studentId);
                    }
                });
            })
            ->when($request->filled('session_id') || $request->filled('class_id') || $request->filled('section_id'), function ($query) use ($request) {
                $query->whereHas('student.academicInformations', function ($academicQuery) use ($request) {
                    if ($request->filled('session_id')) {
                        $academicQuery->where('academic_session_id', $request->session_id);
                    }

                    if ($request->filled('class_id')) {
                        $academicQuery->where('school_class_id', $request->class_id);
                    }

                    if ($request->filled('section_id')) {
                        $academicQuery->where('section_id', $request->section_id);
                    }
                });
            })
            ->whereBetween('payment_date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->whereHas('items')
            ->get();

        foreach ($payments as $payment) {
            $student = $payment->student;
            if (!$student) {
                continue;
            }

            if ($studentIdFilter !== '' && ! $this->studentMatchesFilter($student, $studentIdFilter)) {
                continue;
            }

            $academicInfo = $this->resolveStudentAcademicInfo($student);

            if (!isset($studentMap[$student->id])) {
                $studentMap[$student->id] = $this->blankRow((object) [
                    'student_id' => $student->id,
                    'student_cid' => $student->student_cid,
                    'student_name' => $student->full_name_en,
                    'class_order' => $academicInfo?->schoolClass?->order ?? PHP_INT_MAX,
                    'class_name' => null,
                    'section_name' => null,
                ], $academicInfo, $categories);
            }

            foreach ($payment->items as $item) {
                $fee = $item->fee;
                if (!$fee || !$fee->feeSet) {
                    continue;
                }

                $feeSetItems = $fee->feeSet->items;
                $totalAmount = $feeSetItems->sum('amount');
                if ($totalAmount <= 0) {
                    continue;
                }

                foreach ($feeSetItems as $feeSetItem) {
                    $category = $feeSetItem->category;
                    if (!$category || ! $category->status) {
                        continue;
                    }

                    $columnKey = $categoryKeyMap['fee_' . $category->id] ?? 'fee_' . $category->id;

                    if (!isset($selectedCategoryLookup[$columnKey])) {
                        continue;
                    }

                    $paid = (float) $item->amount * (float) $feeSetItem->amount / (float) $totalAmount;
                    if ($paid <= 0) {
                        continue;
                    }

                    if (!isset($studentMap[$student->id][$columnKey])) {
                        $studentMap[$student->id][$columnKey] = 0;
                    }

                    $studentMap[$student->id][$columnKey] += $paid;
                }
            }
        }

        $inventoryPayments = Payment::query()
            ->join('inventory_sales',      'inventory_sales.id',           '=', 'payments.inventory_sale_id')
            ->join('inventory_sale_items', 'inventory_sale_items.inventory_sale_id', '=', 'inventory_sales.id')
            ->join('inventory_items',      'inventory_items.id',           '=', 'inventory_sale_items.inventory_item_id')
            ->join('inventory_categories', 'inventory_categories.id',      '=', 'inventory_items.category_id')
            ->join('students',             'students.id',                  '=', 'payments.student_id')
            ->leftJoin('student_academic_information as sai2', function ($j) {
                $j->on('sai2.student_id', '=', 'students.id');
            })
            ->leftJoin('school_classes as sc2', 'sc2.id', '=', 'sai2.school_class_id')
            ->leftJoin('sections as sec2',       'sec2.id', '=', 'sai2.section_id')
            ->where('inventory_categories.is_active', 1)
            ->when($studentIdFilter !== '', function ($q) use ($studentIdFilter) {
                $q->where(function ($studentQuery) use ($studentIdFilter) {
                    $studentQuery->where('students.student_cid', $studentIdFilter);
                    if (is_numeric($studentIdFilter)) {
                        $studentQuery->orWhere('students.id', $studentIdFilter);
                    }
                });
            })
            ->when($request->filled('session_id') || $request->filled('class_id') || $request->filled('section_id'), function ($query) use ($request) {
                $query->whereHas('student.academicInformations', function ($academicQuery) use ($request) {
                    if ($request->filled('session_id')) {
                        $academicQuery->where('academic_session_id', $request->session_id);
                    }

                    if ($request->filled('class_id')) {
                        $academicQuery->where('school_class_id', $request->class_id);
                    }

                    if ($request->filled('section_id')) {
                        $academicQuery->where('section_id', $request->section_id);
                    }
                });
            })
            ->whereBetween('payments.payment_date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->select(
                'students.id as student_id',
                'students.student_cid',
                'students.full_name_en as student_name',
                DB::raw('MAX(sc2.`order`) as class_order'),
                DB::raw('MAX(sc2.name_en) as class_name'),
                DB::raw('MAX(sec2.name_en) as section_name'),
                'inventory_categories.id as category_id',
                DB::raw('SUM(COALESCE(inventory_sale_items.paid_amount, inventory_sale_items.subtotal)) as paid')
            )
            ->groupBy('students.id', 'students.student_cid', 'students.full_name_en', 'inventory_categories.id')
            ->get();

        foreach ($inventoryPayments as $r) {
            $sid = $r->student_id;
            if (!isset($studentMap[$sid])) {
                $studentMap[$sid] = $this->blankRow($r, null, $categories);
            }
            $studentMap[$sid]['class_order'] = min($studentMap[$sid]['class_order'] ?? PHP_INT_MAX, (int) ($r->class_order ?? PHP_INT_MAX));
            $columnKey = $categoryKeyMap['inv_' . $r->category_id] ?? 'inv_' . $r->category_id;
            if (!isset($studentMap[$sid][$columnKey])) {
                $studentMap[$sid][$columnKey] = 0;
            }
            $studentMap[$sid][$columnKey] += (float) $r->paid;
        }

        $rows = collect(array_values($studentMap))->map(function ($row) use ($selectedCategoryKeys) {
            $row['grand_total'] = $this->sumSelectedGrandTotal($row, $selectedCategoryKeys);
            $row['selected_grand_total'] = $row['grand_total'];
            return (object) $row;
        })->sortBy('student_name')->values()
            ->groupBy(fn($r) => $r->class_name . '|' . $r->section_name)
            ->map(fn($group) => (object)[
                'class_order' => $group->first()->class_order ?? PHP_INT_MAX,
                'class_name'  => $group->first()->class_name,
                'section_name' => $group->first()->section_name,
                'students'    => $group->values(),
            ])
            ->sortBy(fn ($group) => sprintf('%010d|%s', $group->class_order ?? PHP_INT_MAX, $group->class_name ?? ''))
            ->values();

        $dateLabel = 'Date Range: ' . $fromDate->format('d M Y') . ' - ' . $toDate->format('d M Y');

        return [
            $categories,
            $availableCategories,
            $rows,
            $dateLabel,
            $fromDate->toDateString(),
            $toDate->toDateString(),
            $selectedCategoryKeys,
        ];
    }

    private function sumSelectedGrandTotal(array $row, array $selectedCategoryKeys): float
    {
        if (empty($selectedCategoryKeys)) {
            return 0.0;
        }

        $total = 0.0;

        foreach ($selectedCategoryKeys as $key) {
            $total += (float) ($row[$key] ?? 0);
        }

        return round($total, 2);
    }

    private function buildMergedCategories()
    {
        $categories = FeeCategory::where('status', 1)
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return (object) [
                    'kind' => 'fee',
                    'id' => $category->id,
                    'name' => $category->name,
                    'display_name_html' => $this->formatPaymentReportHeaderLabel($category->name),
                    'column_key' => 'fee_' . $category->id,
                ];
            })
            ->concat(
                InventoryCategory::where('is_active', 1)
                    ->orderBy('name')
                    ->get()
                    ->map(function ($category) {
                        return (object) [
                            'kind' => 'inventory',
                            'id' => $category->id,
                            'name' => $category->name,
                            'display_name_html' => $this->formatPaymentReportHeaderLabel($category->name),
                            'column_key' => 'inv_' . $category->id,
                        ];
                    })
            );

        return $categories
            ->groupBy(function ($category) {
                return Str::lower(trim((string) $category->name));
            })
            ->map(function ($group) {
                $first = $group->first();
                $normalizedName = Str::lower(trim((string) $first->name));

                return (object) [
                    'kind' => $group->pluck('kind')->unique()->count() > 1 ? 'merged' : $first->kind,
                    'id' => $first->id,
                    'name' => $first->name,
                    'display_name_html' => $this->formatPaymentReportHeaderLabel($first->name),
                    'column_key' => 'category_' . substr(md5($normalizedName), 0, 12),
                    'source_keys' => $group->pluck('column_key')->values()->all(),
                ];
            })
            ->values();
    }

    private function formatPaymentReportHeaderLabel(?string $label): string
    {
        $parts = preg_split('/\s+/', trim((string) $label), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (count($parts) <= 1) {
            return e($label ?? '');
        }

        if (count($parts) === 2) {
            return e($parts[0]) . '<br>' . e($parts[1]);
        }

        $head = implode(' ', array_slice($parts, 0, -1));
        $tail = $parts[array_key_last($parts)];

        return e($head) . '<br>' . e($tail);
    }

    private function buildCategoryKeyMap($availableCategories): array
    {
        $map = [];

        foreach ($availableCategories as $category) {
            $sourceKeys = $category->source_keys ?? [$category->column_key];

            foreach ($sourceKeys as $sourceKey) {
                $map[$sourceKey] = $category->column_key;
            }
        }

        return $map;
    }

    private function resolveSelectedPaymentReportColumns(Request $request, $availableCategories, array $categoryKeyMap): array
    {
        $selectionWasSubmitted = $request->has('columns_present');
        $selected = array_values(array_filter((array) $request->input('columns', []), function ($value) {
            return is_string($value) && $value !== '';
        }));

        $validKeys = $availableCategories->pluck('column_key')->all();
        $selected = array_values(array_unique(array_values(array_filter(array_map(function ($value) use ($categoryKeyMap, $validKeys) {
            $mapped = $categoryKeyMap[$value] ?? $value;

            return in_array($mapped, $validKeys, true) ? $mapped : null;
        }, $selected)))));

        if (! $selectionWasSubmitted && empty($selected)) {
            return $validKeys;
        }

        return $selected;
    }

    private function studentMatchesFilter($student, string $studentIdFilter): bool
    {
        if ($studentIdFilter === '') {
            return true;
        }

        if ((string) $student->student_cid === $studentIdFilter) {
            return true;
        }

        return is_numeric($studentIdFilter) && (string) $student->id === $studentIdFilter;
    }

    private function resolveStudentAcademicInfo($student)
    {
        return $student->academicInformations
            ->firstWhere('is_current', true)
            ?? $student->academicInformations->sortByDesc('academic_session_id')->first()
            ?? $student->academicInformations->first();
    }

    private function matchesAcademicFilters($academicInfo, Request $request): bool
    {
        if ($request->filled('session_id') && (int) $academicInfo?->academic_session_id !== (int) $request->session_id) {
            return false;
        }

        if ($request->filled('class_id') && (int) $academicInfo?->school_class_id !== (int) $request->class_id) {
            return false;
        }

        if ($request->filled('section_id') && (int) $academicInfo?->section_id !== (int) $request->section_id) {
            return false;
        }

        return true;
    }

    private function buildActiveScopePills(Request $request, $sessions, $classes, $sections): array
    {
        return collect([
            $request->filled('session_id') ? 'Session: ' . optional($sessions->firstWhere('id', $request->session_id))->name_en : null,
            $request->filled('class_id') ? 'Class: ' . optional($classes->firstWhere('id', $request->class_id))->name_en : null,
            $request->filled('section_id') ? 'Section: ' . optional($sections->firstWhere('id', $request->section_id))->name_en : null,
            $request->filled('from_date') && $request->filled('to_date')
                ? 'Range: ' . Carbon::parse($request->from_date)->format('d M Y') . ' to ' . Carbon::parse($request->to_date)->format('d M Y')
                : null,
        ])->filter()->values()->all();
    }

    private function resolvePaymentReportDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse($value);
    }

    private function blankRow(object $r, $academicInfo, $categories): array
    {
        $row = [
            'student_id'   => $r->student_id,
            'student_cid'  => $r->student_cid,
            'student_name' => $r->student_name,
            'class_order'  => $r->class_order ?? PHP_INT_MAX,
            'class_name'   => $academicInfo?->schoolClass?->name_en ?? $r->class_name ?? '—',
            'section_name' => $academicInfo?->section?->name_en ?? $r->section_name ?? '—',
        ];

        foreach ($categories as $category) {
            $row[$category->column_key] = 0;
        }

        return $row;
    }

    private function sessionYear(?int $sessionId): int
    {
        if (!$sessionId) return now()->year;
        $session = AcademicSession::find($sessionId);
        // Extract 4-digit year from session name (e.g. "2024-25" → 2024)
        preg_match('/\d{4}/', $session?->name_en ?? '', $m);
        return (int) ($m[0] ?? now()->year);
    }

}
