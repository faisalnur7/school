<?php

namespace Tests\Feature;

use App\Models\Fee;
use App\Models\FeeSet;
use App\Models\FeeSetItem;
use App\Models\FeeCategory;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeCollectionDescriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_description_field_is_validated_and_stored(): void
    {
        $user = User::factory()->create();
        $student = Student::create([
            'full_name_en' => 'Test Student',
            'student_cid' => 'STU-2026-TEST',
            'status' => 1,
        ]);

        $category = FeeCategory::create(['name_en' => 'Tuition']);

        $feeSet = FeeSet::create([
            'name' => 'Test Fee',
            'frequency' => 'monthly',
        ]);

        FeeSetItem::create([
            'fee_set_id' => $feeSet->id,
            'fee_category_id' => $category->id,
            'amount' => 1000,
        ]);

        $fee = Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount' => 1000,
            'net_amount' => 1000,
            'due_date' => now(),
            'status' => 'pending',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->postJson(route('fees.pay'), [
                'fees' => [$fee->id],
                'discount' => 0,
                'discount_type' => 'flat',
                'discount_amount' => 0,
                'description' => 'Test payment description',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Payment collected successfully');

        $payment = Payment::where('student_id', $student->id)->first();
        $this->assertEquals('Test payment description', $payment->description);
    }

    public function test_description_validation_rejects_oversized_input(): void
    {
        $user = User::factory()->create();
        $student = Student::create([
            'full_name_en' => 'Test Student',
            'student_cid' => 'STU-2026-TEST2',
            'status' => 1,
        ]);

        $category = FeeCategory::create(['name_en' => 'Tuition']);

        $feeSet = FeeSet::create(['name' => 'Test Fee']);

        FeeSetItem::create([
            'fee_set_id' => $feeSet->id,
            'fee_category_id' => $category->id,
            'amount' => 1000,
        ]);

        $fee = Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount' => 1000,
            'net_amount' => 1000,
            'due_date' => now(),
            'status' => 'pending',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->postJson(route('fees.pay'), [
                'fees' => [$fee->id],
                'discount' => 0,
                'discount_type' => 'flat',
                'discount_amount' => 0,
                'description' => str_repeat('a', 1001),
            ])
            ->assertStatus(422);
    }

    public function test_description_is_optional(): void
    {
        $user = User::factory()->create();
        $student = Student::create([
            'full_name_en' => 'Test Student',
            'student_cid' => 'STU-2026-TEST3',
            'status' => 1,
        ]);

        $category = FeeCategory::create(['name_en' => 'Tuition']);

        $feeSet = FeeSet::create(['name' => 'Test Fee']);

        FeeSetItem::create([
            'fee_set_id' => $feeSet->id,
            'fee_category_id' => $category->id,
            'amount' => 1000,
        ]);

        $fee = Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount' => 1000,
            'net_amount' => 1000,
            'due_date' => now(),
            'status' => 'pending',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->postJson(route('fees.pay'), [
                'fees' => [$fee->id],
                'discount' => 0,
                'discount_type' => 'flat',
                'discount_amount' => 0,
            ])
            ->assertOk();

        $payment = Payment::where('student_id', $student->id)->first();
        $this->assertNull($payment->description);
    }
}