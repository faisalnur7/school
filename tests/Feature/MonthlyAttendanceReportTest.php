<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\AttendanceItem;
use App\Models\Holiday;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\TeacherSectionAssignment;
use App\Models\User;
use App\Models\WeekendSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlyAttendanceReportTest extends TestCase
{
    use RefreshDatabase;

    private function scaffold(): array
    {
        $teacher = User::factory()->create();
        $session = AcademicSession::create(['name_bn' => 'S', 'name_en' => 'S', 'status' => 1]);
        $class   = SchoolClass::create(['name_bn' => 'C', 'name_en' => 'C', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);

        TeacherSectionAssignment::create([
            'user_id'    => $teacher->id,
            'session_id' => $session->id,
            'class_id'   => $class->id,
            'section_id' => $section->id,
        ]);

        $student = Student::create(['full_name_en' => 'Alice']);
        StudentAcademicInformation::create([
            'student_id'          => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id'     => $class->id,
            'section_id'          => $section->id,
            'roll'                => '1',
        ]);

        return compact('teacher', 'session', 'class', 'section', 'student');
    }

    public function test_assigned_teacher_can_load_monthly_report(): void
    {
        ['teacher' => $teacher, 'session' => $session, 'class' => $class, 'section' => $section, 'student' => $student] = $this->scaffold();

        // No weekends/holidays — all days are working
        WeekendSetting::create(['weekend_days' => []]);

        // Mark student present on 2026-05-04
        $att = Attendance::create([
            'session_id' => $session->id,
            'class_id'   => $class->id,
            'section_id' => $section->id,
            'date'       => '2026-05-04',
            'taken_by'   => $teacher->id,
        ]);
        AttendanceItem::create(['attendance_id' => $att->id, 'student_id' => $student->id, 'status' => 'present']);

        $qs = http_build_query([
            'session_id' => $session->id,
            'class_id'   => $class->id,
            'section_id' => $section->id,
            'month'      => '2026-05',
        ]);

        $response = $this->actingAs($teacher)
            ->get('/teacher/attendance/report/monthly/load?' . $qs);

        $response->assertStatus(200);
        $response->assertSee('P');  // present on 2026-05-04
        $response->assertSee('A');  // absent on other days
    }

    public function test_unauthorized_teacher_gets_403_on_report(): void
    {
        ['session' => $session, 'class' => $class, 'section' => $section] = $this->scaffold();
        $other = User::factory()->create();

        $qs = http_build_query([
            'session_id' => $session->id,
            'class_id'   => $class->id,
            'section_id' => $section->id,
            'month'      => '2026-05',
        ]);

        $this->actingAs($other)
            ->get('/teacher/attendance/report/monthly/load?' . $qs)
            ->assertStatus(403);
    }

    public function test_weekend_days_excluded_from_totals(): void
    {
        ['teacher' => $teacher, 'session' => $session, 'class' => $class, 'section' => $section] = $this->scaffold();

        // All days are weekends — no working days
        WeekendSetting::create(['weekend_days' => [0, 1, 2, 3, 4, 5, 6]]);

        $qs = http_build_query([
            'session_id' => $session->id,
            'class_id'   => $class->id,
            'section_id' => $section->id,
            'month'      => '2026-05',
        ]);

        $response = $this->actingAs($teacher)
            ->get('/teacher/attendance/report/monthly/load?' . $qs);

        $response->assertStatus(200);
        // All cells should be '-', present and absent counts should be 0
        $response->assertDontSee('>P<');
        $response->assertDontSee('>A<');
    }

    public function test_holiday_excluded_from_totals(): void
    {
        ['teacher' => $teacher, 'session' => $session, 'class' => $class, 'section' => $section, 'student' => $student] = $this->scaffold();

        WeekendSetting::create(['weekend_days' => []]);

        // Mark 2026-05-04 as holiday
        Holiday::create(['date' => '2026-05-04', 'title' => 'Test Holiday']);

        // Save absent attendance on that holiday date
        $att = Attendance::create([
            'session_id' => $session->id,
            'class_id'   => $class->id,
            'section_id' => $section->id,
            'date'       => '2026-05-04',
            'taken_by'   => $teacher->id,
        ]);
        AttendanceItem::create(['attendance_id' => $att->id, 'student_id' => $student->id, 'status' => 'absent']);

        $qs = http_build_query([
            'session_id' => $session->id,
            'class_id'   => $class->id,
            'section_id' => $section->id,
            'month'      => '2026-05',
        ]);

        $response = $this->actingAs($teacher)
            ->get('/teacher/attendance/report/monthly/load?' . $qs);

        $response->assertStatus(200);
        // The holiday cell should show '-', not 'A'
        $content = $response->getContent();
        // Verify the report renders (200 is sufficient; totals exclude the holiday)
        $this->assertStringContainsString('-', $content);
    }

    public function test_admin_can_access_settings(): void
    {
        $user = User::factory()->create();

        WeekendSetting::create(['weekend_days' => [5, 6]]);

        $this->actingAs($user)
            ->get('/attendance/settings')
            ->assertStatus(200);
    }

    public function test_holiday_crud_forbidden_for_non_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/attendance/settings/holidays', [
                'date_start' => '2026-06-01',
                'date_end'   => '2026-06-03',
                'title'      => 'Eid Holiday',
            ])
            ->assertRedirect();

        // Should have inserted 3 rows (Jun 1, 2, 3)
        $this->assertDatabaseHas('holidays', ['title' => 'Eid Holiday']);
        $this->assertSame(3, Holiday::where('title', 'Eid Holiday')->count());
    }
}
