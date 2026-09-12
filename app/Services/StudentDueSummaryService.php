<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\InventorySaleItem;
use App\Models\Section;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Payment;
use App\Models\FeeCategory;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;

class StudentDueSummaryService
{
    public function build(Request $request): array
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes  = SchoolClass::get();
        $sections = $request->filled('class_id')
            ? Section::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();

        $rows   = collect();
        $totals = [
            'amount' => 0.0,
            'paid' => 0.0,
            'due' => 0.0,
            'fees' => ['amount' => 0.0, 'paid' => 0.0, 'due' => 0.0],
            'inventory' => ['amount' => 0.0, 'paid' => 0.0, 'due' => 0.0],
        ];
        $availableCategories = collect();
        $selectedCategoryKeys = [];

        if (!$request->filled('session_id')) {
            foreach (FeeCategory::where('status', 1)->orderBy('name')->get() as $category) {
                $key = 'fee_' . $category->id;
                $availableCategories->put($key, (object) ['key' => $key, 'name' => $category->name]);
            }
            foreach (InventoryCategory::where('is_active', 1)->orderBy('name')->get() as $category) {
                $key = 'inventory_' . $category->id;
                $availableCategories->put($key, (object) ['key' => $key, 'name' => $category->name]);
            }
            $availableCategories = $availableCategories->sortBy('name')->values();
            $selectedCategoryKeys = $this->resolveSelectedCategoryKeys($request, $availableCategories);
            return [$sessions, $classes, $sections, $rows, $totals, $availableCategories, $selectedCategoryKeys];
        }

        $students = Student::query()
            ->with([
                'academicInformations' => fn($q) => $q
                    ->where('academic_session_id', $request->session_id)
                    ->with(['schoolClass', 'section']),
            'fees' => fn($q) => $q
                    ->whereHas('feeSet', fn($fs) => $fs->where('academic_session_id', $request->session_id))
                    ->where('is_active', 1)
                    ->with('feeSet.items.category'),
        ])
            ->whereHas('academicInformations', fn($q) =>
                $q->where('academic_session_id', $request->session_id)
                  ->when($request->filled('class_id'), fn($q) => $q->where('school_class_id', $request->class_id))
                  ->when($request->filled('section_id'), fn($q) => $q->where('section_id', $request->section_id))
            )
            ->when($request->filled('student_id'), function ($q) use ($request) {
                $studentId = trim((string) $request->student_id);
                $q->where(function ($studentQuery) use ($studentId) {
                    $studentQuery->where('student_cid', $studentId);
                    if (is_numeric($studentId)) {
                        $studentQuery->orWhere('id', $studentId);
                    }
                });
            })
            ->get();

        foreach ($students as $student) {
            foreach ($student->fees as $fee) {
                foreach ($fee->feeSet?->items ?? [] as $item) {
                    if ($item->category && $item->category->status) {
                        $key = 'fee_' . $item->category->id;
                        $availableCategories->put($key, (object) ['key' => $key, 'name' => $item->category->name]);
                    }
                }
            }
        }
        foreach (InventoryCategory::where('is_active', 1)->orderBy('name')->get() as $category) {
            $key = 'inventory_' . $category->id;
            $availableCategories->put($key, (object) ['key' => $key, 'name' => $category->name]);
        }
        $availableCategories = $availableCategories->sortBy('name')->values();
        $selectedCategoryKeys = $this->resolveSelectedCategoryKeys($request, $availableCategories);
        $selectedLookup = array_flip($selectedCategoryKeys);

