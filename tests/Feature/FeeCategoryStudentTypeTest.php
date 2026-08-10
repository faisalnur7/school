<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeSet;
use App\Models\FeeSetItem;
use App\Models\FreeStudentship;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeCategoryStudentTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_fee_category_with_student_type_new(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('fee-categories.store'), [
                'name'         => 'Admission Fee',
                'bn_name'      => 'ভর্তি ফি',
                'description'  => '',
                'student_type' => 'new',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fee_categories', [
            'name'         => 'Admission Fee',
            'student_type' => 'new',
        ]);
    }

    public function test_can_update_student_type_on_fee_category(): void
    {
        $user = User::factory()->create();
        $category = FeeCategory::create([
            'name'         => 'Tuition Fee',
            'bn_name'      => 'টিউশন ফি',
            'student_type' => 'both',
            'status'       => 1,
        ]);

        $this->actingAs($user)
            ->put(route('fee-categories.update', $category->id), [
                'name'         => 'Tuition Fee',
                'bn_name'      => 'টিউশন ফি',
                'description'  => '',
                'student_type' => 'old',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fee_categories', [
            'id'           => $category->id,
            'student_type' => 'old',
        ]);
    }

    public function test_collect_fee_excludes_fees_for_wrong_student_type(): void
    {
        $user    = User::factory()->create();
        $session = AcademicSession::create(['name_en' => '2026', 'name_bn' => '২০২৬', 'status' => 1]);

        // New student (1 academic info entry)
        $student = Student::create(['full_name_en' => 'New Kid', 'student_cid' => 'STU-NEW-001', 'status' => 1]);
        StudentAcademicInformation::create([
            'student_id'          => $student->id,
            'academic_session_id' => $session->id,
        ]);

        // Category only for old students
        $category = FeeCategory::create(['name' => 'Old Fee', 'bn_name' => 'পুরনো ফি', 'student_type' => 'old', 'status' => 1]);

        $feeSet = FeeSet::create(['name' => 'Old Set', 'academic_session_id' => $session->id]);
        FeeSetItem::create(['fee_set_id' => $feeSet->id, 'fee_category_id' => $category->id, 'amount' => 500]);

        Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount'     => 500,
            'paid_amount'=> 0,
            'status'     => 'pending',
            'is_active'  => true,
            'due_date'   => now()->addDays(30),
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('fees.collect_payment', ['student_id' => $student->id]));
        $response->assertOk();

        $pendingFees = $response->viewData('pendingFees');
        $this->assertCount(0, $pendingFees);
    }

    public function test_collect_fee_includes_fees_with_student_type_both(): void
    {
        $user    = User::factory()->create();
        $session = AcademicSession::create(['name_en' => '2026-B', 'name_bn' => '২০২৬-বি', 'status' => 1]);
        $class = SchoolClass::create(['name_en' => 'Class 1', 'name_bn' => 'Class 1', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_en' => 'A', 'name_bn' => 'A', 'status' => 1]);

        // Old student (2 academic info entries)
        $student = Student::create(['full_name_en' => 'Old Kid', 'student_cid' => 'STU-OLD-001', 'status' => 1]);
        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '1',
            'academic_status' => 'active',
            'is_current' => true,
        ]);
        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => null,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '2',
            'academic_status' => 'active',
            'is_current' => false,
        ]);

        // Category for both
        $category = FeeCategory::create(['name' => 'Monthly Fee', 'bn_name' => 'মাসিক ফি', 'student_type' => 'both', 'status' => 1]);

        $feeSet = FeeSet::create(['name' => 'Both Set', 'academic_session_id' => $session->id]);
        FeeSetItem::create(['fee_set_id' => $feeSet->id, 'fee_category_id' => $category->id, 'amount' => 300]);

        Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount'     => 300,
            'paid_amount'=> 0,
            'status'     => 'pending',
            'is_active'  => true,
            'due_date'   => now()->addDays(30),
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('fees.collect_payment', ['student_id' => $student->id]));
        $response->assertOk();

        $pendingFees = $response->viewData('pendingFees');
        $this->assertCount(1, $pendingFees);
    }

    public function test_collect_payment_labels_free_studentship_discounts_correctly(): void
    {
        $user = User::factory()->create();

        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status' => 1,
        ]);

        $class = SchoolClass::create([
            'name_en' => 'Class 1',
            'name_bn' => 'Class 1',
            'status' => 1,
        ]);

        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'A',
            'name_bn' => 'A',
            'status' => 1,
        ]);

        $category = FeeCategory::create([
            'name' => 'Tuition Fee',
            'bn_name' => 'টিউশন ফি',
            'student_type' => 'both',
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
            'full_name_en' => 'Free Studentship Student',
            'student_cid' => 'STU-FREE-001',
            'status' => 1,
        ]);

        $academicInfo = $student->academicInformations()->create([
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '10',
            'is_current' => 1,
            'academic_status' => 'active',
        ]);

        Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount' => 1700,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => 1,
            'due_date' => now()->addMonth(),
        ]);

        FreeStudentship::create([
            'student_id' => $student->id,
            'student_academic_information_id' => $academicInfo->id,
            'name' => 'Free Studentship 2026',
            'type' => 'fixed',
            'amount' => 220,
            'academic_session_id' => $session->id,
            'fee_category_id' => $category->id,
            'status' => 'active',
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('fees.collect_payment', ['student_id' => $student->id]));

        $response->assertOk();
        $response->assertSee('data-discount-label="Free Studentship"', false);
        $response->assertDontSee('Scholarship: -${discount.toFixed(2)}', false);
    }
}
