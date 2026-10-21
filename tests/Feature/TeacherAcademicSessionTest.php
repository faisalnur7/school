<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Employee;
use Tests\TestCase;

class TeacherAcademicSessionTest extends TestCase
{
    public function test_teacher_session_assignment_schema_and_relationships_exist(): void
    {
        $migrations = glob(database_path('migrations/*employee_academic_session*'));

        $this->assertNotEmpty($migrations);
        $migration = file_get_contents($migrations[0]);

        $this->assertStringContainsString('employee_academic_session', $migration);
        $this->assertStringContainsString("foreignId('academic_session_id')", $migration);
        $this->assertStringContainsString("foreignId('employee_id')", $migration);
        $this->assertTrue(method_exists(Employee::class, 'academicSessions'));
        $this->assertTrue(method_exists(AcademicSession::class, 'teachers'));
    }

    public function test_routine_controller_uses_session_assigned_teachers(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/RoutineController.php'));

        $this->assertStringContainsString("whereHas('academicSessions'", $controller);
        $this->assertStringContainsString('whereHas', $controller);
        $this->assertStringContainsString('The selected teacher is not assigned to this academic session.', $controller);
    }

    public function test_teacher_session_assignments_are_managed_from_employee_forms(): void
    {
        $storeRequest = file_get_contents(app_path('Http/Requests/StoreEmployeeRequest.php'));
        $updateRequest = file_get_contents(app_path('Http/Requests/UpdateEmployeeRequest.php'));
        $controller = file_get_contents(app_path('Http/Controllers/HR/EmployeeController.php'));
        $form = file_get_contents(resource_path('views/hr/employees/_form.blade.php'));

        $this->assertStringContainsString("'academic_session_ids'", $storeRequest);
        $this->assertStringContainsString("'academic_session_ids'", $updateRequest);
        $this->assertStringContainsString('academicSessions()->sync', $controller);
        $this->assertStringContainsString('academicSessions', $form);
        $this->assertStringContainsString('activeAcademicSessionId', $form);
    }
}
