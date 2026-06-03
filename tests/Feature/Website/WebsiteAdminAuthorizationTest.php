<?php

namespace Tests\Feature\Website;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteAdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_authorized_user_cannot_access_website_management_pages(): void
    {
        $user = User::factory()->create(['is_super_admin' => false]);

        $response = $this->actingAs($user)->get(route('website.pages.index'));

        $response->assertForbidden();
    }

    public function test_super_admin_can_access_website_management_pages(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $response = $this->actingAs($user)->get(route('website.pages.index'));

        $response->assertOk();
    }
}
