<?php

namespace Tests\Feature;

use Tests\TestCase;

class StudentMobileApiTest extends TestCase
{
    public function test_student_api_requires_sanctum_authentication(): void
    {
        $this->getJson('/api/v1/student/dashboard')
            ->assertUnauthorized();
    }

    public function test_student_login_requires_credentials(): void
    {
        $this->postJson('/api/v1/student/login', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['login', 'password']);
    }

}
