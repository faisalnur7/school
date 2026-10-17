<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectClassAssignment;
use App\Models\SubjectClassConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectClassConfigDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_subject_class_config_can_be_deleted_without_deleting_subject(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $subject = Subject::create([
            'name' => 'Bangla 1st',
            'code' => 'BAN-CONFIG-DELETE',
            'type' => 'mandatory',
            'creative_marks' => 70,
            'mcq_marks' => 30,
            'tutorial_marks' => 20,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        $schoolClass = SchoolClass::create(['name_en' => 'Nine', 'name_bn' => 'নাইন']);
        $assignment = SubjectClassAssignment::create([
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'gender' => 'all',
            'religion' => 'all',
            'is_optional' => false,
            'is_compulsory' => true,
            'is_active' => true,
        ]);
        $config = SubjectClassConfig::create([
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'creative_marks' => 100,
            'mcq_marks' => 0,
            'practical_marks' => 0,
            'viva_marks' => 0,
            'tutorial_marks' => 20,
            'pass_mark' => 40,
        ]);

        $response = $this->actingAs($user)->delete(
            route('subjects.class-config.delete', [$subject, $config])
        );

        $response->assertRedirect(route('subjects.edit', $subject));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('subject_class_configs', ['id' => $config->id]);
        $this->assertDatabaseHas('subjects', ['id' => $subject->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('subject_class_assignments', [
            'id' => $assignment->id,
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'is_active' => true,
        ]);
    }

    public function test_subject_class_config_cannot_be_deleted_from_another_subject(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $subject = Subject::create([
            'name' => 'Bangla',
            'code' => 'BAN-CONFIG-OWNER',
            'type' => 'mandatory',
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        $otherSubject = Subject::create([
            'name' => 'English',
            'code' => 'ENG-CONFIG-OWNER',
            'type' => 'mandatory',
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        $schoolClass = SchoolClass::create(['name_en' => 'Nine', 'name_bn' => 'নাইন']);
        $config = SubjectClassConfig::create([
            'subject_id' => $otherSubject->id,
            'school_class_id' => $schoolClass->id,
            'creative_marks' => 100,
            'pass_mark' => 40,
        ]);

        $response = $this->actingAs($user)->delete(
            route('subjects.class-config.delete', [$subject, $config])
        );

        $response->assertNotFound();
        $this->assertDatabaseHas('subject_class_configs', ['id' => $config->id]);
    }

    public function test_subject_class_config_can_be_removed_through_browser_compatible_post_endpoint(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $subject = Subject::create([
            'name' => 'Bangla',
            'code' => 'BAN-CONFIG-POST-DELETE',
            'type' => 'mandatory',
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        $schoolClass = SchoolClass::create(['name_en' => 'Nine', 'name_bn' => 'নাইন']);
        $config = SubjectClassConfig::create([
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'creative_marks' => 70,
            'pass_mark' => 33,
        ]);

        $response = $this->actingAs($user)->post(
            route('subjects.class-config.remove', [$subject, $config])
        );

        $response->assertRedirect(route('subjects.edit', $subject));
        $this->assertDatabaseMissing('subject_class_configs', ['id' => $config->id]);
        $this->assertDatabaseHas('subjects', ['id' => $subject->id, 'deleted_at' => null]);
    }

    public function test_subject_edit_updates_an_existing_class_configuration(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $subject = Subject::create([
            'name' => 'Bangla 1st',
            'code' => 'BAN-CONFIG-UPDATE',
            'type' => 'mandatory',
            'creative_marks' => 70,
            'mcq_marks' => 30,
            'tutorial_marks' => 20,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        $schoolClass = SchoolClass::create(['name_en' => 'Nine', 'name_bn' => 'নাইন']);
        $config = SubjectClassConfig::create([
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'creative_marks' => 100,
            'mcq_marks' => 0,
            'practical_marks' => 0,
            'viva_marks' => 0,
            'tutorial_marks' => 20,
            'pass_mark' => 40,
        ]);

        $response = $this->actingAs($user)->put(route('subjects.update', $subject), [
            'name' => $subject->name,
            'code' => $subject->code,
            'type' => $subject->type,
            'creative_marks' => 70,
            'mcq_marks' => 30,
            'tutorial_marks' => 20,
            'pass_mark' => 33,
            'is_active' => 1,
            'assign_to_class' => 0,
            'school_class_ids' => [''],
            'class_configs' => [[
                'class_id' => $schoolClass->id,
                'creative_marks' => 100,
                'mcq_marks' => 0,
                'practical_marks' => 0,
                'viva_marks' => 0,
                'tutorial_marks' => 20,
                'pass_mark' => 33,
            ]],
        ]);

        $response->assertRedirect(route('subjects.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('subject_class_configs', ['id' => $config->id]);
        $this->assertDatabaseHas('subject_class_configs', [
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'pass_mark' => 33,
        ]);
    }

    public function test_subject_edit_exposes_existing_configuration_fields_and_actions(): void
    {
        $html = file_get_contents(resource_path('views/pages/subjects/edit.blade.php'));

        $this->assertStringContainsString('class-config-edit-btn', $html);
        $this->assertStringContainsString('class_configs[{{ $configIndex }}][pass_mark_percentage]', $html);
        $this->assertStringContainsString('class-config-delete-btn', $html);
        $this->assertStringNotContainsString('class-config-delete-form', $html);
    }
}
