<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeSet;
use App\Models\FeeSetItem;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentReceivableReportCategoryVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_yearly_fee_category_is_visible_even_when_due_date_is_before_report_window(): void
    {
        $user = User::factory()->create();

        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status' => 1,
        ]);

        $class = SchoolClass::create([
            'name_en' => 'Play Class',
            'name_bn' => 'Play Class',
            'status' => 1,
        ]);

        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'A',
            'name_bn' => 'A',
            'status' => 1,
        ]);

        $student = Student::create([
            'full_name_en' => 'Report Student',
            'student_cid' => 'STU-REP-001',
            'status' => 1,
        ]);

        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '1',
            'academic_status' => 'active',
            'is_current' => true,
        ]);

        $monthlyCategory = FeeCategory::create([
            'name' => 'Tuition Fee',
            'bn_name' => 'মাসিক বেতন',
            'status' => 1,
            'student_type' => 'both',
        ]);

        $yearlyCategory = FeeCategory::create([
            'name' => 'Admission Fee',
            'bn_name' => 'ভর্তি ফি',
            'status' => 1,
            'student_type' => 'both',
        ]);

        $monthlySet = FeeSet::create([
            'name' => 'Monthly Set',
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'frequency' => 'monthly',
        ]);
        FeeSetItem::create([
            'fee_set_id' => $monthlySet->id,
            'fee_category_id' => $monthlyCategory->id,
            'amount' => 2000,
        ]);

        $yearlySet = FeeSet::create([
            'name' => 'Yearly Set',
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'frequency' => 'yearly',
            'due_date' => '2026-01-31',
        ]);
        FeeSetItem::create([
            'fee_set_id' => $yearlySet->id,
            'fee_category_id' => $yearlyCategory->id,
            'amount' => 3000,
        ]);

        Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $monthlySet->id,
            'amount' => 2000,
            'paid_amount' => 0,
            'due_date' => '2026-03-31',
            'status' => 'pending',
            'is_active' => 1,
        ]);

        Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $yearlySet->id,
            'amount' => 3000,
            'paid_amount' => 0,
            'due_date' => '2026-01-31',
            'status' => 'pending',
            'is_active' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('fees.student-receivable-report', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'from_date' => '2026-03-01',
            'to_date' => '2026-12-31',
        ]));

        $response->assertOk();
        $response->assertSee('Tuition Fee');
        $response->assertSee('Admission Fee');
    }

    public function test_receivable_report_can_filter_to_selected_categories_only(): void
    {
        $user = User::factory()->create();

        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status' => 1,
        ]);

        $class = SchoolClass::create([
            'name_en' => 'Play Class',
            'name_bn' => 'Play Class',
            'status' => 1,
        ]);

        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'A',
            'name_bn' => 'A',
            'status' => 1,
        ]);

        $student = Student::create([
            'full_name_en' => 'Selective Report Student',
            'student_cid' => 'STU-REP-002',
            'status' => 1,
        ]);

        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '2',
            'academic_status' => 'active',
            'is_current' => true,
        ]);

        $tuition = FeeCategory::create([
            'name' => 'Tuition Fee',
            'bn_name' => 'মাসিক বেতন',
            'status' => 1,
            'student_type' => 'both',
        ]);

        $admission = FeeCategory::create([
            'name' => 'Admission Fee',
            'bn_name' => 'ভর্তি ফি',
            'status' => 1,
            'student_type' => 'both',
        ]);

        $set = FeeSet::create([
            'name' => 'Monthly Set',
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'frequency' => 'monthly',
        ]);

        FeeSetItem::create([
            'fee_set_id' => $set->id,
            'fee_category_id' => $tuition->id,
            'amount' => 2000,
        ]);

        FeeSetItem::create([
            'fee_set_id' => $set->id,
            'fee_category_id' => $admission->id,
            'amount' => 500,
        ]);

        Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $set->id,
            'amount' => 2500,
            'paid_amount' => 0,
            'due_date' => '2026-03-31',
            'status' => 'pending',
            'is_active' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('fees.student-receivable-report', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'from_date' => '2026-03-01',
            'to_date' => '2026-12-31',
            'columns_present' => 1,
            'columns' => [$tuition->id],
        ]));

        $response->assertOk();
        $response->assertSee('Tuition Fee');
        $this->assertStringNotContainsString('<td>Admission Fee</td>', $response->getContent());
    }
}
