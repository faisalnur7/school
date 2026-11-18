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
        $settingsHtml = $controller->settings($viewRequest)->render();

        $this->assertMatchesRegularExpression('/card-theme-bg\s*:\s*#0f172a/i', $html);
        $this->assertStringContainsString('name="card_color_type"', $settingsHtml);
        $this->assertStringContainsString('name="card_solid_color"', $settingsHtml);
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
        $settingsHtml = $controller->settings($viewRequest)->render();

        $this->assertStringContainsString($setting->card_logo, $settingsHtml);
        $this->assertStringContainsString('name="card_logo"', $settingsHtml);
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
        $settingsHtml = $controller->settings($viewRequest)->render();

        $this->assertStringContainsString('--card-theme-bg: transparent', $html);
        $this->assertStringContainsString('--id-card-school-name-color: #111111', $html);
        $this->assertStringContainsString('--id-card-school-detail-color: #334155', $html);
        $this->assertStringContainsString('name="card_is_transparent"', $settingsHtml);
    }

    public function test_id_card_notice_and_footer_text_colors_are_saved_and_rendered(): void
    {
        $session = AcademicSession::create(['name_bn' => 'S', 'name_en' => 'S', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'C', 'name_en' => 'C', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);
        $user = User::factory()->create();

        $student = Student::create([
            'full_name_en' => 'Eve',
            'student_cid' => 'S-1004',
        ]);

        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '4',
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
            'card_slogan_text_color' => '#0f172a',
            'card_back_notice_text_color' => '#475569',
            'card_footer_text_color' => '#111827',
        ]);

        $controller->saveSettings($saveRequest);

        $setting = AdmitSeatCardSetting::query()->where('card_type', 3)->firstOrFail();
        $this->assertSame('#0f172a', $setting->card_slogan_text_color);
        $this->assertSame('#475569', $setting->card_back_notice_text_color);
        $this->assertSame('#111827', $setting->card_footer_text_color);

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
        $settingsHtml = $controller->settings($viewRequest)->render();

        $this->assertStringContainsString('--id-card-slogan-color: #0f172a', $html);
        $this->assertStringContainsString('--id-card-back-notice-color: #475569', $html);
        $this->assertStringContainsString('--id-card-footer-color: #111827', $html);
        $this->assertStringContainsString('name="card_slogan_text_color"', $settingsHtml);
        $this->assertStringContainsString('name="card_back_notice_text_color"', $settingsHtml);
        $this->assertStringContainsString('name="card_footer_text_color"', $settingsHtml);
    }

    private function seedStudentForCards(): array
    {
        $session = AcademicSession::create(['name_bn' => 'S', 'name_en' => 'S', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'C', 'name_en' => 'C', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);
        $student = Student::create(['full_name_en' => 'Zed', 'student_cid' => 'S-9001']);
        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '9',
            'is_current' => true,
            'academic_status' => 'active',
        ]);
        $this->actingAs(User::factory()->create());

        return [$session, $class, $section];
    }

    private function cardSettingsInput(array $overrides = []): array
    {
        return array_merge([
            'card_type' => 'id_card',
            'cards_per_page' => 4,
            'cards_per_row' => 2,
            'card_width_value' => 5.4,
            'card_height_value' => 8.4,
            'grid_gap_value' => 0.5,
            'card_dimension_unit' => 'cm',
            'card_color_type' => 'solid',
            'card_solid_color' => '#0f172a',
        ], $overrides);
    }

    private function renderCards($session, $class, $section): string
    {
        $request = Request::create('/students/id-cards', 'GET', [
            'session_id' => $session->id, 'class_id' => $class->id, 'section_id' => $section->id,
            'group_id' => '', 'card_type' => 'id_card', 'student_cid' => '',
        ]);
        app()->instance('request', $request);
        app('view')->share('errors', new ViewErrorBag());

        return app(GenerateIdCardController::class)->index($request)->render();
    }

    public function test_name_and_student_detail_settings_are_saved_and_rendered(): void
    {
        [$session, $class, $section] = $this->seedStudentForCards();

        app(GenerateIdCardController::class)->saveSettings(Request::create('/students/id-cards/settings', 'POST', $this->cardSettingsInput([
            'card_name_text_color' => '#aa0000',
            'card_student_detail_text_color' => '#00aa00',
            'card_student_detail_font_size' => 5.5,
        ])));

        $setting = AdmitSeatCardSetting::query()->where('card_type', 3)->firstOrFail();
        $this->assertSame('#aa0000', $setting->card_name_text_color);
        $this->assertSame('#00aa00', $setting->card_student_detail_text_color);
        $this->assertEquals(5.5, (float) $setting->card_student_detail_font_size);

        $html = $this->renderCards($session, $class, $section);
        $this->assertStringContainsString('--id-card-name-color: #aa0000', $html);
        $this->assertStringContainsString('--id-card-student-detail-color: #00aa00', $html);
        $this->assertStringContainsString('--id-card-detail-font-size: 5.5pt', $html);
    }

    public function test_card_too_large_for_the_sheet_is_rejected(): void
    {
        $this->seedStudentForCards();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        // front + back side by side: 2 x 15cm + gap exceeds the 28.1cm printable width.
        app(GenerateIdCardController::class)->saveSettings(Request::create('/students/id-cards/settings', 'POST', $this->cardSettingsInput([
            'card_width_value' => 15,
        ])));
    }

    public function test_layout_that_does_not_fit_is_reported_instead_of_silently_changed(): void
    {
        [$session, $class, $section] = $this->seedStudentForCards();

        // 6.2cm pairs are 12.9cm wide: only 2 fit per row, so 4 per row must be reduced and announced.
        app(GenerateIdCardController::class)->saveSettings(Request::create('/students/id-cards/settings', 'POST', $this->cardSettingsInput([
            'cards_per_page' => 8,
            'cards_per_row' => 4,
            'card_width_value' => 6.2,
            'card_height_value' => 8.8,
        ])));

        $html = $this->renderCards($session, $class, $section);
        $this->assertStringContainsString('Layout adjusted: 8 per page / 4 per row was reduced to', $html);
    }

    public function test_padding_photo_and_logo_are_converted_when_unit_is_px(): void
    {
        [$session, $class, $section] = $this->seedStudentForCards();

        app(GenerateIdCardController::class)->saveSettings(Request::create('/students/id-cards/settings', 'POST', $this->cardSettingsInput([
            'card_dimension_unit' => 'px',
            'card_width_value' => 200,
            'card_height_value' => 320,
            'grid_gap_value' => 20,
            'card_photo_width_value' => 96,
            'card_photo_height_value' => 192,
        ])));

        $html = $this->renderCards($session, $class, $section);
        // 96px = 2.54cm, 192px = 5.08cm
        $this->assertStringContainsString('--id-card-photo-width: 2.54cm', $html);
        $this->assertStringContainsString('--id-card-photo-height: 5.08cm', $html);
    }

    private function addStudent($session, $class, $section, string $name, string $cid, ?string $roll): Student
    {
        $student = Student::create(['full_name_en' => $name, 'student_cid' => $cid]);
        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => $roll,
            'is_current' => true,
            'academic_status' => 'active',
        ]);

        return $student;
    }

    private function settingsPage(array $query = []): string
    {
        $request = Request::create('/students/id-cards/settings', 'GET', array_merge(['card_type' => 'id_card'], $query));
        app()->instance('request', $request);
        app('view')->share('errors', new ViewErrorBag());

        return app(GenerateIdCardController::class)->settings($request)->render();
    }

    private function cardPage(array $query): string
    {
        $request = Request::create('/students/id-cards', 'GET', array_merge(['card_type' => 'id_card'], $query));
        app()->instance('request', $request);
        app('view')->share('errors', new ViewErrorBag());

        return app(GenerateIdCardController::class)->index($request)->render();
    }

    public function test_cards_load_only_when_year_class_and_section_are_selected(): void
    {
        [$session, $class, $section] = $this->seedStudentForCards();

        $withoutSection = $this->cardPage(['session_id' => $session->id, 'class_id' => $class->id]);
        $this->assertStringNotContainsString('class="id-card-pages"', $withoutSection);
        $this->assertStringContainsString('Section</strong>', $withoutSection);

        $complete = $this->cardPage(['session_id' => $session->id, 'class_id' => $class->id, 'section_id' => $section->id]);
        $this->assertStringContainsString('class="id-card-pages"', $complete);

        $byCid = $this->cardPage(['student_cid' => 'S-9001']);
        $this->assertStringContainsString('class="id-card-pages"', $byCid);
    }

    public function test_cards_are_sorted_by_natural_roll_with_missing_rolls_last(): void
    {
        [$session, $class, $section] = $this->seedStudentForCards();
        $this->addStudent($session, $class, $section, 'Ten', 'S-10', '10');
        $this->addStudent($session, $class, $section, 'Two', 'S-02', '2');
        $this->addStudent($session, $class, $section, 'Nada', 'S-00', null);

        $html = $this->cardPage(['session_id' => $session->id, 'class_id' => $class->id, 'section_id' => $section->id]);
        preg_match_all('/class="id-card__name"[^>]*>([^<]+)</', $html, $m);

        // rolls 2, 9 (Zed), 10, then no roll
        $this->assertSame(['Two', 'Zed', 'Ten', 'Nada'], $m[1]);
    }

    public function test_paper_size_and_orientation_drive_the_fit_check(): void
    {
        $this->seedStudentForCards();

        // 12.9cm pairs fit A4 landscape but not A4 portrait (19.4cm printable width).
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(GenerateIdCardController::class)->saveSettings(Request::create('/students/id-cards/settings', 'POST', $this->cardSettingsInput([
            'card_width_value' => 10,
            'card_page_orientation' => 'portrait',
        ])));
    }

    public function test_page_settings_are_saved_and_cards_per_row_cannot_exceed_cards_per_page(): void
    {
        [$session, $class, $section] = $this->seedStudentForCards();

        app(GenerateIdCardController::class)->saveSettings(Request::create('/students/id-cards/settings', 'POST', $this->cardSettingsInput([
            'card_page_size' => 'letter',
            'card_page_orientation' => 'portrait',
        ])));

        $setting = AdmitSeatCardSetting::query()->where('card_type', 3)->firstOrFail();
        $this->assertSame('letter', $setting->card_page_size);
        $this->assertSame('portrait', $setting->card_page_orientation);

        $html = $this->cardPage(['session_id' => $session->id, 'class_id' => $class->id, 'section_id' => $section->id]);
        $this->assertStringContainsString('size: Letter portrait', $html);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(GenerateIdCardController::class)->saveSettings(Request::create('/students/id-cards/settings', 'POST', $this->cardSettingsInput([
            'cards_per_page' => 2,
            'cards_per_row' => 4,
        ])));
    }

    public function test_standard_id_card_defaults_are_exposed_for_reset(): void
    {
        [$session, $class, $section] = $this->seedStudentForCards();

        $defaults = AdmitSeatCardSetting::idCardDefaults();
        $this->assertEquals(5.4, $defaults['card_width_value']);
        $this->assertEquals(8.56, $defaults['card_height_value']);

        $html = $this->settingsPage(['session_id' => $session->id, 'class_id' => $class->id, 'section_id' => $section->id]);
        $this->assertStringContainsString('idCardResetDefaults', $html);
        $this->assertStringContainsString('"card_height_value":8.56', $html);
    }

    public function test_settings_open_on_a_dedicated_page_not_a_modal(): void
    {
        [$session, $class, $section] = $this->seedStudentForCards();
        $query = ['session_id' => $session->id, 'class_id' => $class->id, 'section_id' => $section->id];

        $cards = $this->cardPage($query);
        $this->assertStringContainsString(route('students.id-cards.settings.edit', ['card_type' => 'id_card'] + $query), str_replace('&amp;', '&', $cards));
        $this->assertStringNotContainsString('data-target="#idCardSettingsModal"', $cards);
        $this->assertStringNotContainsString('id="idCardSettingsModal"', $cards);

        $page = $this->settingsPage($query);
        $this->assertStringContainsString('id="idCardSettingsModal"', $page);
        $this->assertStringContainsString('id-card-dedicated-settings', $page);
        $this->assertStringContainsString('name="card_page_size"', $page);
        $this->assertStringContainsString('Back to cards', $page);
        // the filter form and generated cards are not part of the settings page
        $this->assertStringNotContainsString('id="filterForm"', $page);
        $this->assertStringNotContainsString('class="id-card-pages"', $page);
        // the way back keeps the filters
        $this->assertStringContainsString('class="btn btn-outline-secondary btn-sm align-self-center js-id-card-settings-back"', $page);
        $this->assertStringContainsString('section_id=' . $section->id, str_replace('&amp;', '&', $page));
    }

    public function test_dragged_positions_are_saved_clamped_and_printed_on_cards(): void
    {
        [$session, $class, $section] = $this->seedStudentForCards();

        app(GenerateIdCardController::class)->saveSettings(Request::create('/students/id-cards/settings', 'POST', $this->cardSettingsInput([
            'card_show_back_signature' => 1,
            'card_element_positions' => [
                'front_name' => ['x' => 2.5, 'y' => -1.25],
                'back_signature' => ['x' => 500, 'y' => 0],   // clamped to 100
                'not_a_real_element' => ['x' => 9, 'y' => 9],  // dropped
                'front_logo' => ['x' => 0, 'y' => 0],          // zero offsets are not stored
            ],
        ])));

        $saved = AdmitSeatCardSetting::query()->where('card_type', 3)->firstOrFail()->card_element_positions;
        $this->assertEquals(['x' => 2.5, 'y' => -1.25], $saved['front_name']);
        $this->assertEquals(['x' => 100, 'y' => 0], $saved['back_signature']);
        $this->assertArrayNotHasKey('not_a_real_element', $saved);
        $this->assertArrayNotHasKey('front_logo', $saved);

        $cards = $this->cardPage(['session_id' => $session->id, 'class_id' => $class->id, 'section_id' => $section->id]);
        $this->assertStringContainsString('position: relative; left: 2.5mm; top: -1.25mm;', $cards);
        $this->assertStringContainsString('left: 100mm; top: 0mm;', $cards);

        $settings = $this->settingsPage();
        $this->assertStringContainsString('name="card_element_positions[front_name][x]"', $settings);
        $this->assertStringContainsString('data-pos-key="front_name"', $settings);
        $this->assertStringContainsString('left: 2.5mm; top: -1.25mm;', $settings);
        $this->assertStringContainsString('idCardResetPositions', $settings);
    }

    private function allVisibilityOn(array $except = []): array
    {
        $switches = [
            'card_show_logo_front', 'card_show_logo_back', 'card_show_photo_front',
            'card_show_school_detail_front', 'card_show_school_detail_back',
            'card_show_slogan_front', 'card_show_slogan_back', 'card_show_title_front', 'card_show_title_back',
            'card_show_footer_front', 'card_show_footer_back', 'card_show_back_student_details',
            'card_show_back_school_contact', 'card_show_back_qr', 'card_show_back_signature', 'card_show_back_notice',
        ];

        return collect($switches)->mapWithKeys(fn ($name) => [$name => in_array($name, $except, true) ? 0 : 1])->all();
    }

    private function printedCards(array $switchesOff): string
    {
        [$session, $class, $section] = $this->seedStudentForCards();
        SchoolSetting::query()->updateOrCreate(['id' => 1], [
            'name' => 'Green School', 'address' => 'Road 5, Dhaka', 'slogan' => 'Be Bright',
            'contact_number_1' => '01700000001', 'website' => 'green.test', 'whatsapp_number' => '01800000002',
        ]);

        app(GenerateIdCardController::class)->saveSettings(Request::create('/students/id-cards/settings', 'POST',
            $this->cardSettingsInput($this->allVisibilityOn($switchesOff))));

        return $this->cardPage(['session_id' => $session->id, 'class_id' => $class->id, 'section_id' => $section->id]);
    }

    public function test_every_visibility_switch_controls_its_element_on_the_printed_card(): void
    {
        $everythingOn = $this->printedCards([]);
        $this->assertStringContainsString('class="id-card__school-detail"', $everythingOn);   // front address
        $this->assertStringContainsString('class="id-card__slogan"', $everythingOn);
        $this->assertStringContainsString('class="id-card__footer id-card__footer--front"', $everythingOn);
        $this->assertStringContainsString('class="id-card__footer id-card__footer--back"', $everythingOn);
        $this->assertStringContainsString('class="id-card__back-section id-card__back-section--student-details"', $everythingOn);
        $this->assertStringContainsString('class="id-card__back-section id-card__back-section--school-contact"', $everythingOn);
        $this->assertStringContainsString('class="id-card__back-notice"', $everythingOn);
        $this->assertStringContainsString('class="id-card__photo"', $everythingOn);
        $this->assertStringContainsString('Road 5, Dhaka', $everythingOn);
    }

    public function test_switching_front_school_detail_off_removes_the_front_address_only(): void
    {
        $html = $this->printedCards(['card_show_school_detail_front']);
        $this->assertStringNotContainsString('class="id-card__school-detail"', $html);
        // the back contact block still lists the address
        $this->assertStringContainsString('Road 5, Dhaka', $html);
    }

    public function test_switching_back_school_detail_off_removes_the_back_address_row(): void
    {
        $html = $this->printedCards(['card_show_school_detail_back', 'card_show_school_detail_front']);
        $this->assertStringNotContainsString('Road 5, Dhaka', $html);
        $this->assertStringContainsString('class="id-card__back-section id-card__back-section--school-contact"', $html);
    }

    public function test_hidden_footers_leave_no_footer_block_behind(): void
    {
        $html = $this->printedCards(['card_show_footer_front', 'card_show_footer_back']);
        $this->assertStringNotContainsString('class="id-card__footer id-card__footer--front"', $html);
        $this->assertStringNotContainsString('class="id-card__footer id-card__footer--back"', $html);
    }

    public static function hidingSwitches(): array
    {
        return [
            'back notice' => ['card_show_back_notice', 'class="id-card__back-notice"'],
            'back signature' => ['card_show_back_signature', 'class="id-card__signature'],
            'back school contact' => ['card_show_back_school_contact', 'class="id-card__back-section id-card__back-section--school-contact"'],
            'back student details' => ['card_show_back_student_details', 'class="id-card__back-section id-card__back-section--student-details"'],
            'front photo' => ['card_show_photo_front', 'class="id-card__photo"'],
            'front title' => ['card_show_title_front', '>STUDENT ID</div>'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('hidingSwitches')]
    public function test_each_remaining_switch_hides_its_element(string $switch, string $needle): void
    {
        $html = $this->printedCards([$switch]);
        $this->assertStringNotContainsString($needle, $html, "$switch should hide its element");
    }

    public function test_settings_page_groups_visibility_into_front_and_back_sections_with_every_switch(): void
    {
        $this->seedStudentForCards();
        $html = $this->settingsPage();

        $this->assertStringContainsString('Visibility — Front side', $html);
        $this->assertStringContainsString('Visibility — Back side', $html);
        foreach (array_keys($this->allVisibilityOn()) as $name) {
            $this->assertStringContainsString('name="' . $name . '"', $html);
        }
        // front switches come before the back heading, back switches after it
        $backHeading = strpos($html, 'Visibility — Back side');
        $this->assertLessThan($backHeading, strpos($html, 'name="card_show_photo_front"'));
        $this->assertGreaterThan($backHeading, strpos($html, 'name="card_show_back_signature"'));
    }

    public function test_preview_always_renders_toggleable_elements_so_live_switching_works(): void
    {
        $this->seedStudentForCards();
        SchoolSetting::query()->updateOrCreate(['id' => 1], ['name' => 'G', 'address' => 'Road 5', 'slogan' => 'Be Bright']);
        // everything saved as hidden
        app(GenerateIdCardController::class)->saveSettings(Request::create('/students/id-cards/settings', 'POST', $this->cardSettingsInput()));

        $html = $this->settingsPage();
        // hidden by class, not omitted from the markup
        $this->assertMatchesRegularExpression('/id-card__slogan[^"]*d-none/', $html);
        $this->assertMatchesRegularExpression('/id-card__school-detail[^"]*d-none/', $html);
        $this->assertMatchesRegularExpression('/id-card__photo-wrap[^"]*d-none/', $html);
        $this->assertMatchesRegularExpression('/id-card__back-notice[^"]*d-none/', $html);
    }

    private function pdfFor(array $settingsInput = []): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        [$session, $class, $section] = $this->seedStudentForCards();
        SchoolSetting::query()->updateOrCreate(['id' => 1], [
            'name' => 'Green School', 'address' => 'Road 5, Dhaka', 'slogan' => 'Be Bright',
            'contact_number_1' => '01700000001', 'website' => 'green.test', 'whatsapp_number' => '01800000002',
        ]);
        app(GenerateIdCardController::class)->saveSettings(Request::create('/students/id-cards/settings', 'POST',
            $this->cardSettingsInput($this->allVisibilityOn() + $settingsInput)));

        $request = Request::create('/students/id-cards/pdf', 'GET', [
            'card_type' => 'id_card', 'session_id' => $session->id, 'class_id' => $class->id, 'section_id' => $section->id,
        ]);
        app()->instance('request', $request);

        return app(GenerateIdCardController::class)->pdf($request);
    }

    public function test_student_pdf_renders_one_page_per_sheet_and_does_not_crash(): void
    {
        // mPDF does not understand CSS variables / flex / grid. The old screen template made it crash
        // ("Uninitialized string offset") and emit ~140 pages for a single student.
        $response = $this->pdfFor();

        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
        $this->assertSame(1, preg_match_all('/\/Type\s*\/Page[^s]/', $response->getContent()));
    }

    public function test_settings_page_shows_front_and_back_side_by_side_without_a_switcher(): void
    {
        $this->seedStudentForCards();
        $html = $this->settingsPage();

        $this->assertStringNotContainsString('js-card-preview-side', $html);
        $this->assertStringNotContainsString('Preview side selector', $html);
        $this->assertStringContainsString('id="idCardLivePreviewFront"', $html);
        $this->assertStringContainsString('id="idCardLivePreviewBack"', $html);
        $this->assertStringNotContainsString('id-card id-card--back d-none', $html);
        $this->assertStringContainsString('card-preview-captions', $html);
    }

    public function test_settings_page_has_preview_toolbar_grouped_layout_and_sheet_diagram(): void
    {
        $this->seedStudentForCards();
        $html = $this->settingsPage();

        foreach (['idCardZoomIn', 'idCardZoomOut', 'idCardZoomLevel', 'idCardResetPositions', 'idCardPreviewPdf', 'idCardSheetDiagram', 'cardSizePreset', 'idCardFooterDirty'] as $id) {
            $this->assertStringContainsString('id="' . $id . '"', $html);
        }
        foreach (['Sheet', 'Card size', 'Alignment &amp; spacing'] as $group) {
            $this->assertStringContainsString($group, $html);
        }
        foreach (['card_page_size', 'card_page_orientation', 'cards_per_page', 'cards_per_row', 'grid_gap_value', 'card_width_value', 'card_height_value', 'card_dimension_unit', 'card_front_alignment', 'card_back_alignment', 'card_front_padding_value', 'card_back_padding_value'] as $name) {
            $this->assertStringContainsString('name="' . $name . '"', $html);
        }
    }

    public function test_measurement_fields_accept_two_decimal_places(): void
    {
        $this->seedStudentForCards();
        $html = $this->settingsPage();

        foreach (['grid_gap_value', 'card_width_value', 'card_height_value', 'card_front_padding_value', 'card_back_padding_value', 'card_front_header_padding_value', 'card_back_header_padding_value', 'card_photo_width_value', 'card_photo_height_value', 'card_logo_size_value'] as $name) {
            $this->assertMatchesRegularExpression('/name="' . $name . '"[^>]*step="0\.01"/', $html, $name);
        }

        app(GenerateIdCardController::class)->saveSettings(Request::create('/s', 'POST', $this->cardSettingsInput(['card_height_value' => 8.56, 'card_front_header_padding_value' => 0.25])));
        $row = AdmitSeatCardSetting::query()->where('card_type', 3)->firstOrFail();
        $this->assertEquals(8.56, (float) $row->card_height_value);
        $this->assertEquals(0.25, (float) $row->card_front_header_padding_value);
    }

    public function test_size_preset_offers_only_cr80_and_custom(): void
    {
        $this->seedStudentForCards();
        $html = $this->settingsPage();

        $this->assertStringContainsString('value="cr80p"', $html);
        $this->assertStringContainsString('value="cr80l"', $html);
        $this->assertStringContainsString('value="custom"', $html);
        $this->assertStringNotContainsString('CR100', $html);
        $this->assertStringNotContainsString('cr100p', $html);
    }

    public function test_pdf_preview_for_one_student_can_open_inline(): void
    {
        $this->seedStudentForCards();
        $request = Request::create('/students/id-cards/pdf', 'GET', ['card_type' => 'id_card', 'preview_student_id' => 'first', 'inline' => 1]);
        app()->instance('request', $request);

        $response = app(GenerateIdCardController::class)->pdf($request);

        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('inline;', $response->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_header_padding_is_saved_and_applied_to_preview_cards_and_pdf(): void
    {
        [$session, $class, $section] = array_values($this->seedStudentForCards());
        SchoolSetting::query()->updateOrCreate(['id' => 1], ['name' => 'Green School', 'address' => 'Road 5']);
        app(GenerateIdCardController::class)->saveSettings(Request::create('/s', 'POST', $this->cardSettingsInput([
            'card_front_header_padding_value' => 0.45,
            'card_back_header_padding_value' => 0.1,
        ])));

        $row = AdmitSeatCardSetting::query()->where('card_type', 3)->firstOrFail();
        $this->assertEquals(0.45, (float) $row->card_front_header_padding_value);
        $this->assertEquals(0.1, (float) $row->card_back_header_padding_value);

        $settings = $this->settingsPage();
        $this->assertStringContainsString('name="card_front_header_padding_value"', $settings);
        $this->assertStringContainsString('name="card_back_header_padding_value"', $settings);
        $this->assertStringContainsString('--id-card-front-header-padding:0.45cm', $settings);
        $this->assertStringContainsString('--id-card-back-header-padding:0.1cm', $settings);

        $cards = $this->cardPage(['session_id' => $session->id, 'class_id' => $class->id, 'section_id' => $section->id]);
        $this->assertStringContainsString('--id-card-front-header-padding: 0.45cm', $cards);
        $this->assertStringContainsString('--id-card-back-header-padding: 0.1cm', $cards);

        $layout = ['cardsPerPage' => 4, 'cardsPerRow' => 2, 'cardWidthCm' => 5.4, 'cardHeightCm' => 8.4, 'gridGapCm' => 0.5];
        $pdf = view('pages.generate-id-cards._cards-pdf', [
            'students' => collect([Student::query()->with('academicInformations.schoolClass')->first()]),
            'setting' => SchoolSetting::current(), 'cardSettings' => $row, 'layout' => $layout,
        ])->render();
        $this->assertStringContainsString('padding:0.45cm;', $pdf);
        $this->assertStringContainsString('padding:0.1cm;', $pdf);
    }

    public function test_header_padding_rejects_negative_values_and_blank_falls_back_to_default(): void
    {
        $this->seedStudentForCards();
        app(GenerateIdCardController::class)->saveSettings(Request::create('/s', 'POST', $this->cardSettingsInput([
            'card_front_header_padding_value' => '',
        ])));
        $this->assertEquals(0.3, (float) AdmitSeatCardSetting::query()->where('card_type', 3)->firstOrFail()->card_front_header_padding_value);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(GenerateIdCardController::class)->saveSettings(Request::create('/s', 'POST', $this->cardSettingsInput([
            'card_back_header_padding_value' => -1,
        ])));
    }

    public function test_pdf_template_uses_literal_settings_not_css_variables(): void
    {
        $this->seedStudentForCards();
        SchoolSetting::query()->updateOrCreate(['id' => 1], ['name' => 'Green School', 'address' => 'Road 5']);
        app(GenerateIdCardController::class)->saveSettings(Request::create('/s', 'POST', $this->cardSettingsInput($this->allVisibilityOn() + [
            'card_color_type' => 'gradient', 'card_color_gradient_1' => '#112233', 'card_color_gradient_2' => '#445566',
            'card_school_name_text_color' => '#aa0000', 'card_student_detail_font_size' => 6.5,
            'card_student_detail_text_color' => '#00aa00', 'card_name_text_color' => '#0000aa',
        ])));
        $setting = AdmitSeatCardSetting::query()->where('card_type', 3)->firstOrFail();
        $layout = ['cardsPerPage' => 4, 'cardsPerRow' => 2, 'cardWidthCm' => 5.4, 'cardHeightCm' => 8.56, 'gridGapCm' => 0.5];
        $student = Student::query()->with('academicInformations.schoolClass')->first();

        $html = view('pages.generate-id-cards._cards-pdf', [
            'students' => collect([$student]), 'setting' => SchoolSetting::current(), 'cardSettings' => $setting, 'layout' => $layout,
        ])->render();

        $this->assertStringNotContainsString('var(--', $html);
        $this->assertStringNotContainsString('display:flex', str_replace(' ', '', $html));
        $this->assertStringContainsString('background-gradient: linear #112233 #445566', $html);
        $this->assertStringContainsString('color:#aa0000', str_replace(' ', '', $html));      // school name colour
        $this->assertStringContainsString('font-size:6.5pt', str_replace(' ', '', $html));    // student detail size
        $this->assertStringContainsString('color:#00aa00', str_replace(' ', '', $html));      // student detail colour
        $this->assertStringContainsString('color:#0000aa', str_replace(' ', '', $html));      // student name colour
    }

    public function test_pdf_honours_hidden_elements_and_front_address(): void
    {
        $this->seedStudentForCards();
        SchoolSetting::query()->updateOrCreate(['id' => 1], ['name' => 'Green School', 'address' => 'Road 5', 'slogan' => 'Be Bright', 'contact_number_1' => '0170']);
        $render = function (array $off) {
            app(GenerateIdCardController::class)->saveSettings(Request::create('/s', 'POST', $this->cardSettingsInput($this->allVisibilityOn($off))));
            $setting = AdmitSeatCardSetting::query()->where('card_type', 3)->firstOrFail();
            $layout = ['cardsPerPage' => 4, 'cardsPerRow' => 2, 'cardWidthCm' => 5.4, 'cardHeightCm' => 8.56, 'gridGapCm' => 0.5];

            return view('pages.generate-id-cards._cards-pdf', [
                'students' => Student::query()->with('academicInformations.schoolClass')->get(), 'setting' => SchoolSetting::current(),
                'cardSettings' => $setting, 'layout' => $layout,
            ])->render();
        };

        $all = $render([]);
        $this->assertStringContainsString('Be Bright', $all);
        $this->assertStringContainsString('Tel: 0170', $all);
        $this->assertStringContainsString('If found, please return', $all);

        $hidden = $render(['card_show_slogan_front', 'card_show_slogan_back', 'card_show_footer_front', 'card_show_back_notice']);
        $this->assertStringNotContainsString('Be Bright', $hidden);
        $this->assertStringNotContainsString('Tel: 0170', $hidden);
        $this->assertStringNotContainsString('If found, please return', $hidden);
    }
}
