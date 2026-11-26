<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentListSearchByCidTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_list_search_matches_student_cid(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        Student::create([
            'full_name_en' => 'Wanted Student',
            'student_cid' => '260042',
            'status' => 1,
        ]);
        Student::create([
            'full_name_en' => 'Other Student',
            'student_cid' => '260099',
            'status' => 1,
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('students.index', ['search' => '260042']));

        $response->assertOk();
        $response->assertSee('Wanted Student');
        $response->assertDontSee('Other Student');
    }
}
