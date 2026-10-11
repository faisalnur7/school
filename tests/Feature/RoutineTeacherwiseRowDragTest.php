<?php

namespace Tests\Feature;

use Tests\TestCase;

class RoutineTeacherwiseRowDragTest extends TestCase
{
    public function test_teacherwise_playground_marks_each_teacher_row_as_draggable_and_binds_row_sorting(): void
    {
        $view = file_get_contents(resource_path('views/pages/routines/index.blade.php'));

        $this->assertStringContainsString('class="teacherwise-playground-row" draggable="true"', $view);
        $this->assertStringContainsString('bindTeacherRowDrag', $view);
        $this->assertStringContainsString('teacherwise-playground-row.is-dragging', $view);
        $this->assertStringContainsString('teacher_order:', $view);

        $controller = file_get_contents(app_path('Http/Controllers/RoutineController.php'));
        $this->assertStringContainsString("'teacher_order' => ['present', 'array']", $controller);
        $this->assertStringContainsString('RoutineTeacherOrder::upsert', $controller);
    }
}
