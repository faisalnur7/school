<?php

namespace Tests\Feature;

use App\Models\EducationalQualification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EducationalQualificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_manage_educational_qualifications(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $this->actingAs($user)
            ->post(route('educational-qualifications.store'), [
                'name' => 'Bachelor of Education',
                'abbreviation' => 'B.Ed.',
                'status' => 'active',
            ])
            ->assertRedirect(route('educational-qualifications.index'));

        $qualification = EducationalQualification::firstOrFail();

        $this->assertSame('Bachelor of Education', $qualification->name);
        $this->assertSame('B.Ed.', $qualification->abbreviation);

        $this->actingAs($user)
            ->put(route('educational-qualifications.update', $qualification), [
                'name' => 'Master of Education',
                'abbreviation' => 'M.Ed.',
                'status' => 'inactive',
            ])
            ->assertRedirect(route('educational-qualifications.index'));

        $this->assertDatabaseHas('educational_qualifications', [
            'name' => 'Master of Education',
            'abbreviation' => 'M.Ed.',
            'status' => 'inactive',
        ]);

        $this->actingAs($user)
            ->get(route('educational-qualifications.index'))
            ->assertOk()
            ->assertSee('Master of Education')
            ->assertSee('M.Ed.')
            ->assertSee('Inactive')
            ->assertSee('qualifications-page', false)
            ->assertSee('qualifications-action-edit', false)
            ->assertSee('qualifications-action-delete', false)
            ->assertSee('btn-primary', false);

        $this->actingAs($user)
            ->delete(route('educational-qualifications.destroy', $qualification))
            ->assertRedirect(route('educational-qualifications.index'));

        $this->assertDatabaseMissing('educational_qualifications', ['id' => $qualification->id]);
    }

    public function test_qualification_name_and_abbreviation_are_required_and_unique(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $this->actingAs($user)
            ->from(route('educational-qualifications.create'))
            ->post(route('educational-qualifications.store'), [])
            ->assertSessionHasErrors(['name', 'abbreviation']);

        EducationalQualification::create([
            'name' => 'Higher Secondary Certificate',
            'abbreviation' => 'H.S.C.',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->from(route('educational-qualifications.create'))
            ->post(route('educational-qualifications.store'), [
                'name' => 'Higher Secondary Certificate',
                'abbreviation' => 'H.S.C.',
                'status' => 'active',
            ])
            ->assertSessionHasErrors(['name', 'abbreviation']);
    }

    public function test_institute_hub_shows_qualifications_card_to_authorized_user(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $this->actingAs($user)
            ->get(route('institute.hub'))
            ->assertOk()
            ->assertSee('Qualifications')
            ->assertSee(route('educational-qualifications.index'), false);
    }
}
