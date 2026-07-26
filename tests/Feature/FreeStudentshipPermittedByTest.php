<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\FeeCategory;
use App\Models\FeeSet;
use App\Models\FeeSetItem;
use App\Models\Group;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FreeStudentshipPermittedByTest extends TestCase
{
    use RefreshDatabase;

    public function test_permitted_by_is_saved_and_returned_for_studentship_rows(): void
    {
        $user = User::factory()->create();
        $session = AcademicSession::create(['name_en' => '2026', 'name_bn' => '২০২৬', 'status' => 1]);
        $class = SchoolClass::create(['name_en' => 'Play', 'name_bn' => 'Play', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_en' => 'A', 'name_bn' => 'A', 'status' => 1]);
        $group = Group::create(['school_class_id' => $class->id, 'name_en' => 'A', 'name_bn' => 'A', 'status' => 1]);
        $student = Student::create(['full_name_en' => 'Free Student', 'student_cid' => 'FS-001', 'status' => 1]);

        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'group_id' => $group->id,
            'roll' => '1',
            'academic_status' => 'active',
            'is_current' => true,
        ]);

        $feeCategory = FeeCategory::create([
            'name' => 'Tuition Fee',
            'bn_name' => 'টিউশন ফি',
            'status' => 1,
            'is_transport' => 0,
        ]);

        $feeSet = FeeSet::create([
            'name' => 'Play Fee Set',
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'group_id' => $group->id,
            'frequency' => 'monthly',
        ]);

        FeeSetItem::create([
            'fee_set_id' => $feeSet->id,
            'fee_category_id' => $feeCategory->id,
            'amount' => 1500,
        ]);

        $this->withoutMiddleware()
            ->actingAs($user)
            ->post(route('free-studentships.storeBulk'), [
                'academic_session_id' => $session->id,
                'fee_category_id' => $feeCategory->id,
                'students' => [
                    [
                        'student_id' => $student->id,
                        'academic_info_id' => $student->academicInformations()->first()->id,
                        'type' => 'fixed',
                        'amount' => 500,
                        'permitted_by' => 'Headmaster',
                    ],
                ],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('free_studentships', [
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'fee_category_id' => $feeCategory->id,
            'permitted_by' => 'Headmaster',
            'amount' => 500,
            'type' => 'fixed',
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('free-studentships.students', [
                'academic_session_id' => $session->id,
                'fee_category_id' => $feeCategory->id,
                'school_class_id' => $class->id,
                'section_id' => $section->id,
                'group_id' => $group->id,
            ]));

        $response->assertOk();
        $response->assertJsonFragment([
            'existing_permitted_by' => 'Headmaster',
        ]);
    }

    public function test_create_page_shows_permitted_by_field(): void
    {
        $user = User::factory()->create();

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('free-studentships.create'));

        $response->assertOk();
        $response->assertSee('Permitted By', false);
    }
}
