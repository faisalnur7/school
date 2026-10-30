<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeSet;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Services\PaymentRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PaymentRequestApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_server_calculated_mixed_fee_and_inventory_snapshot(): void
    {
        Carbon::setTestNow('2026-10-30 10:00:00');
        $context = $this->createStudentContext('PR-001');
        $fee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 500,
            'paid_amount' => 100,
            'status' => 'partial',
            'is_active' => true,
        ]);
        $category = InventoryCategory::create(['name' => 'Books', 'is_active' => true]);
        $item = InventoryItem::create([
            'category_id' => $category->id,
            'item_type' => 'common',
            'name' => 'Notebook',
            'selling_price' => 75,
            'purchase_price' => 50,
            'current_stock' => 10,
            'is_active' => true,
            'stock_type' => 'stocked',
        ]);

        $request = app(PaymentRequestService::class)->createForStudent($context['student'], [
            'fees' => [['fee_id' => $fee->id, 'amount' => 9999]],
            'items' => [[
                'inventory_item_id' => $item->id,
                'quantity' => 2,
                'unit_price' => 1,
            ]],
        ]);

        $this->assertSame('pending', $request->status);
        $this->assertSame(550.0, (float) $request->total_amount);
        $this->assertEquals(Carbon::now()->addHours(24), $request->expires_at);
        $this->assertCount(2, $request->items);
        $this->assertDatabaseHas('payment_request_items', [
            'payment_request_id' => $request->id,
            'item_type' => 'fee',
            'fee_id' => $fee->id,
            'requested_amount' => 400,
        ]);
        $this->assertDatabaseHas('payment_request_items', [
            'payment_request_id' => $request->id,
            'item_type' => 'inventory',
            'inventory_item_id' => $item->id,
            'quantity' => 2,
            'unit_price' => 75,
            'subtotal' => 150,
        ]);
        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseCount('inventory_sales', 0);
    }

    public function test_it_rejects_an_empty_cart(): void
    {
        $context = $this->createStudentContext('PR-002');

        $this->expectException(ValidationException::class);

        app(PaymentRequestService::class)->createForStudent($context['student'], []);
    }

    public function test_it_rejects_a_fee_from_another_student(): void
    {
        $first = $this->createStudentContext('PR-003');
        $second = $this->createStudentContext('PR-004');
        $fee = Fee::create([
            'student_id' => $second['student']->id,
            'fee_set_id' => $second['feeSet']->id,
            'amount' => 500,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => true,
        ]);

        $this->expectException(ValidationException::class);

        app(PaymentRequestService::class)->createForStudent($first['student'], [
            'fees' => [['fee_id' => $fee->id]],
        ]);
    }

    public function test_expired_returns_true_only_after_the_24_hour_window(): void
    {
        Carbon::setTestNow('2026-10-30 10:00:00');
        $context = $this->createStudentContext('PR-005');
        $fee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 100,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => true,
        ]);
        $request = app(PaymentRequestService::class)->createForStudent($context['student'], [
            'fees' => [['fee_id' => $fee->id]],
        ]);

        $this->assertFalse($request->isExpired());
        Carbon::setTestNow('2026-10-31 10:00:01');
        $this->assertTrue($request->fresh()->isExpired());
    }

    private function createStudentContext(string $studentCid): array
    {
        $session = AcademicSession::create(['name_en' => '2026', 'name_bn' => '২০২৬', 'status' => 1]);
        $class = SchoolClass::create(['name_en' => 'Class 1', 'name_bn' => 'Class 1', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_en' => 'A', 'name_bn' => 'A', 'status' => 1]);
        $student = Student::create(['full_name_en' => 'Payment Request Student', 'student_cid' => $studentCid, 'status' => 1]);
        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'academic_status' => 'active',
            'is_current' => true,
        ]);
        $feeSet = FeeSet::create(['name' => 'Monthly Fee', 'academic_session_id' => $session->id, 'frequency' => 'monthly']);

        return compact('session', 'class', 'section', 'student', 'feeSet');
    }
}
