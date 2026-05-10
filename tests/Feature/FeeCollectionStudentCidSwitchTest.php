<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeCollectionStudentCidSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_switch_route_requires_authentication(): void
    {
        $this->post('/fees/switch-student', ['student_cid' => 'STU-1'])
            ->assertRedirect('/login');
    }

    public function test_authenticated_user_can_switch_student_by_cid(): void
    {
        $user = User::factory()->create();

        $s1 = Student::create([
            'full_name_en' => 'Alice',
            'student_cid' => 'STU-2026-001',
            'status' => 1,
        ]);

        $s2 = Student::create([
            'full_name_en' => 'Bob',
            'student_cid' => 'STU-2026-002',
            'status' => 1,
        ]);

        $this->actingAs($user)
            ->postJson('/fees/switch-student', ['student_cid' => $s2->student_cid])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'student_id' => $s2->id,
                'student_name' => 'Bob',
                'redirect_url' => route('fees.collect_payment', $s2->id),
            ]);
    }

    public function test_empty_cid_returns_validation_message(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/fees/switch-student', ['student_cid' => ''])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Please enter a valid student CID.',
            ]);
    }

    public function test_non_existent_cid_returns_not_found_message(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/fees/switch-student', ['student_cid' => 'STU-NOT-EXISTS'])
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'No student found with CID: STU-NOT-EXISTS. Please check and try again.',
            ]);
    }
}

