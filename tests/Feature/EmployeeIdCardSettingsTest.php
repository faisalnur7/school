<?php

namespace Tests\Feature;

use App\Models\AdmitSeatCardSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeIdCardSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_id_card_uses_a_separate_preset(): void
    {
        $employee = AdmitSeatCardSetting::current(5);
        $student = AdmitSeatCardSetting::current(3);

        $employee->card_title_font_size = 6.1;
        $employee->save();

        $this->assertSame(5, $employee->card_type);
        $this->assertSame(6.1, (float) AdmitSeatCardSetting::findOrFail($employee->id)->card_title_font_size);
        $this->assertNotSame(6.1, (float) $student->card_title_font_size);
        $this->assertSame(3, $student->card_type);
    }

    public function test_employee_settings_route_saves_only_the_employee_preset(): void
    {
        $admin = User::factory()->create(['is_super_admin' => true]);

        $this->actingAs($admin)
            ->post(route('hr.employee-id-cards.settings'), [
                'cards_per_page' => 4,
                'cards_per_row' => 2,
                'card_width_value' => 6.2,
                'card_height_value' => 8.8,
                'grid_gap_value' => 0.5,
                'card_dimension_unit' => 'cm',
                'card_color_type' => 'solid',
                'card_solid_color' => '#14532d',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(6.2, (float) AdmitSeatCardSetting::where('card_type', 5)->value('card_width_value'));
        $this->assertSame('#14532d', AdmitSeatCardSetting::where('card_type', 5)->value('card_solid_color'));
        $this->assertNotSame(6.2, (float) AdmitSeatCardSetting::where('card_type', 3)->value('card_width_value'));
    }
}
