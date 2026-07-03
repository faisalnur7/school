<?php

namespace Tests\Feature;

use App\Http\Controllers\AdmitSeatCardController;
use App\Models\AcademicSession;
use App\Models\AdmitSeatCardSetting;
use App\Models\Group;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class AdmitSeatCardSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_logo_is_used_when_no_card_logo_is_saved(): void
    {
        $session = AcademicSession::create(['name_bn' => 'S', 'name_en' => 'S', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'C', 'name_en' => 'C', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);
        Group::create(['name_en' => 'G', 'name_bn' => 'G', 'status' => 1]);
        $user = User::factory()->create();

        $schoolLogoPath = 'uploads/school_settings/test-school-logo.png';
        $schoolLogoFullPath = public_path($schoolLogoPath);
        if (!is_dir(dirname($schoolLogoFullPath))) {
            mkdir(dirname($schoolLogoFullPath), 0755, true);
        }
        file_put_contents($schoolLogoFullPath, 'school-logo');

        SchoolSetting::current()->fill([
            'name' => 'Test School',
            'short_name' => 'TS',
            'address' => 'Test Address',
            'logo' => $schoolLogoPath,
        ])->save();

        $student = Student::create([
            'full_name_en' => 'Charlie',
            'student_cid' => 'A-1001',
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

        $controller = app(AdmitSeatCardController::class);
        $this->actingAs($user);

        $saveRequest = Request::create('/results/admit-seat-cards/settings', 'POST', [
            'card_type' => 'admit_card',
            'cards_per_page' => 8,
            'cards_per_row' => 2,
            'card_width_value' => 9.4,
            'card_height_value' => 6.6,
            'grid_gap_value' => 0.85,
            'card_dimension_unit' => 'cm',
            'card_color_type' => 'gradient',
            'card_color_gradient_1' => '#1e3a5f',
            'card_color_gradient_2' => '#2563eb',
            'card_solid_color' => '#1e3a5f',
        ]);

        $controller->saveSettings($saveRequest);

        $setting = AdmitSeatCardSetting::query()->where('card_type', 1)->firstOrFail();
        $this->assertNull($setting->card_logo);

        $viewRequest = Request::create('/results/admit-seat-cards', 'GET', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'group_id' => '',
            'card_type' => 'admit_card',
            'exam_type' => '',
            'exam_id' => '',
            'student_cid' => '',
        ]);

        app()->instance('request', $viewRequest);
        app('view')->share('errors', new ViewErrorBag());
        $response = $controller->index($viewRequest);
        $html = $response->render();

        $this->assertStringContainsString('Card Type', $html);
        $this->assertStringContainsString('ADMIT CARD', $html);
        $this->assertStringContainsString($schoolLogoPath, $html);
        $this->assertStringContainsString('name="card_logo"', $html);
        $this->assertStringContainsString('name="card_color_type"', $html);
    }

    public function test_transparent_admit_cards_use_custom_text_colors(): void
    {
        $session = AcademicSession::create(['name_bn' => 'S', 'name_en' => 'S', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'C', 'name_en' => 'C', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);
        Group::create(['name_en' => 'G', 'name_bn' => 'G', 'status' => 1]);
        $user = User::factory()->create();

        $student = Student::create([
            'full_name_en' => 'Eva',
            'student_cid' => 'A-1002',
        ]);

        SchoolSetting::current()->fill([
            'name' => 'Test School',
            'address' => 'CIP Tower, Hazaridighi, Dhaka',
        ])->save();

        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '4',
            'is_current' => true,
            'academic_status' => 'active',
        ]);

        $controller = app(AdmitSeatCardController::class);
        $this->actingAs($user);

        $saveRequest = Request::create('/results/admit-seat-cards/settings', 'POST', [
            'card_type' => 'admit_card',
            'cards_per_page' => 8,
            'cards_per_row' => 2,
            'card_width_value' => 9.4,
            'card_height_value' => 6.6,
            'grid_gap_value' => 0.85,
            'card_dimension_unit' => 'cm',
            'card_is_transparent' => 1,
            'card_color_type' => 'gradient',
            'card_color_gradient_1' => '#1e3a5f',
            'card_color_gradient_2' => '#2563eb',
            'card_solid_color' => '#1e3a5f',
            'card_school_name_text_color' => '#111111',
            'card_school_detail_text_color' => '#334155',
            'card_title_text_color' => '#111111',
            'card_exam_type_text_color' => '#0f172a',
            'card_exam_name_text_color' => '#334155',
            'card_student_detail_alignment' => 'center',
            'card_student_detail_font_size' => 9.1,
            'card_student_detail_text_color' => '#0f172a',
            'card_logo_size_value' => 0.8,
        ]);

        $controller->saveSettings($saveRequest);

        $setting = AdmitSeatCardSetting::query()->where('card_type', 1)->firstOrFail();
        $this->assertTrue((bool) $setting->card_is_transparent);
        $this->assertSame('#111111', $setting->card_school_name_text_color);
        $this->assertSame('#0f172a', $setting->card_exam_type_text_color);
        $this->assertSame('center', $setting->card_student_detail_alignment);
        $this->assertSame(9.1, (float) $setting->card_student_detail_font_size);
        $this->assertSame('#0f172a', $setting->card_student_detail_text_color);
        $this->assertSame(0.8, (float) $setting->card_logo_size_value);

        $viewRequest = Request::create('/results/admit-seat-cards', 'GET', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'group_id' => '',
            'card_type' => 'admit_card',
            'exam_type' => '',
            'exam_id' => '',
            'student_cid' => '',
        ]);

        app()->instance('request', $viewRequest);
        app('view')->share('errors', new ViewErrorBag());
        $response = $controller->index($viewRequest);
        $html = $response->render();

        $this->assertStringContainsString('--admit-card-theme-bg: transparent', $html);
        $this->assertStringContainsString('--admit-card-school-name-color: #111111', $html);
        $this->assertStringContainsString('--admit-card-exam-type-color: #0f172a', $html);
        $this->assertStringContainsString('--admit-card-student-detail-align: center', $html);
        $this->assertStringContainsString('--admit-card-student-detail-font-size: 9.1pt', $html);
        $this->assertStringContainsString('--admit-card-student-detail-color: #0f172a', $html);
        $this->assertStringContainsString('CIP Tower, Hazaridighi, Dhaka', $html);
        $this->assertStringContainsString('Principal', $html);
        $this->assertStringContainsString('name="card_is_transparent"', $html);
    }

    public function test_admit_card_layout_shows_effective_page_capacity_when_requested_count_does_not_fit(): void
    {
        $session = AcademicSession::create(['name_bn' => 'S', 'name_en' => 'S', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'C', 'name_en' => 'C', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);
        Group::create(['name_en' => 'G', 'name_bn' => 'G', 'status' => 1]);
        $user = User::factory()->create();

        SchoolSetting::current()->fill([
            'name' => 'Test School',
            'address' => 'Test Address',
        ])->save();

        $student = Student::create([
            'full_name_en' => 'Eva',
            'student_cid' => 'A-1003',
        ]);

        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '5',
            'is_current' => true,
            'academic_status' => 'active',
        ]);

        $controller = app(AdmitSeatCardController::class);
        $this->actingAs($user);

        $saveRequest = Request::create('/results/admit-seat-cards/settings', 'POST', [
            'card_type' => 'admit_card',
            'cards_per_page' => 8,
            'cards_per_row' => 2,
            'card_width_value' => 9.4,
            'card_height_value' => 6.6,
            'grid_gap_value' => 0.85,
            'card_dimension_unit' => 'cm',
            'card_color_type' => 'gradient',
            'card_color_gradient_1' => '#1e3a5f',
            'card_color_gradient_2' => '#2563eb',
            'card_solid_color' => '#1e3a5f',
        ]);

        $controller->saveSettings($saveRequest);

        $viewRequest = Request::create('/results/admit-seat-cards', 'GET', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'group_id' => '',
            'card_type' => 'admit_card',
            'exam_type' => '',
            'exam_id' => '',
            'student_cid' => '',
        ]);

        app()->instance('request', $viewRequest);
        app('view')->share('errors', new ViewErrorBag());
        $response = $controller->index($viewRequest);
        $html = $response->render();

        $this->assertStringContainsString('6 cards/page', $html);
        $this->assertStringContainsString('Requested 8 cards/page, but only 6 fit on A4', $html);
        $this->assertStringContainsString('Only 6 cards fit on A4 with the current layout.', $html);
    }
}
