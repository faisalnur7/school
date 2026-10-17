<?php

namespace Tests\Feature;

use App\Http\Controllers\SubjectController;
use App\Models\SchoolClass;
use App\Models\Group;
use App\Models\Subject;
use App\Models\SubjectClassAssignment;
use App\Models\User;
use App\Services\SubjectOrderingService;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class SubjectOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_subjects_defaults_to_classwise_view_and_keeps_list_view_explicit(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $defaultResponse = $this->actingAs($user)->get(route('subjects.index'));
        $defaultResponse->assertOk()->assertViewIs('pages.subjects.index_classwise');

        $listResponse = $this->actingAs($user)->get(route('subjects.index', ['view' => 'list']));
        $listResponse->assertOk()->assertViewIs('pages.subjects.index');
    }

    public function test_classwise_subject_order_can_be_saved_for_a_class_and_group(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        [$class, $assignments] = $this->assignments();

        $response = $this->actingAs($user)->post(route('subjects.reorder'), [
            'school_class_id' => $class->id,
            'group_id' => null,
            'assignment_ids' => $assignments->reverse()->pluck('id')->values()->all(),
        ]);

        $response->assertOk()->assertJson(['ok' => true]);
        $this->assertSame(
            $assignments->reverse()->pluck('id')->values()->all(),
            SubjectClassAssignment::where('school_class_id', $class->id)
                ->orderBy('sort_order')
                ->pluck('id')
                ->all()
        );
    }

    public function test_classwise_subjects_are_rendered_in_saved_order(): void
    {
        [$class, $assignments] = $this->assignments();
        $assignments->each(fn (SubjectClassAssignment $assignment, $index) =>
            $assignment->update(['sort_order' => $assignments->count() - $index])
        );

        $view = app(SubjectController::class)->indexClasswise(Request::create('/subjects/classwise'));
        $subjects = $view->getData()['classwiseSubjects'][$class->id]['groups']['no_group']['subjects'];

        $this->assertSame(
            $assignments->sortByDesc('id')->pluck('subject_id')->all(),
            $subjects->pluck('id')->all()
        );
    }

    public function test_report_rows_follow_the_same_saved_subject_order(): void
    {
        [$class, $assignments] = $this->assignments();
        $assignments->each(fn (SubjectClassAssignment $assignment, $index) =>
            $assignment->update(['sort_order' => $assignments->count() - $index])
        );

        $rows = app(SubjectOrderingService::class)->sortRows(new Collection([
            ['subject_id' => $assignments[0]->subject_id, 'subject_name' => 'Zulu'],
            ['subject_id' => $assignments[1]->subject_id, 'subject_name' => 'Alpha'],
        ]), $class->id);

        $this->assertSame(
            $assignments->sortByDesc('id')->pluck('subject_id')->all(),
            $rows->pluck('subject_id')->all()
        );
    }

    public function test_common_subjects_are_sorted_before_group_subjects(): void
    {
        [$class, $assignments] = $this->assignments();
        $group = Group::create(['name_en' => 'Science', 'name_bn' => 'বিজ্ঞান', 'status' => 1]);
        $groupSubject = Subject::create([
            'name' => 'Group Subject',
            'code' => 'GROUP-ORDER',
            'type' => 'mandatory',
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        $groupAssignment = SubjectClassAssignment::create([
            'subject_id' => $groupSubject->id,
            'school_class_id' => $class->id,
            'group_id' => $group->id,
            'gender' => 'all',
            'religion' => 'all',
            'is_optional' => false,
            'is_compulsory' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $assignments->first()->update(['sort_order' => 2]);

        $subjects = app(SubjectOrderingService::class)->sortSubjects(
            Subject::whereIn('id', [$assignments->first()->subject_id, $groupSubject->id])->get(),
            $class->id,
            $group->id,
        );

        $this->assertSame([$assignments->first()->subject_id, $groupAssignment->subject_id], $subjects->pluck('id')->all());
    }

    public function test_progress_report_can_place_paper_subject_rows_first_while_preserving_sort_order(): void
    {
        [$class, $assignments] = $this->assignments();
        $assignments[0]->update(['sort_order' => 1]);
        $assignments[1]->update(['sort_order' => 2]);

        $rows = app(SubjectOrderingService::class)->sortRows(new Collection([
            ['subject_id' => $assignments[0]->subject_id, 'subject_name' => 'Simple Subject'],
            ['subject_id' => $assignments[1]->subject_id, 'subject_name' => 'Paper Parent', 'papers' => [
                ['subject_id' => 99, 'subject_name' => 'Paper'],
            ]],
        ]), $class->id, null, true);

        $this->assertSame($assignments[1]->subject_id, $rows->first()['subject_id']);
        $this->assertSame($assignments[0]->subject_id, $rows->last()['subject_id']);
    }

    public function test_classwise_subjects_place_papers_first_then_follow_sort_order(): void
    {
        $class = SchoolClass::create(['name_en' => 'Paper Class', 'name_bn' => 'পেপার', 'status' => 1]);
        $parent = Subject::create([
            'name' => 'Bangla',
            'code' => 'BAN-PAPER-ORDER',
            'type' => 'mandatory',
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        $paper = Subject::create([
            'name' => 'Bangla 1st',
            'code' => 'BAN-1-PAPER-ORDER',
            'type' => 'mandatory',
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
            'is_paper' => true,
            'parent_id' => $parent->id,
        ]);
        $simple = Subject::create([
            'name' => 'Mathematics',
            'code' => 'MATH-PAPER-ORDER',
            'type' => 'mandatory',
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);

        foreach ([[$paper, 2], [$simple, 1]] as [$subject, $sortOrder]) {
            SubjectClassAssignment::create([
                'subject_id' => $subject->id,
                'school_class_id' => $class->id,
                'group_id' => null,
                'gender' => 'all',
                'religion' => 'all',
                'is_optional' => false,
                'is_compulsory' => true,
                'is_active' => true,
                'sort_order' => $sortOrder,
            ]);
        }

        $subjects = app(SubjectOrderingService::class)->sortSubjects(
            collect([$simple, $paper]),
            $class->id,
        );

        $this->assertSame([$paper->id, $simple->id], $subjects->pluck('id')->all());
    }

    private function assignments(): array
    {
        $class = SchoolClass::create(['name_en' => 'Order Class', 'name_bn' => 'অর্ডার', 'status' => 1]);
        $subjects = collect([
            Subject::create(['name' => 'Zulu', 'code' => 'ZUL-ORDER', 'type' => 'mandatory', 'creative_marks' => 100, 'pass_mark' => 33, 'is_active' => true]),
            Subject::create(['name' => 'Alpha', 'code' => 'ALP-ORDER', 'type' => 'mandatory', 'creative_marks' => 100, 'pass_mark' => 33, 'is_active' => true]),
        ]);

        $assignments = $subjects->map(fn (Subject $subject) => SubjectClassAssignment::create([
            'subject_id' => $subject->id,
            'school_class_id' => $class->id,
            'group_id' => null,
            'gender' => 'all',
            'religion' => 'all',
            'is_optional' => false,
            'is_compulsory' => true,
            'is_active' => true,
        ]));

        return [$class, $assignments];
    }
}
