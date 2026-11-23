<?php

namespace Tests\Feature;

use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeSet;
use App\Models\FeeSetItem;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeCollectionCartDiscountTest extends TestCase
{
    use RefreshDatabase;

    private function makeFee(Student $student, float $amount): Fee
    {
        $category = FeeCategory::create(['name_en' => 'Tuition ' . $amount]);
        $feeSet = FeeSet::create(['name' => 'Fee ' . $amount, 'frequency' => 'monthly']);
        FeeSetItem::create(['fee_set_id' => $feeSet->id, 'fee_category_id' => $category->id, 'amount' => $amount]);

        return Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount' => $amount,
            'net_amount' => $amount,
            'due_date' => now(),
            'status' => 'pending',
            'is_active' => true,
        ]);
    }

    private function makeStudent(string $cid): Student
    {
        return Student::create(['full_name_en' => 'Discount Student', 'student_cid' => $cid, 'status' => 1]);
    }

    private function pay(array $payload)
    {
        return $this->withoutMiddleware()
            ->actingAs(User::factory()->create())
            ->postJson(route('fees.pay'), $payload + [
                'payment_date' => now()->toDateString(),
                'discount_type' => 'flat',
            ]);
    }

    public function test_discount_is_split_across_fees_and_the_amount_matches_the_cart_total(): void
    {
        $student = $this->makeStudent('STU-DISC-1');
        $big = $this->makeFee($student, 15000);
        $small = $this->makeFee($student, 5000);

        // What the page now sends for "pay in full" with a 500 flat discount: 375 + 125 taken off.
        $this->pay([
            'fees' => [
                ['fee_id' => $big->id, 'amount' => 14625],
                ['fee_id' => $small->id, 'amount' => 4875],
            ],
            'discount' => 500,
            'discount_amount' => 500,
        ])->assertOk();

        $payment = Payment::where('student_id', $student->id)->firstOrFail();
        $this->assertSame(19500.0, (float) $payment->amount);
        $this->assertSame(500.0, (float) $payment->discount_amount);
        $this->assertSame('paid', $big->fresh()->status);
        $this->assertSame('paid', $small->fresh()->status);
    }

    public function test_discount_larger_than_the_fees_is_capped_at_the_fee_total(): void
    {
        $student = $this->makeStudent('STU-DISC-2');
        $fee = $this->makeFee($student, 1000);

        $this->pay([
            'fees' => [['fee_id' => $fee->id, 'amount' => 0]],
            'discount' => 5000,
            'discount_amount' => 5000,
        ])->assertOk();

        $payment = Payment::where('student_id', $student->id)->firstOrFail();
        $this->assertSame(0.0, (float) $payment->amount);
        $this->assertSame(1000.0, (float) $payment->discount_amount);
    }

    public function test_discount_on_a_cart_without_fees_is_not_recorded(): void
    {
        $student = $this->makeStudent('STU-DISC-3');
        $category = InventoryCategory::create(['name' => 'Uniform', 'is_active' => true]);
        $item = InventoryItem::create([
            'category_id' => $category->id,
            'item_type' => 'common',
            'name' => 'Tie',
            'selling_price' => 120,
            'purchase_price' => 80,
            'current_stock' => 5,
            'is_active' => true,
            'stock_type' => 'stocked',
        ]);

        $this->pay([
            'student_id' => $student->id,
            'items' => [['inventory_item_id' => $item->id, 'quantity' => 1, 'paid_amount' => 120]],
            'discount' => 50,
            'discount_amount' => 50,
        ])->assertOk();

        $payment = Payment::where('student_id', $student->id)->firstOrFail();
        $this->assertSame(0.0, (float) $payment->discount_amount);
        $this->assertNull($payment->discount_type);
    }

    public function test_negative_discount_is_rejected(): void
    {
        $student = $this->makeStudent('STU-DISC-4');
        $fee = $this->makeFee($student, 1000);

        $this->pay([
            'fees' => [['fee_id' => $fee->id]],
            'discount_amount' => -100,
        ])->assertStatus(422);
    }
}
