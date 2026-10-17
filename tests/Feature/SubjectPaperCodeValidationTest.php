<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectPaperCodeValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_editing_a_multiple_paper_subject_accepts_existing_paper_codes(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $parent = Subject::create([
            'name' => 'Bangla',
            'code' => 'BAN-PAPER-PARENT',
            'type' => 'mandatory',
            'has_multiple_papers' => true,
            'combine_papers_for_result' => true,
            'is_parent' => true,
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        $firstPaper = Subject::create([
            'name' => 'Bangla 1st',
            'code' => 'BAN-PAPER-1',
            'type' => 'mandatory',
            'parent_id' => $parent->id,
            'is_paper' => true,
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        $secondPaper = Subject::create([
            'name' => 'Bangla 2nd',
            'code' => 'BAN-PAPER-2',
            'type' => 'mandatory',
            'parent_id' => $parent->id,
            'is_paper' => true,
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->put(route('subjects.update', $parent), [
            'name' => $parent->name,
            'code' => $parent->code,
            'type' => $parent->type,
            'has_multiple_papers' => 1,
            'combine_papers_for_result' => 1,
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => 1,
            'assign_to_class' => 0,
            'school_class_ids' => [''],
            'papers' => [
                [
                    'id' => $firstPaper->id,
                    'name' => $firstPaper->name,
                    'code' => $firstPaper->code,
                    'creative_marks' => 100,
                    'pass_mark' => 33,
                ],
                [
                    'id' => $secondPaper->id,
                    'name' => $secondPaper->name,
                    'code' => $secondPaper->code,
                    'creative_marks' => 100,
                    'pass_mark' => 33,
                ],
            ],
        ]);

        $response->assertRedirect(route('subjects.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('subjects', [
            'id' => $firstPaper->id,
            'code' => $firstPaper->code,
            'parent_id' => $parent->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('subjects', [
            'id' => $secondPaper->id,
            'code' => $secondPaper->code,
            'parent_id' => $parent->id,
            'deleted_at' => null,
        ]);
    }
}
