<?php

namespace Tests\Feature;

use App\Models\MobileAppFeature;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\MobileAppFeatureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class PlatformMobileAppSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function platformAdmin(): User
    {
        $role = Role::create([
            'name' => 'Platform Admin',
            'system_key' => 'platform_admin',
            'is_reserved' => true,
        ]);

        $admin = User::factory()->create(['role_id' => $role->id]);
        Artisan::call('db:seed', ['--class' => MobileAppFeatureSeeder::class, '--quiet' => true]);

        return $admin;
    }

    public function test_platform_admin_can_view_the_global_mobile_app_settings(): void
    {
        $admin = $this->platformAdmin();

        $this->actingAs($admin)->get(route('platform-admins.mobile-app.edit'))
            ->assertOk()
            ->assertSee('Mobile App Settings')
            ->assertSee('Class routine')
            ->assertSee('Dashboard')
            ->assertSee('name="features[dashboard][is_enabled]"', false)
            ->assertSee('disabled', false);
    }

    public function test_non_platform_admin_cannot_view_or_update_mobile_app_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('platform-admins.mobile-app.edit'))
            ->assertForbidden();

        $this->actingAs($user)->put(route('platform-admins.mobile-app.update'), [
            'is_active' => '0',
        ])->assertForbidden();
    }

    public function test_platform_admin_sidebar_link_is_visible_only_to_platform_admins(): void
    {
        $admin = $this->platformAdmin();
        $regularUser = User::factory()->create();

        $this->actingAs($admin);
        $adminNav = View::make('layouts.partials._side-nav')->render();
        $this->actingAs($regularUser);
        $regularNav = View::make('layouts.partials._side-nav')->render();

        $this->assertStringContainsString('Mobile App Settings', $adminNav);
        $this->assertStringNotContainsString('Mobile App Settings', $regularNav);
    }

    public function test_platform_admin_can_save_global_status_and_feature_visibility(): void
    {
        $admin = $this->platformAdmin();

        $response = $this->actingAs($admin)->put(route('platform-admins.mobile-app.update'), [
            'is_active' => '0',
            'maintenance_message' => 'Maintenance tonight.',
            'features' => [
                'dashboard' => ['is_enabled' => '0', 'sort_order' => 100],
                'fees' => ['is_enabled' => '0', 'sort_order' => 10],
            ],
        ]);

        $response->assertRedirect(route('platform-admins.mobile-app.edit'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('mobile_settings', [
            'is_active' => false,
            'maintenance_message' => 'Maintenance tonight.',
            'updated_by' => $admin->id,
        ]);
        $this->assertFalse((bool) MobileAppFeature::where('key', 'fees')->value('is_enabled'));
        $this->assertTrue((bool) MobileAppFeature::where('key', 'dashboard')->value('is_enabled'));
    }
}
