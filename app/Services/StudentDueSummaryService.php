<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\InventorySaleItem;
use App\Models\Section;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Payment;
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

        if (!$request->filled('session_id')) {
            return [$sessions, $classes, $sections, $rows, $totals];
        }

        $students = Student::query()
            ->with([
                'academicInformations' => fn($q) => $q
                    ->where('academic_session_id', $request->session_id)
                    ->with(['schoolClass', 'section']),
            'fees' => fn($q) => $q
                    ->whereHas('feeSet', fn($fs) => $fs->where('academic_session_id', $request->session_id))
                    ->where('is_active', 1)
                    ->with('feeSet'),
        ])
            ->whereHas('academicInformations', fn($q) =>
                $q->where('academic_session_id', $request->session_id)
                  ->when($request->filled('class_id'), fn($q) => $q->where('school_class_id', $request->class_id))
                  ->when($request->filled('section_id'), fn($q) => $q->where('section_id', $request->section_id))
            )
            ->get();

        foreach ($students as $student) {
            $academicInfo = $student->academicInformations->first();

            $feeLines = $student->fees
                ->groupBy('fee_set_id')
                ->map(function ($group) {
                    $amount = $group->sum(fn($f) => (float) $f->amount - (float) $f->scholarship_discount);
                    $paid   = $group->sum(fn($f) => (float) $f->paid_amount);
                    return (object)[
                        'type'        => 'fee',
                        'description' => $group->first()->feeSet?->name ?? '—',
                        'amount'      => $amount,
                        'paid'        => $paid,
                        'due'         => max(0, $amount - $paid),
                    ];
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

                    $amount = (float) $item->subtotal;
                    $paid   = (float) ($item->paid_amount ?? 0);
                    $due    = max(0, $amount - $paid);

                    if ($due <= 0) {
                        continue;
                    }

                    $inventoryLines->push((object) [
                        'type'        => 'inventory',
                        'description' => $category->name . ' - ' . ($inventoryItem->name ?? 'Item'),
                        'amount'      => $amount,
                        'paid'        => $paid,
                        'due'         => $due,
                    ]);
                }
            }

            $lines = $feeLines->concat($inventoryLines)->values();

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

        return [$sessions, $classes, $sections, $rows, $totals];
    }
}
