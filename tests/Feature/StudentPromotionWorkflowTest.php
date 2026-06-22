<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\FeeCategory;
use App\Models\FeeSet;
use App\Models\FeeSetItem;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\StudentSubject;
use App\Models\Subject;
use App\Models\SubjectClassAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPromotionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function gradeForTotal(float $total): array
    {
        if ($total >= 80) {
            return ['letter' => 'A+', 'gpa' => 5];
        }

        if ($total >= 70) {
            return ['letter' => 'A', 'gpa' => 4.5];
        }

        if ($total >= 60) {
            return ['letter' => 'A-', 'gpa' => 4];
        }

        if ($total >= 50) {
            return ['letter' => 'B', 'gpa' => 3];
        }

        if ($total >= 33) {
            return ['letter' => 'C', 'gpa' => 2];
        }

        return ['letter' => 'F', 'gpa' => 0];
    }

    private function buildScenario(array $studentRows): array
    {
        $user = User::factory()->create();

        $sourceSession = AcademicSession::create([
            'name_en' => '2025',
            'name_bn' => '২০২৫',
            'status' => 1,
        ]);

        $targetSession = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status' => 1,
        ]);

        $sourceClass = SchoolClass::create([
            'name_en' => 'Class 5',
            'name_bn' => 'Class 5',
            'status' => 1,
        ]);

        $targetClass = SchoolClass::create([
            'name_en' => 'Class 6',
            'name_bn' => 'Class 6',
            'status' => 1,
        ]);

        $section = Section::create([
            'school_class_id' => $sourceClass->id,
            'name_en' => 'A',
            'name_bn' => 'A',
        ]);

        $subjects = collect();
        $subjectCount = count($studentRows[0]['totals']);
        for ($i = 1; $i <= $subjectCount; $i++) {
            $subject = Subject::create([
                'name' => 'Subject ' . $i,
                'code' => 'S' . $i,
                'type' => 'mandatory',
                'has_multiple_papers' => false,
                'combine_papers_for_result' => false,
                'parent_id' => null,
                'is_parent' => false,
                'is_paper' => false,
                'creative_marks' => 25,
                'mcq_marks' => 25,
                'practical_marks' => 25,
                'viva_marks' => 25,
                'pass_mark' => 33,
                'is_active' => true,
            ]);

            foreach ([$sourceClass->id, $targetClass->id] as $classId) {
                SubjectClassAssignment::create([
                    'subject_id' => $subject->id,
                    'school_class_id' => $classId,
                    'group_id' => null,
                    'gender' => 'all',
                    'religion' => 'all',
                    'is_optional' => false,
                    'is_compulsory' => true,
                    'exclusive_group_key' => null,
                    'is_active' => true,
                ]);
            }

            $subjects->push($subject);
        }

        $feeCategory = FeeCategory::create([
            'name' => 'Tuition Fee',
            'bn_name' => 'Tuition Fee',
            'description' => 'Promotion fee category',
            'status' => 1,
            'is_transport' => 0,
            'student_type' => 'both',
        ]);

        $feeSet = FeeSet::create([
            'name' => 'Target Class Fee Set',
            'bn_name' => 'Target Class Fee Set',
            'academic_session_id' => $targetSession->id,
            'school_class_id' => $targetClass->id,
            'group_id' => null,
            'frequency' => 'yearly',
            'description' => 'Fee set for promoted students',
            'month' => null,
        ]);

        FeeSetItem::create([
            'fee_set_id' => $feeSet->id,
            'fee_category_id' => $feeCategory->id,
            'amount' => 150,
        ]);

        $exam = Exam::create([
            'name' => 'Terminal Final',
            'type' => Exam::TYPE_TERMINAL,
            'academic_session_id' => $sourceSession->id,
            'year' => 2025,
            'status' => Exam::STATUS_PUBLISHED,
        ]);

        $students = collect();
        foreach ($studentRows as $index => $row) {
            $student = Student::create([
                'full_name_en' => $row['name'],
                'student_cid' => $row['cid'],
                'status' => 1,
            ]);

            $academicInfo = StudentAcademicInformation::create([
                'student_id' => $student->id,
                'academic_session_id' => $sourceSession->id,
                'school_class_id' => $sourceClass->id,
                'section_id' => $section->id,
                'group_id' => null,
                'roll' => (string) ($index + 1),
                'academic_status' => 'active',
                'promotion_status' => 'new_admission',
                'is_current' => true,
            ]);

            foreach ($row['totals'] as $subjectIndex => $total) {
                $grade = $this->gradeForTotal((float) $total);

                ExamMark::create([
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'subject_id' => $subjects[$subjectIndex]->id,
                    'total' => $total,
                    'is_absent' => false,
                    'letter_grade' => $grade['letter'],
                    'gpa' => $grade['gpa'],
                ]);
            }

            $students->push([
                'student' => $student,
                'academic_info' => $academicInfo,
            ]);
        }

        return compact('user', 'sourceSession', 'targetSession', 'sourceClass', 'targetClass', 'section', 'subjects', 'exam', 'students', 'feeSet');
    }

    private function promotionPostPayload(array $scenario, array $rows, array $overrides = []): array
    {
        $payload = array_merge([
            'source_session_id' => $scenario['sourceSession']->id,
            'source_class_id' => $scenario['sourceClass']->id,
            'target_session_id' => $scenario['targetSession']->id,
            'target_class_id' => $scenario['targetClass']->id,
            'promotion_mode' => 'final_term_merit_list',
            'fail_threshold' => 1,
            'student_id' => null,
            'promotions' => $rows,
        ], $overrides);

        return $payload;
    }

    public function test_promote_page_renders_source_and_target_filters(): void
    {
        $scenario = $this->buildScenario([
            ['name' => 'Alpha Student', 'cid' => 'CID-001', 'totals' => [80, 80]],
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($scenario['user'])
            ->get(route('students.promote'));

        $response->assertOk();
        $response->assertSee('Source Session');
        $response->assertSee('Source Class');
        $response->assertSee('Target Session');
        $response->assertSee('Target Class');
        $response->assertSee('Student ID');
        $response->assertSee('Promotion Mode');
        $response->assertSee('Final Term Merit List');
        $response->assertSee('N Subjects Fail');
        $response->assertSee('Custom');
        $response->assertDontSee('Merit Rank');
    }

    public function test_single_student_promotion_promotes_only_the_selected_student(): void
    {
        $scenario = $this->buildScenario([
            ['name' => 'Alpha Student', 'cid' => 'CID-001', 'totals' => [80, 80]],
            ['name' => 'Beta Student', 'cid' => 'CID-002', 'totals' => [70, 70]],
        ]);

        $student = $scenario['students'][0]['student'];
        $academicInfo = $scenario['students'][0]['academic_info'];

        $response = $this->withoutMiddleware()
            ->actingAs($scenario['user'])
            ->get(route('students.promote', [
                'source_session_id' => $scenario['sourceSession']->id,
                'source_class_id' => $scenario['sourceClass']->id,
                'target_session_id' => $scenario['targetSession']->id,
                'target_class_id' => $scenario['targetClass']->id,
                'student_id' => $student->student_cid,
                'promotion_mode' => 'custom',
            ]));

        $response->assertOk();
        $response->assertSee($student->full_name_en);
        $response->assertDontSee('Beta Student');

        $this->withoutMiddleware()
            ->actingAs($scenario['user'])
            ->post(route('students.promote.store'), $this->promotionPostPayload($scenario, [
                [
                    'selected' => 1,
                    'source_academic_information_id' => $academicInfo->id,
                    'student_id' => $student->id,
                    'target_section_id' => $scenario['section']->id,
                    'target_group_id' => null,
                    'target_roll' => 1,
                ],
            ], [
                'promotion_mode' => 'custom',
            ]))
            ->assertRedirect();

        $targetRecord = StudentAcademicInformation::query()
            ->where('student_id', $student->id)
            ->where('academic_session_id', $scenario['targetSession']->id)
            ->first();

        $this->assertNotNull($targetRecord);
        $this->assertSame($academicInfo->id, $targetRecord->previous_academic_information_id);
        $this->assertTrue($targetRecord->is_current);
        $this->assertSame('1', (string) $targetRecord->roll);
        $this->assertFalse((bool) $academicInfo->fresh()->is_current);

        $this->assertDatabaseHas('fees', [
            'student_id' => $student->id,
            'fee_set_id' => $scenario['feeSet']->id,
            'amount' => 150,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('student_subjects', [
            'student_id' => $student->id,
            'subject_id' => $scenario['subjects'][0]->id,
            'academic_session_id' => $scenario['targetSession']->id,
            'school_class_id' => $scenario['targetClass']->id,
            'is_optional' => 0,
            'is_mandatory' => 1,
        ]);
    }

    public function test_merit_mode_assigns_sequential_target_rolls(): void
    {
        $scenario = $this->buildScenario([
            ['name' => 'Alpha Student', 'cid' => 'CID-001', 'totals' => [95, 95]],
            ['name' => 'Beta Student', 'cid' => 'CID-002', 'totals' => [85, 85]],
            ['name' => 'Gamma Student', 'cid' => 'CID-003', 'totals' => [75, 75]],
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($scenario['user'])
            ->get(route('students.promote', [
                'source_session_id' => $scenario['sourceSession']->id,
                'source_class_id' => $scenario['sourceClass']->id,
                'target_session_id' => $scenario['targetSession']->id,
                'target_class_id' => $scenario['targetClass']->id,
                'promotion_mode' => 'final_term_merit_list',
            ]));

        $response->assertOk();
        $response->assertSeeInOrder(['Alpha Student', 'Beta Student', 'Gamma Student']);

        $payloadRows = [];
        foreach ($scenario['students'] as $index => $row) {
            $payloadRows[$index] = [
                'selected' => 1,
                'source_academic_information_id' => $row['academic_info']->id,
                'student_id' => $row['student']->id,
                'target_section_id' => $scenario['section']->id,
                'target_group_id' => null,
                'target_roll' => $index + 1,
            ];
        }

        $this->withoutMiddleware()
            ->actingAs($scenario['user'])
            ->post(route('students.promote.store'), $this->promotionPostPayload($scenario, $payloadRows))
            ->assertRedirect();

        $targetRolls = StudentAcademicInformation::query()
            ->whereIn('student_id', $scenario['students']->pluck('student.id')->all())
            ->where('academic_session_id', $scenario['targetSession']->id)
            ->orderBy('student_id')
            ->pluck('roll')
            ->all();

        $this->assertSame(['1', '2', '3'], array_values($targetRolls));
    }

    public function test_skipped_row_in_merit_mode_keeps_later_target_rolls(): void
    {
        $scenario = $this->buildScenario([
            ['name' => 'Alpha Student', 'cid' => 'CID-001', 'totals' => [95, 95]],
            ['name' => 'Beta Student', 'cid' => 'CID-002', 'totals' => [85, 85]],
            ['name' => 'Gamma Student', 'cid' => 'CID-003', 'totals' => [75, 75]],
        ]);

        $payloadRows = [
            [
                'selected' => 1,
                'source_academic_information_id' => $scenario['students'][0]['academic_info']->id,
                'student_id' => $scenario['students'][0]['student']->id,
                'target_section_id' => $scenario['section']->id,
                'target_group_id' => null,
                'target_roll' => 1,
            ],
            [
                'source_academic_information_id' => $scenario['students'][1]['academic_info']->id,
                'student_id' => $scenario['students'][1]['student']->id,
                'target_section_id' => $scenario['section']->id,
                'target_group_id' => null,
                'target_roll' => 2,
            ],
            [
                'selected' => 1,
                'source_academic_information_id' => $scenario['students'][2]['academic_info']->id,
                'student_id' => $scenario['students'][2]['student']->id,
                'target_section_id' => $scenario['section']->id,
                'target_group_id' => null,
                'target_roll' => 3,
            ],
        ];

        $this->withoutMiddleware()
            ->actingAs($scenario['user'])
            ->post(route('students.promote.store'), $this->promotionPostPayload($scenario, $payloadRows))
            ->assertRedirect();

        $records = StudentAcademicInformation::query()
            ->where('academic_session_id', $scenario['targetSession']->id)
            ->orderBy('roll')
            ->get();

        $this->assertCount(2, $records);
        $this->assertSame(['1', '3'], $records->pluck('roll')->all());
        $this->assertSame([$scenario['students'][0]['student']->id, $scenario['students'][2]['student']->id], $records->pluck('student_id')->all());
    }

    public function test_custom_mode_rejects_duplicate_target_rolls(): void
    {
        $scenario = $this->buildScenario([
            ['name' => 'Alpha Student', 'cid' => 'CID-001', 'totals' => [80, 80]],
            ['name' => 'Beta Student', 'cid' => 'CID-002', 'totals' => [70, 70]],
        ]);

        $payloadRows = [
            [
                'selected' => 1,
                'source_academic_information_id' => $scenario['students'][0]['academic_info']->id,
                'student_id' => $scenario['students'][0]['student']->id,
                'target_section_id' => $scenario['section']->id,
                'target_group_id' => null,
                'target_roll' => 1,
            ],
            [
                'selected' => 1,
                'source_academic_information_id' => $scenario['students'][1]['academic_info']->id,
                'student_id' => $scenario['students'][1]['student']->id,
                'target_section_id' => $scenario['section']->id,
                'target_group_id' => null,
                'target_roll' => 1,
            ],
        ];

        $response = $this->withoutMiddleware()
            ->actingAs($scenario['user'])
            ->post(route('students.promote.store'), $this->promotionPostPayload($scenario, $payloadRows, [
                'promotion_mode' => 'custom',
            ]));

        $response->assertSessionHasErrors('promotions');
        $this->assertDatabaseMissing('student_academic_information', [
            'academic_session_id' => $scenario['targetSession']->id,
        ]);
    }

    public function test_fail_based_cohort_loading_filters_by_threshold(): void
    {
        $scenario = $this->buildScenario([
            ['name' => 'Failing Student', 'cid' => 'CID-001', 'totals' => [20, 20]],
            ['name' => 'Passing Student', 'cid' => 'CID-002', 'totals' => [80, 80]],
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($scenario['user'])
            ->get(route('students.promote', [
                'source_session_id' => $scenario['sourceSession']->id,
                'source_class_id' => $scenario['sourceClass']->id,
                'target_session_id' => $scenario['targetSession']->id,
                'target_class_id' => $scenario['targetClass']->id,
                'promotion_mode' => 'n_subjects_fail',
                'fail_threshold' => 2,
            ]));

        $response->assertOk();
        $response->assertSee('Failing Student');
        $response->assertSee('Failed in 2 subject(s)');
        $response->assertSee('name="promotions[0][source_academic_information_id]"', false);
    }

    public function test_prevents_duplicate_current_records_for_target_session(): void
    {
        $scenario = $this->buildScenario([
            ['name' => 'Alpha Student', 'cid' => 'CID-001', 'totals' => [80, 80]],
        ]);

        $student = $scenario['students'][0]['student'];
        $academicInfo = $scenario['students'][0]['academic_info'];

        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $scenario['targetSession']->id,
            'school_class_id' => $scenario['targetClass']->id,
            'section_id' => $scenario['section']->id,
            'group_id' => null,
            'roll' => '1',
            'academic_status' => 'active',
            'promotion_status' => 'promoted',
            'is_current' => true,
            'previous_academic_information_id' => $academicInfo->id,
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($scenario['user'])
            ->post(route('students.promote.store'), $this->promotionPostPayload($scenario, [
                [
                    'selected' => 1,
                    'source_academic_information_id' => $academicInfo->id,
                    'student_id' => $student->id,
                    'target_section_id' => $scenario['section']->id,
                    'target_group_id' => null,
                    'target_roll' => 2,
                ],
            ]));

        $response->assertSessionHasErrors('target_session_id');
        $this->assertSame(1, StudentAcademicInformation::query()
            ->where('student_id', $student->id)
            ->where('academic_session_id', $scenario['targetSession']->id)
            ->count());
    }
}
