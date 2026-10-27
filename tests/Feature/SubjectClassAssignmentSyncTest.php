<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\StudentSubject;
use App\Models\Subject;
use App\Models\SubjectClassAssignment;
use App\Models\User;
use App\Http\Controllers\ExamController;
use App\Services\SubjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class SubjectClassAssignmentSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_unassigning_removes_unmarked_assignments_but_archives_marked_assignments(): void
    {
        $scenario = $this->scenario();
        $service = app(SubjectService::class);

        $result = $service->syncClassAssignments($scenario['subject'], [], $this->assignmentOptions());

        $this->assertCount(1, $result['removed']);
        $this->assertCount(1, $result['archived']);
        $this->assertDatabaseMissing('subject_class_assignments', [
            'id' => $scenario['unmarkedAssignment']->id,
        ]);
        $this->assertDatabaseHas('subject_class_assignments', [
            'id' => $scenario['markedAssignment']->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseMissing('student_subjects', [
            'student_id' => $scenario['student']->id,
            'subject_id' => $scenario['subject']->id,
            'school_class_id' => $scenario['markedClass']->id,
            'academic_session_id' => $scenario['session']->id,
        ]);
        $this->assertDatabaseHas('exam_marks', [
            'exam_id' => $scenario['exam']->id,
            'student_id' => $scenario['student']->id,
            'subject_id' => $scenario['subject']->id,
            'total' => 78,
        ]);
    }

    public function test_reassigning_reactivates_the_archived_assignment_and_preserves_marks(): void
    {
        $scenario = $this->scenario();
        $service = app(SubjectService::class);

        $service->syncClassAssignments($scenario['subject'], [], $this->assignmentOptions());
        $service->syncClassAssignments(
            $scenario['subject'],
            [$scenario['markedClass']->id],
            $this->assignmentOptions()
        );

        $this->assertDatabaseHas('subject_class_assignments', [
            'id' => $scenario['markedAssignment']->id,
            'subject_id' => $scenario['subject']->id,
            'school_class_id' => $scenario['markedClass']->id,
            'is_active' => true,
        ]);
        $this->assertSame(
            1,
            SubjectClassAssignment::where('subject_id', $scenario['subject']->id)
                ->where('school_class_id', $scenario['markedClass']->id)
                ->count()
        );
        $this->assertDatabaseHas('student_subjects', [
            'student_id' => $scenario['student']->id,
            'subject_id' => $scenario['subject']->id,
            'school_class_id' => $scenario['markedClass']->id,
            'academic_session_id' => $scenario['session']->id,
        ]);
        $this->assertDatabaseHas('exam_marks', [
            'exam_id' => $scenario['exam']->id,
            'student_id' => $scenario['student']->id,
            'subject_id' => $scenario['subject']->id,
            'total' => 78,
        ]);
    }

    public function test_assignment_with_marks_is_reported_before_confirmation(): void
    {
        $scenario = $this->scenario();
        $service = app(SubjectService::class);

        $assignments = $service->getAssignmentsWithExamMarksToRemove($scenario['subject'], []);

        $this->assertCount(1, $assignments);
        $this->assertSame($scenario['markedAssignment']->id, $assignments->first()->id);
    }

    public function test_historical_terminal_result_keeps_a_subject_with_saved_marks_after_unassignment(): void
    {
        $scenario = $this->scenario();
        app(SubjectService::class)->syncClassAssignments($scenario['subject'], [], $this->assignmentOptions());

        $view = app(ExamController::class)->terminalResult(
            Request::create('/results/terminal', 'GET', [
                'class_id' => $scenario['markedClass']->id,
                'show_unassigned' => 1,
            ]),
            $scenario['exam']
        );

        $this->assertTrue(
            $view->getData()['displaySubjects']->contains('id', $scenario['subject']->id)
        );
    }

    public function test_a_new_terminal_exam_does_not_show_an_unassigned_subject_without_marks(): void
    {
        $scenario = $this->scenario();
        app(SubjectService::class)->syncClassAssignments($scenario['subject'], [], $this->assignmentOptions());
        $newExam = Exam::create([
            'name' => 'New Terminal Exam',
            'type' => Exam::TYPE_TERMINAL,
            'academic_session_id' => $scenario['session']->id,
            'year' => 2026,
            'status' => Exam::STATUS_PUBLISHED,
        ]);

        $view = app(ExamController::class)->terminalResult(
            Request::create('/results/terminal', 'GET', [
                'class_id' => $scenario['markedClass']->id,
            ]),
            $newExam
        );

        $this->assertFalse(
            $view->getData()['displaySubjects']->contains('id', $scenario['subject']->id)
        );
    }

    public function test_terminal_result_can_hide_unassigned_subjects_from_columns_and_calculations(): void
    {
        $scenario = $this->scenario();
        app(SubjectService::class)->syncClassAssignments($scenario['subject'], [], $this->assignmentOptions());

        $view = app(ExamController::class)->terminalResult(
            Request::create('/results/terminal', 'GET', [
                'class_id' => $scenario['markedClass']->id,
                'show_unassigned' => 0,
            ]),
            $scenario['exam']
        );

        $data = $view->getData();
        $row = $data['results'][$scenario['student']->id];

        $this->assertFalse($data['showUnassigned']);
        $this->assertFalse($data['displaySubjects']->contains('id', $scenario['subject']->id));
        $this->assertSame(0, $row['total_obtained']);
        $this->assertSame(0, $row['total_full']);
        $this->assertSame(0, $row['failed_subject_count']);
        $this->assertSame('Passed', $row['status']);
    }

    public function test_any_failed_terminal_subject_forces_overall_grade_f_and_gpa_zero(): void
    {
        $scenario = $this->scenario();
        $scenario['examMark']->update([
            'total' => 20,
            'letter_grade' => 'F',
            'gpa' => 0,
        ]);

        $view = app(ExamController::class)->terminalResult(
            Request::create('/results/terminal', 'GET', [
                'class_id' => $scenario['markedClass']->id,
            ]),
            $scenario['exam']
        );

        $row = $view->getData()['results'][$scenario['student']->id];

        $this->assertSame(0.0, $row['gpa']);
        $this->assertSame('F', $row['gpa_label']);
        $this->assertSame('Failed', $row['status']);
        $this->assertSame(1, $row['failed_subject_count']);
    }

    public function test_subject_edit_requires_confirmation_before_archiving_assignments_with_marks(): void
    {
        $scenario = $this->scenario();
        $user = User::factory()->create(['is_super_admin' => true]);
        $payload = [
            'name' => $scenario['subject']->name,
            'code' => $scenario['subject']->code,
            'type' => $scenario['subject']->type,
            'is_active' => 1,
            'creative_marks' => 100,
            'pass_mark' => 33,
            'assign_to_class' => 0,
            'school_class_ids' => [''],
            'gender' => 'all',
            'religion' => 'all',
        ];

        $response = $this->actingAs($user)->put(
            route('subjects.update', $scenario['subject']),
            $payload
        );

        $response->assertRedirect();
        $response->assertSessionHas('unassignment_warning');
        $this->assertDatabaseHas('subject_class_assignments', [
            'id' => $scenario['markedAssignment']->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->put(
            route('subjects.update', $scenario['subject']),
            $payload + ['confirm_unassignment_with_marks' => 1]
        );

        $response->assertRedirect(route('subjects.index'));
        $this->assertDatabaseHas('subject_class_assignments', [
            'id' => $scenario['markedAssignment']->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('exam_marks', [
            'exam_id' => $scenario['exam']->id,
            'student_id' => $scenario['student']->id,
            'subject_id' => $scenario['subject']->id,
            'total' => 78,
        ]);
    }

    public function test_combined_subject_assignment_is_archived_when_a_paper_has_marks(): void
    {
        $scenario = $this->scenario();
        $parent = Subject::create([
            'name' => 'Bangla',
            'code' => 'BAN-SYNC',
            'type' => 'mandatory',
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        $paper = Subject::create([
            'name' => 'Bangla 1st Paper',
            'code' => 'BAN1-SYNC',
            'type' => 'mandatory',
            'parent_id' => $parent->id,
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        $assignment = SubjectClassAssignment::create([
            'subject_id' => $parent->id,
            'school_class_id' => $scenario['markedClass']->id,
            'group_id' => null,
            'gender' => 'all',
            'religion' => 'all',
            'is_optional' => false,
            'is_compulsory' => true,
            'is_active' => true,
        ]);
        ExamMark::create([
            'exam_id' => $scenario['exam']->id,
            'student_id' => $scenario['student']->id,
            'subject_id' => $paper->id,
            'total' => 82,
            'is_absent' => false,
            'letter_grade' => 'A+',
            'gpa' => 5,
        ]);

        app(SubjectService::class)->syncClassAssignments($parent, [], $this->assignmentOptions());

        $this->assertDatabaseHas('subject_class_assignments', [
            'id' => $assignment->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('exam_marks', [
            'exam_id' => $scenario['exam']->id,
            'student_id' => $scenario['student']->id,
            'subject_id' => $paper->id,
            'total' => 82,
        ]);
    }

    public function test_changing_all_groups_assignment_to_science_does_not_leave_a_global_duplicate(): void
    {
        $schoolClass = $this->schoolClass('Nine');
        $science = \App\Models\Group::create(['name_en' => 'Science', 'name_bn' => 'Science']);
        $subject = Subject::create([
            'name' => 'Agriculture',
            'code' => 'AGRI-GROUP-SYNC',
            'type' => 'optional',
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        $global = SubjectClassAssignment::create([
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'group_id' => null,
            'gender' => 'all',
            'religion' => 'all',
            'is_optional' => true,
            'is_compulsory' => false,
            'is_active' => true,
        ]);

        app(SubjectService::class)->syncClassAssignments($subject, [$schoolClass->id], [
            'group_id' => $science->id,
            'gender' => 'all',
            'religion' => 'all',
            'is_optional' => true,
            'is_compulsory' => false,
        ]);

        $this->assertDatabaseHas('subject_class_assignments', [
            'id' => $global->id,
            'group_id' => $science->id,
            'is_active' => true,
        ]);
        $this->assertSame(1, SubjectClassAssignment::where('subject_id', $subject->id)
            ->where('school_class_id', $schoolClass->id)
            ->where('is_active', true)
            ->count());
    }

    public function test_explicit_group_ids_win_when_a_conflicting_all_groups_flag_is_submitted(): void
    {
        $schoolClass = $this->schoolClass('Nine');
        $science = \App\Models\Group::create(['name_en' => 'Science', 'name_bn' => 'Science']);
        $subject = Subject::create([
            'name' => 'Bangladesh & Global Studies',
            'code' => 'BGS-CONFLICTING-GROUP-FLAG',
            'type' => 'mandatory',
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        SubjectClassAssignment::create([
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'group_id' => null,
            'gender' => 'all',
            'religion' => 'all',
            'is_optional' => false,
            'is_compulsory' => true,
            'is_active' => true,
        ]);
        $user = User::factory()->create(['is_super_admin' => true]);

        $response = $this->actingAs($user)->put(route('subjects.update', $subject), [
            'name' => $subject->name,
            'code' => $subject->code,
            'type' => $subject->type,
            'is_active' => 1,
            'creative_marks' => 100,
            'pass_mark' => 33,
            'assign_to_class' => 1,
            'school_class_ids' => [$schoolClass->id],
            'all_groups' => 1,
            'group_ids' => [$science->id],
            'gender' => 'all',
            'religion' => 'all',
        ]);

        $response->assertRedirect(route('subjects.index'));
        $this->assertDatabaseHas('subject_class_assignments', [
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'group_id' => $science->id,
            'is_active' => true,
        ]);
        $this->assertDatabaseMissing('subject_class_assignments', [
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'group_id' => null,
            'is_active' => true,
        ]);
    }

    public function test_sync_supports_multiple_group_assignments_for_one_class(): void
    {
        AcademicSession::create(['name_en' => '2026', 'name_bn' => '২০২৬', 'status' => 1]);
        $schoolClass = $this->schoolClass('Nine');
        $science = \App\Models\Group::create(['name_en' => 'Science', 'name_bn' => 'Science']);
        $humanities = \App\Models\Group::create(['name_en' => 'Humanities', 'name_bn' => 'Humanities']);
        $subject = Subject::create([
            'name' => 'General Science',
            'code' => 'GENERAL-SCIENCE-MULTI-GROUP',
            'type' => 'mandatory',
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);

        app(SubjectService::class)->syncClassAssignments($subject, [$schoolClass->id], [
            'group_ids' => [$science->id, $humanities->id],
            'gender' => 'all',
            'religion' => 'all',
            'is_optional' => false,
            'is_compulsory' => true,
        ]);

        $this->assertSame(
            [$science->id, $humanities->id],
            SubjectClassAssignment::where('subject_id', $subject->id)
                ->where('school_class_id', $schoolClass->id)
                ->where('is_active', true)
                ->orderBy('group_id')
                ->pluck('group_id')
                ->all()
        );
        $this->assertDatabaseMissing('subject_class_assignments', [
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'group_id' => null,
            'is_active' => true,
        ]);
    }

    public function test_sync_reuses_an_inactive_group_assignment_when_replacing_a_common_assignment(): void
    {
        $schoolClass = $this->schoolClass('Nine');
        $business = \App\Models\Group::create(['name_en' => 'Business Studies', 'name_bn' => 'Business Studies']);
        $humanities = \App\Models\Group::create(['name_en' => 'Humanities', 'name_bn' => 'Humanities']);
        $subject = Subject::create([
            'name' => 'Agriculture',
            'code' => 'AGRI-INACTIVE-GROUP-SYNC',
            'type' => 'optional',
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        $common = SubjectClassAssignment::create([
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'group_id' => null,
            'gender' => 'all',
            'religion' => 'all',
            'is_optional' => true,
            'is_compulsory' => false,
            'is_active' => true,
        ]);
        $historicalBusiness = SubjectClassAssignment::create([
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'group_id' => $business->id,
            'gender' => 'all',
            'religion' => 'all',
            'is_optional' => true,
            'is_compulsory' => false,
            'is_active' => false,
        ]);

        app(SubjectService::class)->syncClassAssignments($subject, [$schoolClass->id], [
            'group_ids' => [$business->id, $humanities->id],
            'gender' => 'all',
            'religion' => 'all',
            'is_optional' => true,
            'is_compulsory' => false,
        ]);

        $this->assertDatabaseHas('subject_class_assignments', [
            'id' => $historicalBusiness->id,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('subject_class_assignments', [
            'id' => $common->id,
            'group_id' => $humanities->id,
            'is_active' => true,
        ]);
        $this->assertDatabaseMissing('subject_class_assignments', [
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'group_id' => null,
            'is_active' => true,
        ]);
    }

    public function test_duplicate_assignment_detection_recommends_archiving_the_global_assignment(): void
    {
        $schoolClass = $this->schoolClass('Nine');
        $science = \App\Models\Group::create(['name_en' => 'Science', 'name_bn' => 'Science']);
        $subject = Subject::create([
            'name' => 'Agriculture',
            'code' => 'AGRI-DUPLICATE-DETECT',
            'type' => 'optional',
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        $global = SubjectClassAssignment::create([
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'group_id' => null,
            'gender' => 'all',
            'religion' => 'all',
            'is_optional' => true,
            'is_compulsory' => false,
            'is_active' => true,
        ]);
        SubjectClassAssignment::create([
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'group_id' => $science->id,
            'gender' => 'all',
            'religion' => 'all',
            'is_optional' => true,
            'is_compulsory' => false,
            'is_active' => true,
        ]);

        $duplicates = app(SubjectService::class)->getDuplicateAssignments($subject);

        $this->assertCount(1, $duplicates);
        $this->assertSame($global->id, $duplicates->first()['recommended']->id);
    }

    private function scenario(): array
    {
        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status' => 1,
        ]);
        $markedClass = $this->schoolClass('Play');
        $unmarkedClass = $this->schoolClass('Nursery');
        $subject = Subject::create([
            'name' => 'English',
            'code' => 'ENG-SYNC',
            'type' => 'mandatory',
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        $assignmentData = [
            'subject_id' => $subject->id,
            'group_id' => null,
            'gender' => 'all',
            'religion' => 'all',
            'is_optional' => false,
            'is_compulsory' => true,
            'exclusive_group_key' => null,
            'is_active' => true,
        ];
        $markedAssignment = SubjectClassAssignment::create($assignmentData + [
            'school_class_id' => $markedClass->id,
        ]);
        $unmarkedAssignment = SubjectClassAssignment::create($assignmentData + [
            'school_class_id' => $unmarkedClass->id,
        ]);

        $student = Student::create([
            'full_name_en' => 'Test Student',
            'student_cid' => 'SYNC-001',
            'status' => 1,
        ]);
        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $markedClass->id,
            'section_id' => null,
            'group_id' => null,
            'roll' => '1',
        ]);
        StudentSubject::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'school_class_id' => $markedClass->id,
            'academic_session_id' => $session->id,
            'is_optional' => false,
            'is_mandatory' => true,
        ]);
        $exam = Exam::create([
            'name' => 'Terminal Exam',
            'type' => Exam::TYPE_TERMINAL,
            'academic_session_id' => $session->id,
            'year' => 2026,
            'status' => Exam::STATUS_PUBLISHED,
        ]);
        $examMark = ExamMark::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'total' => 78,
            'is_absent' => false,
            'letter_grade' => 'A',
            'gpa' => 4.5,
        ]);

        return compact('session', 'markedClass', 'unmarkedClass', 'subject', 'markedAssignment', 'unmarkedAssignment', 'student', 'exam', 'examMark');
    }

    private function schoolClass(string $name): SchoolClass
    {
        return SchoolClass::create([
            'name_en' => $name,
            'name_bn' => $name,
            'status' => 1,
        ]);
    }

    private function assignmentOptions(): array
    {
        return [
            'group_id' => null,
            'gender' => 'all',
            'religion' => 'all',
            'is_optional' => false,
            'is_compulsory' => true,
            'exclusive_group_key' => null,
        ];
    }
}
