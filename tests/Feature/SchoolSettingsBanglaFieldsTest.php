<?php

namespace Tests\Feature;

use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolSettingsBanglaFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_settings_page_shows_bangla_and_code_fields(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $response = $this->actingAs($user)->get(route('school-settings.index'));

        $response->assertOk()
            ->assertSee('School Name in Bangla')
            ->assertSee('Address in Bangla')
            ->assertSee('IPEMIS Code')
            ->assertSee('School Code')
            ->assertSee('name="name_bn"', false)
            ->assertSee('name="address_bn"', false)
            ->assertSee('name="ipemis_code"', false)
            ->assertSee('name="school_code"', false);
    }

    public function test_school_settings_update_stores_bangla_and_code_fields(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $this->actingAs($user)->put(route('school-settings.update'), [
            'name' => 'Green Chartered School and College',
            'address' => 'Dohazari, Chattogram',
            'name_bn' => 'গ্রিন চার্টার্ড স্কুল অ্যান্ড কলেজ',
            'address_bn' => 'দোহাজারী, চট্টগ্রাম',
            'ipemis_code' => '123456789',
            'school_code' => 'GCSC-01',
        ])->assertRedirect(route('school-settings.index'));

        $this->assertDatabaseHas('school_settings', [
            'name_bn' => 'গ্রিন চার্টার্ড স্কুল অ্যান্ড কলেজ',
            'address_bn' => 'দোহাজারী, চট্টগ্রাম',
            'ipemis_code' => '123456789',
            'school_code' => 'GCSC-01',
        ]);
    }
}
