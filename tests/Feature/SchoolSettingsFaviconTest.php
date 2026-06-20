<?php

namespace Tests\Feature;

use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SchoolSettingsFaviconTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_settings_page_shows_favicon_upload_field(): void
    {
        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $response = $this->actingAs($user)->get(route('school-settings.index'));

        $response->assertOk();
        $response->assertSee('Favicon');
    }

    public function test_school_settings_update_stores_favicon(): void
    {
        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $response = $this->actingAs($user)->put(route('school-settings.update'), [
            'name' => 'Test School',
            'address' => 'Test Address',
            'favicon' => UploadedFile::fake()->image('favicon.png', 32, 32),
        ]);

        $response->assertRedirect(route('school-settings.index'));

        $setting = SchoolSetting::current();

        $this->assertNotEmpty($setting->favicon);
        $this->assertStringStartsWith('uploads/school_settings/', $setting->favicon);
        $this->assertFileExists(public_path($setting->favicon));
    }
}
