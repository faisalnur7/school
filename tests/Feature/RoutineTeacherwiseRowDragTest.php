<?php

namespace Tests\Feature;

use App\Http\Controllers\RoutineController;
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
        $this->assertStringContainsString("'deleted_routine_ids' => ['present', 'array']", $controller);
        $this->assertStringContainsString('$submittedIds->merge($deletedRoutineIds)', $controller);
        $this->assertStringContainsString('RoutineTeacherOrder::upsert', $controller);

        $this->assertStringContainsString('const deletedRoutineIds = new Set();', $view);
        $this->assertStringContainsString('deleted_routine_ids: [...deletedRoutineIds]', $view);
    }

    public function test_teacherwise_playground_counts_all_class_placements_across_periods(): void
    {
        $routines = collect(range(1, 13))->map(function (int $placement) {
            return (object) [
                'teacher_id' => 7,
                'teacher' => (object) ['name' => 'Akhi Sultana'],
                'time_schedule_id' => $placement,
                'start_time' => '08:30:00',
                'school_class_id' => 1,
            ];
        });

        $method = new \ReflectionMethod(RoutineController::class, 'buildTeacherwisePlaygroundRows');
        $method->setAccessible(true);
        $rows = $method->invoke(new RoutineController(), $routines);

        $this->assertSame(13, $rows['7']['class_count']);
    }

    public function test_teacherwise_playground_uses_configured_working_days_for_new_subjects(): void
    {
        $view = file_get_contents(resource_path('views/pages/routines/index.blade.php'));

        $this->assertStringContainsString('@foreach($days as $day)', $view);
        $this->assertStringNotContainsString('@foreach([\'Saturday\' => \'Sat\', \'Sunday\' => \'Sun\', \'Monday\' => \'Mon\', \'Tuesday\' => \'Tue\', \'Wednesday\' => \'Wed\', \'Thursday\' => \'Thu\'] as $day => $shortDay)', $view);
    }

    public function test_teacherwise_save_has_a_complete_snapshot_guard_before_deleting_routines(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/RoutineController.php'));

        $this->assertStringContainsString('assertTeacherwiseSnapshotIsComplete', $controller);
        $this->assertStringContainsString('The teacherwise draft is incomplete', $controller);
    }
}
