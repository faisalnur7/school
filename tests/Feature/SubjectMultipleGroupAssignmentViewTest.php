<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectMultipleGroupAssignmentViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_subject_assignment_form_uses_group_checkbox_cards(): void
    {
        $view = file_get_contents(resource_path('views/pages/subjects/edit.blade.php'));

        $this->assertStringContainsString('subject-group-grid', $view);
        $this->assertStringContainsString('subject-group-item', $view);
        $this->assertStringContainsString('name="group_ids[]"', $view);
        $this->assertStringContainsString('name="all_groups"', $view);
    }

    public function test_subject_assignment_form_does_not_submit_an_empty_class_id(): void
    {
        $view = file_get_contents(resource_path('views/pages/subjects/edit.blade.php'));

        $this->assertStringNotContainsString(
            '<input type="hidden" name="school_class_ids[]" value="">',
            $view
        );
    }
}
