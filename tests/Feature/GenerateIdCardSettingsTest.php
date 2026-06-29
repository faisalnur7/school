<?php

namespace Tests\Feature;

use App\Http\Controllers\GenerateIdCardController;
use App\Models\AcademicSession;
use App\Models\AdmitSeatCardSetting;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class GenerateIdCardSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_card_color_is_saved_and_applied_to_id_cards(): void
    {
        $session = AcademicSession::create(['name_bn' => 'S', 'name_en' => 'S', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'C', 'name_en' => 'C', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);
        $user = User::factory()->create();

        $student = Student::create([
            'full_name_en' => 'Alice',
            'student_cid' => 'S-1001',
        ]);

        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '1',
            'is_current' => true,
            'academic_status' => 'active',
        ]);

        $controller = app(GenerateIdCardController::class);
        $this->actingAs($user);

        $saveRequest = Request::create('/students/id-cards/settings', 'POST', [
            'card_type' => 'id_card',
            'cards_per_page' => 4,
            'cards_per_row' => 2,
            'card_width_value' => 5.4,
            'card_height_value' => 8.4,
            'grid_gap_value' => 0.5,
            'card_dimension_unit' => 'cm',
            'card_color_type' => 'solid',
            'card_color_gradient_1' => '#123456',
            'card_color_gradient_2' => '#234567',
            'card_solid_color' => '#0f172a',
        ]);

        $controller->saveSettings($saveRequest);

        $setting = AdmitSeatCardSetting::query()->where('card_type', 3)->firstOrFail();
        $this->assertSame('solid', $setting->card_color_type);
        $this->assertSame('#0f172a', $setting->card_solid_color);

        $viewRequest = Request::create('/students/id-cards', 'GET', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'group_id' => '',
            'card_type' => 'id_card',
            'student_cid' => '',
        ]);

        app()->instance('request', $viewRequest);
        app('view')->share('errors', new ViewErrorBag());
        $response = $controller->index($viewRequest);
        $viewData = $response->getData();

        $this->assertSame('solid', $viewData['cardSettings']->card_color_type ?? null);
        $this->assertNotEmpty($viewData['students']);

        $html = $response->render();

        $this->assertMatchesRegularExpression('/card-theme-bg\s*:\s*#0f172a/i', $html);
        $this->assertStringContainsString('name="card_color_type"', $html);
        $this->assertStringContainsString('name="card_solid_color"', $html);
    }

    public function test_card_logo_is_saved_and_rendered_for_id_cards(): void
    {
        $session = AcademicSession::create(['name_bn' => 'S', 'name_en' => 'S', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'C', 'name_en' => 'C', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);
        $user = User::factory()->create();

        $student = Student::create([
            'full_name_en' => 'Bob',
            'student_cid' => 'S-1002',
        ]);

        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '2',
            'is_current' => true,
            'academic_status' => 'active',
        ]);

        $controller = app(GenerateIdCardController::class);
        $this->actingAs($user);

        $logoFile = UploadedFile::fake()->image('card-logo.png')->size(20);
        $saveRequest = Request::create('/students/id-cards/settings', 'POST', [
            'card_type' => 'id_card',
            'cards_per_page' => 4,
            'cards_per_row' => 2,
            'card_width_value' => 5.4,
            'card_height_value' => 8.4,
            'grid_gap_value' => 0.5,
            'card_dimension_unit' => 'cm',
            'card_color_type' => 'gradient',
            'card_color_gradient_1' => '#123456',
            'card_color_gradient_2' => '#234567',
            'card_solid_color' => '#0f172a',
        ], [], ['card_logo' => $logoFile]);

        $controller->saveSettings($saveRequest);

        $setting = AdmitSeatCardSetting::query()->where('card_type', 3)->firstOrFail();
        $this->assertNotEmpty($setting->card_logo);
        $this->assertFileExists(public_path($setting->card_logo));

        $viewRequest = Request::create('/students/id-cards', 'GET', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'group_id' => '',
            'card_type' => 'id_card',
            'student_cid' => '',
        ]);

        app()->instance('request', $viewRequest);
        app('view')->share('errors', new ViewErrorBag());
        $response = $controller->index($viewRequest);

        $html = $response->render();

        $this->assertStringContainsString($setting->card_logo, $html);
        $this->assertStringContainsString('name="card_logo"', $html);
    }

    public function test_transparent_id_cards_use_custom_text_colors(): void
    {
        $session = AcademicSession::create(['name_bn' => 'S', 'name_en' => 'S', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'C', 'name_en' => 'C', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);
        $user = User::factory()->create();

        $student = Student::create([
            'full_name_en' => 'Dana',
            'student_cid' => 'S-1003',
        ]);

        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '3',
            'is_current' => true,
            'academic_status' => 'active',
        ]);

        $controller = app(GenerateIdCardController::class);
        $this->actingAs($user);

        $saveRequest = Request::create('/students/id-cards/settings', 'POST', [
            'card_type' => 'id_card',
            'cards_per_page' => 4,
            'cards_per_row' => 2,
            'card_width_value' => 5.4,
            'card_height_value' => 8.4,
            'grid_gap_value' => 0.5,
            'card_dimension_unit' => 'cm',
            'card_is_transparent' => 1,
            'card_color_type' => 'gradient',
            'card_color_gradient_1' => '#123456',
            'card_color_gradient_2' => '#234567',
            'card_solid_color' => '#0f172a',
            'card_school_name_text_color' => '#111111',
            'card_school_detail_text_color' => '#334155',
            'card_title_text_color' => '#111111',
        ]);

        $controller->saveSettings($saveRequest);

        $setting = AdmitSeatCardSetting::query()->where('card_type', 3)->firstOrFail();
        $this->assertTrue((bool) $setting->card_is_transparent);
        $this->assertSame('#111111', $setting->card_school_name_text_color);
        $this->assertSame('#334155', $setting->card_school_detail_text_color);

        $viewRequest = Request::create('/students/id-cards', 'GET', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'group_id' => '',
            'card_type' => 'id_card',
            'student_cid' => '',
        ]);

        app()->instance('request', $viewRequest);
        app('view')->share('errors', new ViewErrorBag());
        $response = $controller->index($viewRequest);
        $html = $response->render();

        $this->assertStringContainsString('--card-theme-bg: transparent', $html);
        $this->assertStringContainsString('--id-card-school-name-color: #111111', $html);
        $this->assertStringContainsString('--id-card-school-detail-color: #334155', $html);
        $this->assertStringContainsString('name="card_is_transparent"', $html);
    }
}
