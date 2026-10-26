<?php

namespace Tests\Feature;

use Tests\TestCase;

class RoutineIntegrityTest extends TestCase
{
    public function test_routine_validation_restricts_classes_teachers_and_schedules_to_active_records(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/RoutineController.php'));

        $this->assertStringContainsString("Rule::exists('school_classes', 'id')->where", $controller);
        $this->assertStringContainsString("Rule::exists('employees', 'id')->where", $controller);
        $this->assertStringContainsString("where('employee_type', 'teacher')", $controller);
        $this->assertStringContainsString("where('is_active', true)", $controller);
    }

    public function test_schedule_updates_propagate_times_and_protect_used_schedules(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/ClassScheduleController.php'));

        $this->assertStringContainsString('schedule->routines()->exists()', $controller);
        $this->assertStringContainsString('schedule->routines()->update', $controller);
    }

    public function test_class_playground_has_a_complete_snapshot_guard_before_deleting_routines(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/RoutineController.php'));

        $this->assertStringContainsString('The playground draft is incomplete', $controller);
        $this->assertStringContainsString('array_diff($currentIds, $declaredRoutineIds->all())', $controller);
        $this->assertStringContainsString("'deleted_routine_ids' => ['present', 'array']", $controller);
        $this->assertStringContainsString('$submittedIds->merge($deletedRoutineIds)', $controller);

        $view = file_get_contents(resource_path('views/pages/routines/index.blade.php'));
        $this->assertStringContainsString('deleted_routine_ids: [...deletedRoutineIds]', $view);
    }

    public function test_playground_card_removal_requires_confirmation(): void
    {
        $view = file_get_contents(resource_path('views/pages/routines/index.blade.php'));

        $this->assertSame(3, substr_count($view, "title: 'Remove routine?'"));
        $this->assertSame(3, substr_count($view, "confirmButtonText: 'Yes, remove it'"));
        $this->assertStringContainsString("asset('assets/plugins/sweetalert2/sweetalert2.all.min.js')", $view);
        $this->assertStringNotContainsString("window.confirm('Remove this routine from the draft?')", $view);
        $this->assertStringContainsString('class="d-inline routine-delete-form"', $view);
        $this->assertStringNotContainsString("onsubmit=\"return confirm('Delete this routine?')\"", $view);
    }
}
