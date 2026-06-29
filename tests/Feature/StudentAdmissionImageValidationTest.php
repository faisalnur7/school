<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StudentAdmissionImageValidationTest extends TestCase
{
    use RefreshDatabase;

    private function admissionPayload(array $overrides = []): array
    {
        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status' => 1,
        ]);

        $class = SchoolClass::create([
            'name_en' => 'Class 1',
            'name_bn' => 'Class 1',
            'status' => 1,
        ]);

        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'A',
            'name_bn' => 'A',
        ]);

        return array_merge([
            'full_name_en' => 'Test Student',
            'full_name_bn' => 'টেস্ট স্টুডেন্ট',
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'group_id' => null,
            'roll' => null,
            'gender' => null,
            'religion' => null,
            'blood_group' => null,
            'disable' => 0,
        ], $overrides);
    }

    public function test_admission_accepts_image_within_allowed_dimensions(): void
    {
        $response = $this->withoutMiddleware()->post(
            route('students.admission.store'),
            $this->admissionPayload([
                'image' => UploadedFile::fake()->image('student.jpg', 295, 445),
            ])
        );

        $response->assertRedirect(route('students.index'));
        $this->assertDatabaseCount('students', 1);
        $this->assertDatabaseHas('students', [
            'full_name_en' => 'Test Student',
        ]);

        $this->assertNotNull(Student::first()?->image);
    }

    public function test_admission_rejects_image_outside_allowed_dimensions(): void
    {
        $response = $this->withoutMiddleware()->post(
            route('students.admission.store'),
            $this->admissionPayload([
                'image' => UploadedFile::fake()->image('student.jpg', 280, 445),
            ])
        );

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('students', 0);
    }
}
