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
        [$sessions, $feeCategories, $invCategories, $rows, $mode, $dateLabel] = $this->buildData($request);

        return view('pages.student-payment-report.index',
            compact('sessions', 'feeCategories', 'invCategories', 'rows', 'mode', 'dateLabel'));
    }

    public function pdf(Request $request)
    {
        [, $feeCategories, $invCategories, $rows, $mode, $dateLabel] = $this->buildData($request);

        $html = view('pages.student-payment-report.pdf',
            compact('feeCategories', 'invCategories', 'rows', 'mode', 'dateLabel'))->render();

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

    private function buildReceiveData(Request $request): array
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

        $studentMap = [];

        $payments = Payment::with([
                'student.academicInformations.schoolClass',
                'student.academicInformations.section',
                'items.fee.feeSet.items.category',
            ])
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

    private function buildData(Request $request): array
    {
        $sessions      = AcademicSession::orderByDesc('id')->get();
        $feeCategories = FeeCategory::where('status', 1)->orderBy('name')->get();
        $invCategories = InventoryCategory::where('is_active', 1)->orderBy('name')->get();
        $rows          = collect();
        $mode          = null;
        $dateLabel     = null;

        // Determine mode from request
        if ($request->filled('mode')) {
            $mode = $request->input('mode');
        } elseif (!$request->anyFilled(['date', 'month', 'session_id', 'from_date', 'to_date'])) {
            return [$sessions, $feeCategories, $invCategories, $rows, $mode, $dateLabel];
        } else {
            // auto-detect for backward compat
            if ($request->filled('date'))       $mode = 'daily';
            elseif ($request->filled('month'))  $mode = 'monthly';
            elseif ($request->filled('session_id')) $mode = 'yearly';
            elseif ($request->filled('from_date'))  $mode = 'range';
        }

        // Build payment_date range condition closure for fee queries.
        $dateFilter = function ($q) use ($request, $mode) {
            match ($mode) {
                'daily'   => $q->whereDate('payments.payment_date', $request->date),
                'monthly' => $q->whereMonth('payments.payment_date', $request->month)
                               ->whereYear('payments.payment_date', $request->year),
                'range'   => $q->whereBetween('payments.payment_date', [$request->from_date, $request->to_date]),
                default   => null,
            };
        };

        // Build payment_date range condition closure for inventory queries.
        $inventoryDateFilter = function ($q) use ($request, $mode) {
            match ($mode) {
                'daily'   => $q->whereDate('payments.payment_date', $request->date),
                'monthly' => $q->whereMonth('payments.payment_date', $request->month)
                               ->whereYear('payments.payment_date', $request->year),
                'yearly'  => $q->whereYear('payments.payment_date', $this->sessionYear($request->session_id)),
                'range'   => $q->whereBetween('payments.payment_date', [$request->from_date, $request->to_date]),
                default   => null,
            };
        };

        // ── Fee category payments ──────────────────────────────────────────────
        // Use Eloquent Payment relationships and fee set items to allocate paid amounts by category.
        $payments = Payment::with([
                'student.academicInformations.schoolClass',
                'student.academicInformations.section',
                'items.fee.feeSet.items.category',
            ])
            ->when($mode === 'daily', fn($q) => $q->whereDate('payment_date', $request->date))
            ->when($mode === 'monthly', fn($q) =>
                $q->whereMonth('payment_date', $request->month)
                  ->whereYear('payment_date', $request->year)
            )
            ->when($mode === 'range', fn($q) =>
                $q->whereBetween('payment_date', [$request->from_date, $request->to_date])
            )
            ->when($mode === 'yearly', fn($q) =>
                $q->whereHas('items.fee.feeSet', fn($q2) =>
                    $q2->where('academic_session_id', $request->session_id)
                )
            )
            ->whereHas('items')
            ->get();

        $feeRows = collect();

        foreach ($payments as $payment) {
            $student = $payment->student;
            if (!$student) {
                continue;
            }

            $academicInfo = $student->academicInformations
                ->where('academic_session_id', $request->session_id)
                ->first() ?? $student->academicInformations->first();

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
                    if (!$category || !$category->status) {
                        continue;
                    }

                    $paid = (float) $item->amount * (float) $feeSetItem->amount / (float) $totalAmount;
                    $feeRows->push((object)[
                        'student_id'   => $student->id,
                        'student_cid'  => $student->student_cid,
                        'student_name' => $student->full_name_en,
                        'class_name'   => $academicInfo?->schoolClass?->name_en ?? '—',
                        'section_name' => $academicInfo?->section?->name_en ?? '—',
                        'category_id'  => $category->id,
                        'paid'         => $paid,
                    ]);
                }
            }
        }

        $feeRows = $feeRows
            ->groupBy(fn($row) => $row->student_id . '|' . $row->category_id)
            ->map(fn($group) => (object)[
                'student_id'   => $group->first()->student_id,
                'student_cid'  => $group->first()->student_cid,
                'student_name' => $group->first()->student_name,
                'class_name'   => $group->first()->class_name,
                'section_name' => $group->first()->section_name,
                'category_id'  => $group->first()->category_id,
                'paid'         => $group->sum('paid'),
            ])
            ->values();

        // ── Inventory category payments ────────────────────────────────────────
        // payments → inventory_sales → inventory_sale_items → inventory_items → inventory_categories
        $invRows = Payment::query()
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
            ->tap($inventoryDateFilter)
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

        // ── Build pivot rows ───────────────────────────────────────────────────
        $studentMap = [];

        foreach ($feeRows as $r) {
            $sid = $r->student_id;
            if (!isset($studentMap[$sid])) {
                $studentMap[$sid] = $this->blankRow($r, $feeCategories, $invCategories);
            }
            $studentMap[$sid]['fee_' . $r->category_id] = (float) $r->paid;
        }

        foreach ($invRows as $r) {
            $sid = $r->student_id;
            if (!isset($studentMap[$sid])) {
                $studentMap[$sid] = $this->blankRow($r, $feeCategories, $invCategories);
            }
            $studentMap[$sid]['inv_' . $r->category_id] = (float) $r->paid;
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

        $dateLabel = $this->buildDateLabel($request, $mode, $sessions);

        return [$sessions, $feeCategories, $invCategories, $rows, $mode, $dateLabel];
    }

    private function blankRow(object $r, $feeCategories, $invCategories): array
    {
        $row = [
            'student_id'   => $r->student_id,
            'student_cid'  => $r->student_cid,
            'student_name' => $r->student_name,
            'class_name'   => $r->class_name ?? '—',
            'section_name' => $r->section_name ?? '—',
        ];
        foreach ($feeCategories as $fc) $row['fee_' . $fc->id] = 0;
        foreach ($invCategories  as $ic) $row['inv_' . $ic->id] = 0;
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

    private function buildDateLabel(Request $request, ?string $mode, $sessions): ?string
    {
        return match ($mode) {
            'daily'   => 'Date: ' . $request->date,
            'monthly' => date('F', mktime(0, 0, 0, $request->month, 1)) . ' ' . $request->year,
            'yearly'  => 'Session: ' . ($sessions->find($request->session_id)?->name_en ?? '—'),
            'range'   => $request->from_date . ' to ' . $request->to_date,
            default   => null,
        };
    }
}
