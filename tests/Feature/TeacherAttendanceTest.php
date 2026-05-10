<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\TeacherSectionAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_attendance(): void
    {
        $teacher = User::factory()->create();

        $session = AcademicSession::create(['name_bn' => 'S', 'name_en' => 'S', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'C', 'name_en' => 'C', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);

        TeacherSectionAssignment::create([
            'user_id' => $teacher->id,
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
        ]);

        $s1 = Student::create(['full_name_en' => 'Alice']);
        $s2 = Student::create(['full_name_en' => 'Bob']);

        StudentAcademicInformation::create([
            'student_id' => $s1->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '1',
        ]);
        StudentAcademicInformation::create([
            'student_id' => $s2->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '2',
        ]);

        $this->actingAs($teacher)
            ->post('/teacher/attendance', [
                'session_id' => $session->id,
                'class_id' => $class->id,
                'section_id' => $section->id,
                'date' => '2026-05-10',
                'student_ids' => [$s1->id, $s2->id],
                'present_ids' => [$s1->id],
            ])
            ->assertRedirect();

        $attendance = Attendance::query()
            ->where('session_id', $session->id)
            ->where('class_id', $class->id)
            ->where('section_id', $section->id)
            ->whereDate('date', '2026-05-10')
            ->first();

        $this->assertNotNull($attendance);
        $this->assertDatabaseHas('attendance_items', [
            'attendance_id' => $attendance->id,
            'student_id' => $s1->id,
            'status' => 'present',
        ]);
        $this->assertDatabaseHas('attendance_items', [
            'attendance_id' => $attendance->id,
            'student_id' => $s2->id,
            'status' => 'absent',
        ]);
    }

    public function test_teacher_can_edit_attendance_after_submit(): void
    {
        $teacher = User::factory()->create();

        $session = AcademicSession::create(['name_bn' => 'S', 'name_en' => 'S', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'C', 'name_en' => 'C', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);

        TeacherSectionAssignment::create([
            'user_id' => $teacher->id,
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
        ]);

        $s1 = Student::create(['full_name_en' => 'Alice']);
        $s2 = Student::create(['full_name_en' => 'Bob']);
        StudentAcademicInformation::insert([
            [
                'student_id' => $s1->id,
                'academic_session_id' => $session->id,
                'school_class_id' => $class->id,
                'section_id' => $section->id,
                'roll' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => $s2->id,
                'academic_session_id' => $session->id,
                'school_class_id' => $class->id,
                'section_id' => $section->id,
                'roll' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->actingAs($teacher)->post('/teacher/attendance', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'date' => '2026-05-10',
            'student_ids' => [$s1->id, $s2->id],
            'present_ids' => [$s1->id, $s2->id],
        ]);

        $attendance = Attendance::query()->firstOrFail();

        $this->actingAs($teacher)
            ->patch('/teacher/attendance/'.$attendance->id, [
                'session_id' => $session->id,
                'class_id' => $class->id,
                'section_id' => $section->id,
                'date' => '2026-05-10',
                'student_ids' => [$s1->id, $s2->id],
                'present_ids' => [$s2->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('attendance_items', [
            'attendance_id' => $attendance->id,
            'student_id' => $s1->id,
            'status' => 'absent',
        ]);
        $this->assertDatabaseHas('attendance_items', [
            'attendance_id' => $attendance->id,
            'student_id' => $s2->id,
            'status' => 'present',
        ]);
    }

    public function test_unique_per_date_enforced(): void
    {
        $teacher = User::factory()->create();

        $session = AcademicSession::create(['name_bn' => 'S', 'name_en' => 'S', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'C', 'name_en' => 'C', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);

        TeacherSectionAssignment::create([
            'user_id' => $teacher->id,
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
        ]);

        $s1 = Student::create(['full_name_en' => 'Alice']);
        StudentAcademicInformation::create([
            'student_id' => $s1->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '1',
        ]);

        $this->actingAs($teacher)->post('/teacher/attendance', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'date' => '2026-05-10',
            'student_ids' => [$s1->id],
            'present_ids' => [$s1->id],
        ]);

        $this->actingAs($teacher)->post('/teacher/attendance', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'date' => '2026-05-10',
            'student_ids' => [$s1->id],
            'present_ids' => [],
        ]);

        $this->assertSame(1, Attendance::count());
    }

    public function test_unauthorized_teacher_forbidden(): void
    {
        $teacher = User::factory()->create();
        $otherTeacher = User::factory()->create();

        $session = AcademicSession::create(['name_bn' => 'S', 'name_en' => 'S', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'C', 'name_en' => 'C', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);

        TeacherSectionAssignment::create([
            'user_id' => $teacher->id,
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
        ]);

        $s1 = Student::create(['full_name_en' => 'Alice']);
        StudentAcademicInformation::create([
            'student_id' => $s1->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '1',
        ]);

        $this->actingAs($otherTeacher)
            ->get('/teacher/attendance/load?session_id='.$session->id.'&class_id='.$class->id.'&section_id='.$section->id.'&date=2026-05-10')
            ->assertStatus(403);

        $this->actingAs($otherTeacher)
            ->post('/teacher/attendance', [
                'session_id' => $session->id,
                'class_id' => $class->id,
                'section_id' => $section->id,
                'date' => '2026-05-10',
                'student_ids' => [$s1->id],
                'present_ids' => [$s1->id],
            ])
            ->assertStatus(403);
    }
}