        foreach ($students as $student) {
            $academicInfo = $student->academicInformations->first();

            $feeLines = $student->fees
                ->groupBy('fee_set_id')
                ->flatMap(function ($group) use ($selectedLookup) {
                    $feeSet = $group->first()->feeSet;
                    $items = $feeSet?->items ?? collect();
                    $setTotal = (float) $items->sum('amount');
                    if ($setTotal <= 0) return collect();
                    return $items->filter(fn ($item) => $item->category && $item->category->status && isset($selectedLookup['fee_' . $item->category->id]))
                        ->map(function ($item) use ($group, $setTotal, $feeSet) {
                            $share = (float) $item->amount / $setTotal;
                            $amount = $group->sum(fn($f) => ((float) $f->amount - (float) $f->scholarship_discount) * $share);
                            $paid = $group->sum(fn($f) => (float) $f->paid_amount * $share);
                            return (object)[
                                'type' => 'fee',
                                'category_key' => 'fee_' . $item->category->id,
                                'description' => ($feeSet?->name ?? '—') . ' - ' . $item->category->name,
                                'amount' => $amount,
                                'paid' => $paid,
                                'due' => max(0, $amount - $paid),
                            ];
                        });
                })
                ->filter(fn ($line) => (float) $line->due > 0)
                ->values();

            $inventoryPayments = Payment::with(['inventorySale.items.inventoryItem.category', 'student.academicInformations.schoolClass', 'student.academicInformations.section'])
                ->where('student_id', $student->id)
                ->whereNotNull('inventory_sale_id')
                ->get();

            $inventoryLines = collect();
            foreach ($inventoryPayments as $payment) {
                $sale = $payment->inventorySale;
                if (!$sale) {
                    continue;
                }

                foreach ($sale->items as $item) {
                    $inventoryItem = $item->inventoryItem;
                    $category = $inventoryItem?->category;
                    if (!$inventoryItem || !$category) {
                        continue;
                    }

                    $categoryKey = 'inventory_' . $category->id;
                    $availableCategories->put($categoryKey, (object) ['key' => $categoryKey, 'name' => $category->name]);
                    if (!isset($selectedLookup[$categoryKey])) continue;

                    $amount = (float) $item->subtotal;
                    $paid   = (float) ($item->paid_amount ?? 0);
                    $due    = max(0, $amount - $paid);

                    if ($due <= 0) {
                        continue;
                    }

                    $inventoryLines->push((object) [
                        'type'        => 'inventory',
                        'category_key' => $categoryKey,
                        'description' => $category->name . ' - ' . ($inventoryItem->name ?? 'Item'),
                        'amount'      => $amount,
                        'paid'        => $paid,
                        'due'         => $due,
                    ]);
                }
            }

            $lines = $feeLines->concat($inventoryLines)->values();

            // Skip students with no outstanding fee or inventory dues.
            if ($lines->isEmpty()) {
                continue;
            }

            $rows->push((object)[
                'student_id'   => $student->id,
                'cid'          => $student->student_cid,
                'name'         => $student->full_name_en,
                'class_name'   => $academicInfo?->schoolClass?->name_en ?? '—',
                'section_name' => $academicInfo?->section?->name_en ?? '—',
                'lines'        => $lines,
                'fees_total'   => $feeLines->sum('amount'),
                'fees_paid'    => $feeLines->sum('paid'),
                'fees_due'     => $feeLines->sum('due'),
                'inventory_total' => $inventoryLines->sum('amount'),
                'inventory_paid'  => $inventoryLines->sum('paid'),
                'inventory_due'   => $inventoryLines->sum('due'),
                'paid_amount'  => $lines->sum('paid'),
                'due'          => $lines->sum('due'),
            ]);
        }

        $rows = $rows->sortBy('name')->values();

        $totals['fees']['amount'] = $rows->sum('fees_total');
        $totals['fees']['paid']   = $rows->sum('fees_paid');
        $totals['fees']['due']    = $rows->sum('fees_due');
        $totals['inventory']['amount'] = $rows->sum('inventory_total');
        $totals['inventory']['paid']   = $rows->sum('inventory_paid');
        $totals['inventory']['due']    = $rows->sum('inventory_due');
        $totals['amount'] = $totals['fees']['amount'] + $totals['inventory']['amount'];
        $totals['paid']   = $totals['fees']['paid'] + $totals['inventory']['paid'];
        $totals['due']    = $totals['fees']['due'] + $totals['inventory']['due'];

        return [$sessions, $classes, $sections, $rows, $totals, $availableCategories, $selectedCategoryKeys];
    }

    private function resolveSelectedCategoryKeys(Request $request, $availableCategories): array
    {
        $valid = $availableCategories->pluck('key')->values()->all();
        if (!$request->has('columns_present')) return $valid;

        return array_values(array_unique(array_filter(array_map(
            fn ($value) => in_array((string) $value, $valid, true) ? (string) $value : null,
            (array) $request->input('columns', [])
        ))));
    }
}
