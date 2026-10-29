<?php

namespace Tests\Feature;

use App\Models\MobileAppFeature;
use App\Models\MobileSetting;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\User;
use Database\Seeders\MobileAppFeatureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentMobileConfigurationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => MobileAppFeatureSeeder::class, '--quiet' => true]);
    }

    private function studentUser(): User
    {
        $student = Student::create([
            'student_cid' => 'MOBILE-001',
            'full_name_en' => 'Mobile Student',
            'full_name_bn' => 'Mobile Student',
            'status' => 1,
        ]);
        $session = AcademicSession::create(['name_bn' => '2026', 'name_en' => '2026', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'Class 1', 'name_en' => 'Class 1', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);
        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'is_current' => true,
            'academic_status' => 'active',
            'promotion_status' => 'new_admission',
        ]);

        return User::factory()->create(['student_id' => $student->id, 'is_active' => true]);
    }

    public function test_authenticated_student_receives_ordered_mobile_configuration(): void
    {
        $user = $this->studentUser();
        Sanctum::actingAs($user, ['student']);

        $this->getJson('/api/v1/student/mobile-configuration')
            ->assertOk()
            ->assertJsonPath('is_active', true)
            ->assertJsonPath('features.0.key', 'dashboard')
            ->assertJsonPath('features.0.route', '/dashboard')
            ->assertJsonPath('features.0.is_enabled', true)
            ->assertJsonCount(10, 'features');
    }

    public function test_inactive_app_blocks_feature_data_but_keeps_configuration_and_me_available(): void
    {
        $user = $this->studentUser();
        MobileSetting::current()->update(['is_active' => false, 'maintenance_message' => 'Back soon.']);
        Sanctum::actingAs($user, ['student']);

        $this->getJson('/api/v1/student/mobile-configuration')->assertOk()->assertJsonPath('is_active', false);
        $this->getJson('/api/v1/student/me')->assertOk()->assertJsonPath('user.mobile_app.is_active', false);
        $this->getJson('/api/v1/student/dashboard')
            ->assertStatus(503)
            ->assertJsonPath('code', 'mobile_app_inactive')
            ->assertJsonPath('maintenance_message', 'Back soon.');
    }

    public function test_disabled_feature_returns_stable_not_found_error(): void
    {
        $user = $this->studentUser();
        MobileAppFeature::where('key', 'notices')->update(['is_enabled' => false]);
        Sanctum::actingAs($user, ['student']);

        $this->getJson('/api/v1/student/notices')
            ->assertNotFound()
            ->assertJsonPath('code', 'mobile_feature_disabled');
    }

    public function test_login_payload_includes_mobile_app_summary(): void
    {
        $user = $this->studentUser();
        $user->update(['email' => 'mobile@example.com', 'password' => bcrypt('secret')]);

        $this->postJson('/api/v1/student/login', ['login' => 'mobile@example.com', 'password' => 'secret'])
            ->assertOk()
            ->assertJsonPath('user.mobile_app.is_active', true)
            ->assertJsonPath('user.mobile_app.enabled_features.0', 'dashboard');
    }

    public function test_dashboard_omits_disabled_capability_sections(): void
    {
        $user = $this->studentUser();
        MobileAppFeature::whereIn('key', ['fees', 'notices'])->update(['is_enabled' => false]);
        Sanctum::actingAs($user, ['student']);

        $this->getJson('/api/v1/student/dashboard')
            ->assertOk()
            ->assertJsonMissingPath('summary.fee_due')
            ->assertJsonMissingPath('summary.latest_notices');
    }
}
