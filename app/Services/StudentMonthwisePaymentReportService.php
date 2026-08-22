<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\InventorySale;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StudentMonthwisePaymentReportService
{
    public function build(int $studentId, int $sessionId, ?int $classId = null, ?int $sectionId = null): array
    {
        $session = AcademicSession::findOrFail($sessionId);
        $student = Student::with([
            'fees' => fn ($query) => $query
                ->whereHas('feeSet', fn ($feeSet) => $feeSet->where('academic_session_id', $sessionId))
                ->where('is_active', 1)
                ->with(['feeSet.items.category', 'paymentItems']),
        ])->findOrFail($studentId);

        $academicInfo = StudentAcademicInformation::with(['schoolClass', 'section'])
            ->where('student_id', $studentId)
            ->where('academic_session_id', $sessionId)
            ->when($classId, fn ($query) => $query->where('school_class_id', $classId))
            ->when($sectionId, fn ($query) => $query->where('section_id', $sectionId))
            ->firstOrFail();

        $year = $this->sessionYear($session);
        $rows = collect();

        foreach ($student->fees as $fee) {
            $feeItems = $fee->feeSet?->items ?? collect();
            $totalBase = (float) $feeItems->sum('amount');
            if ($totalBase <= 0) {
                continue;
            }

            $netFeeAmount = max(0, (float) $fee->amount - (float) $fee->scholarship_discount);
            $feePaidAmount = (float) ($fee->paymentItems->sum('amount') ?: $fee->paid_amount);
            $month = $this->monthFor($fee->due_date ?: $fee->created_at, $year);
            if (!$month) {
                continue;
            }

            foreach ($feeItems as $feeItem) {
                $category = $feeItem->category;
                if (!$category) {
                    continue;
                }

                $share = (float) $feeItem->amount / $totalBase;
                $amount = round($netFeeAmount * $share, 2);
                $paid = round($feePaidAmount * $share, 2);

                if ($amount == 0.0 && $paid == 0.0) {
                    continue;
                }

                $rows->push($this->row(
                    $month,
                    $category->name ?? 'Fee',
                    $amount,
                    $paid,
                    'fee'
                ));
            }
        }

        $sales = InventorySale::with(['items.inventoryItem.category'])
            ->where('student_id', $studentId)
            ->get();

        foreach ($sales as $sale) {
            $month = $this->monthFor($sale->created_at, $year);
            if (!$month) {
                continue;
            }

            foreach ($sale->items as $item) {
                $inventoryItem = $item->inventoryItem;
                if (!$inventoryItem) {
                    continue;
                }

                $amount = (float) $item->subtotal;
                $paid = (float) ($item->paid_amount ?? 0);
                if ($amount == 0.0 && $paid == 0.0) {
                    continue;
                }

                $rows->push($this->row(
                    $month,
                    $inventoryItem->name ?: ($inventoryItem->category?->name ?? 'Inventory Sale'),
                    $amount,
                    $paid,
                    'inventory'
                ));
            }
        }

        $months = $rows->groupBy('month')->sortKeys()->map(function (Collection $monthRows, string $month) {
            $amount = round($monthRows->sum('amount'), 2);
            $paid = round($monthRows->sum('paid'), 2);

            return (object) [
                'key' => $month,
                'label' => Carbon::createFromFormat('Y-m', $month)->format('M-y'),
                'rows' => $monthRows->values(),
                'amount' => $amount,
                'paid' => $paid,
                'due' => round($amount - $paid, 2),
            ];
        })->values();

        $runningDue = 0.0;
        $months->each(function ($month) use (&$runningDue) {
            $runningDue = round($runningDue + $month->due, 2);
            $month->running_due = $runningDue;
        });

        return [
            'student' => $student,
            'academicInfo' => $academicInfo,
            'session' => $session,
            'months' => $months,
            'totals' => [
                'amount' => round($months->sum('amount'), 2),
                'paid' => round($months->sum('paid'), 2),
                'due' => round($months->sum('due'), 2),
            ],
        ];
    }

    private function row(string $month, string $description, float $amount, float $paid, string $type): object
    {
        return (object) [
            'month' => $month,
            'description' => $description,
            'amount' => round($amount, 2),
            'paid' => round($paid, 2),
            'due' => round($amount - $paid, 2),
            'type' => $type,
        ];
    }

    private function sessionYear(AcademicSession $session): int
    {
        preg_match('/\b(20\d{2})\b/', (string) $session->name_en, $matches);

        return (int) ($matches[1] ?? now()->year);
    }

    private function monthFor($date, int $year): ?string
    {
        if (!$date) {
            return null;
        }

        $date = Carbon::parse($date);
        return $date->year === $year ? $date->format('Y-m') : null;
    }
}
