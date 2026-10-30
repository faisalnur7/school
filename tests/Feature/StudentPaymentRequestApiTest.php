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
use App\Models\User;
use Database\Seeders\MobileAppFeatureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentPaymentRequestApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => MobileAppFeatureSeeder::class, '--quiet' => true]);
    }

    public function test_student_can_create_and_read_their_immutable_payment_request(): void
    {
        $context = $this->createStudentContext('MOBILE-PR-001');
        $fee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 250,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => true,
        ]);
        $paidFee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 80,
            'paid_amount' => 80,
            'status' => 'paid',
            'is_active' => true,
        ]);
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
        Sanctum::actingAs($context['user'], ['student']);

        $feesResponse = $this->getJson('/api/v1/student/fees')
            ->assertOk()
            ->assertJsonPath('inventory.0.items.0.id', $item->id)
            ->assertJsonPath('payment_request_fees.0.id', $fee->id);
        $this->assertFalse(collect($feesResponse->json('payment_request_fees'))->contains('id', $paidFee->id));

        $response = $this->postJson('/api/v1/student/payment-requests', [
            'fees' => [['fee_id' => $fee->id, 'amount' => 250]],
            'items' => [['inventory_item_id' => $item->id, 'quantity' => 1]],
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('total_amount', 370)
            ->assertJsonPath('items.0.item_type', 'fee');

        $requestId = $response->json('id');
        $this->getJson("/api/v1/student/payment-requests/{$requestId}")
            ->assertOk()
            ->assertJsonPath('id', $requestId)
            ->assertJsonCount(2, 'items');
    }

    public function test_student_can_cancel_only_their_pending_request(): void
    {
        $context = $this->createStudentContext('MOBILE-PR-002');
        $fee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 250,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => true,
        ]);
        Sanctum::actingAs($context['user'], ['student']);
        $requestId = $this->postJson('/api/v1/student/payment-requests', ['fees' => [['fee_id' => $fee->id]]])->json('id');

        $this->deleteJson("/api/v1/student/payment-requests/{$requestId}")
            ->assertOk()
            ->assertJsonPath('status', 'cancelled');

        $this->assertDatabaseHas('payment_requests', ['id' => $requestId, 'status' => 'cancelled']);
    }

    public function test_student_cannot_read_another_students_request(): void
    {
        $owner = $this->createStudentContext('MOBILE-PR-003');
        $other = $this->createStudentContext('MOBILE-PR-004');
        $fee = Fee::create([
            'student_id' => $owner['student']->id,
            'fee_set_id' => $owner['feeSet']->id,
            'amount' => 250,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => true,
        ]);
        Sanctum::actingAs($owner['user'], ['student']);
        $requestId = $this->postJson('/api/v1/student/payment-requests', ['fees' => [['fee_id' => $fee->id]]])->json('id');

        Sanctum::actingAs($other['user'], ['student']);
        $this->getJson("/api/v1/student/payment-requests/{$requestId}")->assertNotFound();
    }

    private function createStudentContext(string $cid): array
    {
        $session = AcademicSession::create(['name_bn' => '2026', 'name_en' => '2026', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'Class 1', 'name_en' => 'Class 1', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);
        $student = Student::create(['student_cid' => $cid, 'full_name_en' => 'Mobile Payment Student', 'status' => 1]);
        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'is_current' => true,
            'academic_status' => 'active',
        ]);
        $user = User::factory()->create(['student_id' => $student->id, 'is_active' => true]);
        $feeSet = FeeSet::create(['name' => 'Monthly Fee', 'academic_session_id' => $session->id]);

        return compact('session', 'class', 'section', 'student', 'user', 'feeSet');
    }
}
