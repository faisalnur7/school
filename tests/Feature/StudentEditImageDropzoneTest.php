<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentEditImageDropzoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_page_renders_dropzone_and_existing_image_preview(): void
    {
        $student = Student::create([
            'full_name_en' => 'Existing Student',
            'image' => 'uploads/students/existing.jpg',
        ]);

        $response = $this->withoutMiddleware()->get(route('students.edit', $student->id));

        $response->assertOk();
        $response->assertSee('studentImageDropzone');
        $response->assertSee('data-existing-image-url', false);
        $response->assertSee('uploads/students/existing.jpg');
    }
}
