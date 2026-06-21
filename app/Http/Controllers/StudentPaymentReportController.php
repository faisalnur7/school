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
use Mpdf\Mpdf;

class StudentPaymentReportController extends Controller
{
    public function index(Request $request)
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes = SchoolClass::orderBy('id')->get();
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

        $html = view('pages.student-payment-report.pdf',
            compact('categories', 'rows', 'dateLabel'))->render();

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
        $classes = SchoolClass::orderBy('id')->get();
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
                'items.fee.feeSet.items.category',
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
            ->whereHas('items')
            ->get();

        foreach ($payments as $payment) {
            $student = $payment->student;
            if (!$student) {
                continue;
            }

            $academicInfo = $student->academicInformations
                ->where('academic_session_id', $request->session_id)
                ->first() ?? $student->academicInformations->first();

            if ($request->filled('session_id') && !$academicInfo) {
                continue;
            }
            if ($request->filled('class_id') && $academicInfo?->school_class_id != $request->class_id) {
                continue;
            }
            if ($request->filled('section_id') && $academicInfo?->section_id != $request->section_id) {
                continue;
            }

            $monthKey = Carbon::parse($payment->payment_date)->format('Y-m');
            if (!isset($months[$monthKey])) {
                continue;
            }

            $studentKey = $student->id;

            foreach ($payment->items as $item) {
                $fee = $item->fee;
                if (!$fee || !$fee->feeSet) {
                    continue;
                }

                $feeSetItems = $fee->feeSet->items;
                $feeTotal = $feeSetItems->sum('amount');
                if ($feeTotal <= 0) {
                    continue;
                }

                foreach ($feeSetItems as $feeSetItem) {
                    $category = $feeSetItem->category;
                    if (!$category || !$category->status) {
                        continue;
                    }

                    $amount = (float) $item->amount * ($feeSetItem->amount / $feeTotal);
                    if ($amount <= 0) {
                        continue;
                    }

                    $key = $studentKey . '|' . $category->id . '|' . $category->name;
                    if (!isset($studentMap[$studentKey])) {
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

                    if (!isset($studentMap[$studentKey]['lines'][$key])) {
                        $studentMap[$studentKey]['lines'][$key] = [
                            'acc_code' => $category->id,
                            'description' => $category->name,
                            'monthTotals' => array_fill_keys(array_keys($months), 0.0),
                            'total' => 0.0,
                        ];
                    }

                    $studentMap[$studentKey]['lines'][$key]['monthTotals'][$monthKey] += $amount;
                    $studentMap[$studentKey]['lines'][$key]['total'] += $amount;
                    $studentMap[$studentKey]['monthTotals'][$monthKey] += $amount;
                    $studentMap[$studentKey]['student_total'] += $amount;
                    $totals['months'][$monthKey] += $amount;
                    $totals['total'] += $amount;
                }
            }
        }

        $inventoryPayments = Payment::with([
                'student.academicInformations.schoolClass',
                'student.academicInformations.section',
                'inventorySale.items.inventoryItem.category',
                'inventoryDueItems.inventorySaleItem.inventoryItem.category',
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
            ->whereBetween('payment_date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->where(function ($q) {
                $q->whereNotNull('inventory_sale_id')
                  ->orWhereHas('inventoryDueItems');
            })
            ->get();

        foreach ($inventoryPayments as $payment) {
            $student = $payment->student;
            if (!$student) {
                continue;
            }

            $academicInfo = $student->academicInformations
                ->where('academic_session_id', $request->session_id)
                ->first() ?? $student->academicInformations->first();

            if ($request->filled('session_id') && !$academicInfo) {
                continue;
            }
            if ($request->filled('class_id') && $academicInfo?->school_class_id != $request->class_id) {
                continue;
            }
            if ($request->filled('section_id') && $academicInfo?->section_id != $request->section_id) {
                continue;
            }

            $monthKey = Carbon::parse($payment->payment_date)->format('Y-m');
            if (!isset($months[$monthKey])) {
                continue;
            }
            $academicInfo = $student->academicInformations->first();
            $studentKey = $student->id;

            if ($payment->inventorySale) {
                $sale = $payment->inventorySale;
                $saleItems = $sale->items;
                $saleTotal = $saleItems->sum('subtotal');
                if ($saleTotal > 0) {
                    $inventoryPaidTotal = (float) ($sale->paid_amount ?? 0);

                    foreach ($saleItems as $saleItem) {
                        $inventoryItem = $saleItem->inventoryItem;
                        $inventoryCategory = $inventoryItem?->category;
                        if (!$inventoryCategory || !$inventoryCategory->is_active) {
                            continue;
                        }

                        $amount = $inventoryPaidTotal * ($saleItem->subtotal / $saleTotal);
                        if ($amount <= 0) {
                            continue;
                        }

                        if (!isset($studentMap[$studentKey])) {
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

                        $key = $studentKey . '|' . $inventoryCategory->id . '|' . $inventoryCategory->name;
                        if (!isset($studentMap[$studentKey]['lines'][$key])) {
                            $studentMap[$studentKey]['lines'][$key] = [
                                'acc_code' => $inventoryCategory->id,
                                'description' => $inventoryCategory->name,
                                'monthTotals' => array_fill_keys(array_keys($months), 0.0),
                                'total' => 0.0,
                            ];
                        }

                        $studentMap[$studentKey]['lines'][$key]['monthTotals'][$monthKey] += $amount;
                        $studentMap[$studentKey]['lines'][$key]['total'] += $amount;
                        $studentMap[$studentKey]['monthTotals'][$monthKey] += $amount;
                        $studentMap[$studentKey]['student_total'] += $amount;
                        $totals['months'][$monthKey] += $amount;
                        $totals['total'] += $amount;
                    }
                }
            }

            if ($payment->inventoryDueItems->isNotEmpty()) {
                foreach ($payment->inventoryDueItems as $dueItem) {
                    $saleItem = $dueItem->inventorySaleItem;
                    $inventoryItem = $saleItem?->inventoryItem;
                    $inventoryCategory = $inventoryItem?->category;
                    if (!$inventoryCategory || !$inventoryCategory->is_active) {
                        continue;
                    }

                    $amount = (float) $dueItem->amount;
                    if ($amount <= 0) {
                        continue;
                    }

                    if (!isset($studentMap[$studentKey])) {
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

                    $key = $studentKey . '|due|' . $inventoryCategory->id . '|' . $inventoryCategory->name;
                    if (!isset($studentMap[$studentKey]['lines'][$key])) {
                        $studentMap[$studentKey]['lines'][$key] = [
                            'acc_code' => $inventoryCategory->id,
                            'description' => $inventoryCategory->name . ' - ' . ($inventoryItem?->name ?? 'Item'),
                            'monthTotals' => array_fill_keys(array_keys($months), 0.0),
                            'total' => 0.0,
                        ];
                    }

                    $studentMap[$studentKey]['lines'][$key]['monthTotals'][$monthKey] += $amount;
                    $studentMap[$studentKey]['lines'][$key]['total'] += $amount;
                    $studentMap[$studentKey]['monthTotals'][$monthKey] += $amount;
                    $studentMap[$studentKey]['student_total'] += $amount;
                    $totals['months'][$monthKey] += $amount;
                    $totals['total'] += $amount;
                }
            }
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
        $selectedCategoryKeys = $this->resolveSelectedPaymentReportColumns($request, $availableCategories);
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

                    if (!isset($selectedCategoryLookup['fee_' . $category->id])) {
                        continue;
                    }

                    $paid = (float) $item->amount * (float) $feeSetItem->amount / (float) $totalAmount;
                    if ($paid <= 0) {
                        continue;
                    }

                    $columnKey = 'fee_' . $category->id;
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
            ->whereBetween('payments.payment_date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->select(
                'students.id as student_id',
                'students.student_cid',
                'students.full_name_en as student_name',
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
            $columnKey = 'inv_' . $r->category_id;
            if (!isset($studentMap[$sid][$columnKey])) {
                $studentMap[$sid][$columnKey] = 0;
            }
            $studentMap[$sid][$columnKey] += (float) $r->paid;
        }

        $rows = collect(array_values($studentMap))->map(function ($row) {
            $row['grand_total'] = array_sum(array_filter($row, fn($v, $k) =>
                is_numeric($v) && (str_starts_with($k, 'fee_') || str_starts_with($k, 'inv_')),
                ARRAY_FILTER_USE_BOTH
            ));
            return (object) $row;
        })->sortBy('student_name')->values()
            ->groupBy(fn($r) => $r->class_name . '|' . $r->section_name)
            ->map(fn($group) => (object)[
                'class_name'  => $group->first()->class_name,
                'section_name' => $group->first()->section_name,
                'students'    => $group->values(),
            ])
            ->sortBy('class_name')
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

    private function buildMergedCategories()
    {
        return FeeCategory::where('status', 1)
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return (object) [
                    'kind' => 'fee',
                    'id' => $category->id,
                    'name' => $category->name,
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
                            'column_key' => 'inv_' . $category->id,
                        ];
                    })
            )
            ->values();
    }

    private function resolveSelectedPaymentReportColumns(Request $request, $availableCategories): array
    {
        $selectionWasSubmitted = $request->has('columns_present');
        $selected = array_values(array_filter((array) $request->input('columns', []), function ($value) {
            return is_string($value) && $value !== '';
        }));

        $validKeys = $availableCategories->pluck('column_key')->all();
        $selected = array_values(array_intersect($selected, $validKeys));

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
