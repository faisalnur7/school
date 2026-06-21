<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeSet;
use App\Models\FeeSetItem;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeCollectionReceiptOutstandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_receipt_uses_cumulative_paid_amount_for_outstanding_balance(): void
    {
        $user = User::factory()->create();

        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status'  => 1,
        ]);

        $class = SchoolClass::create([
            'name_en' => 'Class 1',
            'name_bn' => 'Class 1',
            'status'  => 1,
        ]);

        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'A',
            'name_bn' => 'A',
        ]);

        $category = FeeCategory::create([
            'name' => 'Tuition Fee',
            'status' => 1,
        ]);

        $feeSet = FeeSet::create([
            'name' => 'Monthly Fees',
            'bn_name' => 'Monthly Fees',
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'frequency' => 'monthly',
        ]);

        FeeSetItem::create([
            'fee_set_id' => $feeSet->id,
            'fee_category_id' => $category->id,
            'amount' => 1700,
        ]);

        $student = Student::create([
            'full_name_en' => 'Receipt Student',
            'student_cid' => '0310',
            'status' => 1,
        ]);

        $fee = Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount' => 1700,
            'scholarship_discount' => 300,
            'paid_amount' => 1400,
            'due_date' => '2026-06-21',
            'status' => 'paid',
            'is_active' => 1,
        ]);

        $firstPayment = Payment::create([
            'student_id' => $student->id,
            'amount' => 1100,
            'gross_amount' => 1700,
            'scholarship_amount' => 300,
            'discount_type' => null,
            'discount_amount' => 0,
            'payment_date' => '2026-06-21',
            'payment_method' => 'Cash',
            'receipt_no' => 'R-20260621-0001',
            'collected_by' => $user->id,
            'description' => 'First payment',
        ]);

        PaymentItem::create([
            'payment_id' => $firstPayment->id,
            'fee_id' => $fee->id,
            'amount' => 1100,
        ]);

        $secondPayment = Payment::create([
            'student_id' => $student->id,
            'amount' => 300,
            'gross_amount' => 1700,
            'scholarship_amount' => 300,
            'discount_type' => null,
            'discount_amount' => 0,
            'payment_date' => '2026-06-21',
            'payment_method' => 'Cash',
            'receipt_no' => 'R-20260621-0002',
            'collected_by' => $user->id,
            'description' => 'Second payment',
        ]);

        PaymentItem::create([
            'payment_id' => $secondPayment->id,
            'fee_id' => $fee->id,
            'amount' => 300,
        ]);

        $firstHtml = view('pages.payments.receipt', [
            'payment' => $firstPayment->fresh()->load([
                'student',
                'collector',
                'items.fee.feeSet.items.category',
                'inventorySale.items.inventoryItem.category',
                'inventoryDueItems.inventorySaleItem.inventoryItem.category',
            ]),
            'setting' => null,
            'receiptSummary' => $this->receiptSummaryFor($firstPayment->fresh()->load(['items.fee'])),
        ])->render();

        $this->assertStringContainsString('R-20260621-0001', $firstHtml);
        $this->assertStringContainsString('BDT 1,100.00', $firstHtml);
        $this->assertStringContainsString('Outstanding balance after this payment: BDT 300.00', $firstHtml);

        $secondHtml = view('pages.payments.receipt', [
            'payment' => $secondPayment->fresh()->load([
                'student',
                'collector',
                'items.fee.feeSet.items.category',
                'inventorySale.items.inventoryItem.category',
                'inventoryDueItems.inventorySaleItem.inventoryItem.category',
            ]),
            'setting' => null,
            'receiptSummary' => $this->receiptSummaryFor($secondPayment->fresh()->load(['items.fee'])),
        ])->render();

        $this->assertStringContainsString('R-20260621-0002', $secondHtml);
        $this->assertStringContainsString('BDT 1,400.00', $secondHtml);
        $this->assertStringNotContainsString('Outstanding balance after this payment: BDT 1,100.00', $secondHtml);
        $this->assertStringNotContainsString('Outstanding balance after this payment: BDT 300.00', $secondHtml);
    }

    private function receiptSummaryFor(Payment $payment): array
    {
        $feeRecords = $payment->items
            ->map(fn ($item) => $item->fee)
            ->filter()
            ->unique('id')
            ->values();

        $feeSubtotal = (float) $feeRecords->sum(fn ($fee) => (float) ($fee->amount ?? 0));
        $scholarshipAmt = round((float) ($payment->scholarship_amount ?? 0), 2);
        $freeStudentshipAmt = round((float) ($payment->discount_amount ?? 0), 2);
        $totalDue = round(max(0, $feeSubtotal - $scholarshipAmt - $freeStudentshipAmt), 2);

        $paidCutoffDate = Carbon::parse($payment->payment_date ?: $payment->created_at)->toDateString();

        $paidByFee = PaymentItem::query()
            ->selectRaw('payment_items.fee_id as fee_id, SUM(payment_items.amount) as total_paid')
            ->join('payments', 'payments.id', '=', 'payment_items.payment_id')
            ->whereIn('payment_items.fee_id', $feeRecords->pluck('id')->all())
            ->where(function ($query) use ($payment, $paidCutoffDate) {
                $query->whereDate('payments.payment_date', '<', $paidCutoffDate)
                    ->orWhere(function ($subQuery) use ($payment, $paidCutoffDate) {
                        $subQuery->whereDate('payments.payment_date', '=', $paidCutoffDate)
                            ->where('payments.id', '<=', $payment->id);
                    });
            })
            ->groupBy('payment_items.fee_id')
            ->pluck('total_paid', 'fee_id');

        $feePaidTotal = round($feeRecords->sum(fn ($fee) => (float) ($paidByFee[$fee->id] ?? 0)), 2);
        $balanceDue = round(max(0, $totalDue - $feePaidTotal), 2);

        return [
            'feeSubtotal' => $feeSubtotal,
            'scholarshipAmt' => $scholarshipAmt,
            'freeStudentshipAmt' => $freeStudentshipAmt,
            'totalDue' => $totalDue,
            'totalPaid' => $feePaidTotal,
            'balanceDue' => $balanceDue,
        ];
    }
}
