<?php

namespace Tests\Feature;

use Tests\TestCase;

class SubjectFilterLayoutTest extends TestCase
{
    public function test_subject_filter_uses_a_single_toolbar_row_and_classwise_groups_are_not_cards(): void
    {
        $filter = file_get_contents(resource_path('views/pages/subjects/filter.blade.php'));
        $classwise = file_get_contents(resource_path('views/pages/subjects/index_classwise.blade.php'));

        $this->assertStringContainsString('subject-filter-form', $filter);
        $this->assertStringContainsString('flex-wrap: nowrap;', $filter);
        $this->assertStringContainsString('subject-filter-action', $filter);
        $this->assertStringContainsString('flex: 0 0 auto;', $filter);
        $this->assertStringNotContainsString('flex: 0 0 16%;', $filter);
        $this->assertStringContainsString('subject-group', $classwise);
        $this->assertStringNotContainsString('<div class="card mb-3 shadow-sm">', $classwise);
        $this->assertStringContainsString('gap: 0;', $classwise);
        $this->assertStringContainsString('border-radius: 3px;', $classwise);
        $this->assertStringContainsString('transition: background-color 160ms ease, color 160ms ease, border-color 160ms ease;', $classwise);
        $this->assertStringContainsString('.classwise-subjects-card .nav-tabs .nav-link:hover', $classwise);
        $this->assertStringContainsString('border-bottom: 1px solid #dbe3ef;', $classwise);
        $this->assertStringContainsString('classwise-class-summary', $classwise);
        $this->assertStringContainsString('padding: 0.55rem 0.75rem;', $classwise);
        $this->assertStringContainsString('font-size: 0.82rem;', $classwise);
        $this->assertStringContainsString("row.closest('.subject-group')", $classwise);
    }
}
