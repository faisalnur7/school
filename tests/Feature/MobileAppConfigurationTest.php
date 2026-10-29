<?php

namespace Tests\Feature;

use App\Models\MobileAppFeature;
use App\Models\MobileSetting;
use App\Services\MobileAppConfigurationService;
use Database\Seeders\MobileAppFeatureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Tests\TestCase;

class MobileAppConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_mobile_settings_have_safe_defaults(): void
    {
        $settings = MobileSetting::current();

        $this->assertTrue($settings->is_active);
        $this->assertNull($settings->maintenance_message);
        $this->assertSame(1, $settings->id);
    }

    public function test_feature_seeder_creates_the_current_mobile_screen_registry_in_order(): void
    {
        Artisan::call('db:seed', ['--class' => MobileAppFeatureSeeder::class, '--quiet' => true]);

        $this->assertSame(
            [
                'dashboard',
                'class_routine',
                'exam_routine',
                'results',
                'attendance',
                'fees',
                'notices',
                'homework',
                'notifications',
                'holidays',
            ],
            MobileAppFeature::query()->orderBy('sort_order')->pluck('key')->all(),
        );

        $this->assertTrue(MobileAppFeature::where('key', 'dashboard')->value('is_enabled'));
        $this->assertSame('/class-routine', MobileAppFeature::where('key', 'class_routine')->value('flutter_route'));
    }

    public function test_seeder_is_idempotent_and_preserves_platform_admin_changes(): void
    {
        Artisan::call('db:seed', ['--class' => MobileAppFeatureSeeder::class, '--quiet' => true]);

        MobileAppFeature::where('key', 'fees')->update([
            'label' => 'School Payments',
            'icon' => 'payments_rounded',
            'sort_order' => 90,
            'is_enabled' => false,
        ]);

        Artisan::call('db:seed', ['--class' => MobileAppFeatureSeeder::class, '--quiet' => true]);

        $feature = MobileAppFeature::where('key', 'fees')->firstOrFail();
        $this->assertSame('School Payments', $feature->label);
        $this->assertSame('payments_rounded', $feature->icon);
        $this->assertSame(90, $feature->sort_order);
        $this->assertFalse($feature->is_enabled);
        $this->assertCount(10, MobileAppFeature::all());
    }

    public function test_configuration_service_keeps_dashboard_enabled_and_updates_global_settings_atomically(): void
    {
        Artisan::call('db:seed', ['--class' => MobileAppFeatureSeeder::class, '--quiet' => true]);
        $service = app(MobileAppConfigurationService::class);
        $actor = User::factory()->create();

        $service->update(
            ['is_active' => false, 'maintenance_message' => 'Temporarily unavailable.'],
            [
                ['key' => 'dashboard', 'is_enabled' => false, 'sort_order' => 50],
                ['key' => 'fees', 'is_enabled' => false, 'sort_order' => 10],
            ],
            $actor->id,
        );

        $this->assertFalse(MobileSetting::current()->is_active);
        $this->assertSame('Temporarily unavailable.', MobileSetting::current()->maintenance_message);
        $this->assertTrue(MobileAppFeature::where('key', 'dashboard')->value('is_enabled'));
        $this->assertSame(10, MobileAppFeature::where('key', 'fees')->value('sort_order'));
        $this->assertSame($actor->id, MobileSetting::current()->updated_by);
    }
}
