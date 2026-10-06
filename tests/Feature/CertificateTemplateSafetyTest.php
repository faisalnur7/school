<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckPermission;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateTemplateSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_only_certificate_template_cannot_be_deleted(): void
    {
        $user = User::create([
            'name' => 'Certificate Admin',
            'email' => 'certificate-admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $certificate = Certificate::create([
            'name' => 'Testimonial',
            'slug' => 'testimonial',
            'description' => 'Test certificate',
            'is_active' => true,
        ]);

        $template = CertificateTemplate::create([
            'certificate_id' => $certificate->id,
            'name' => 'Default Testimonial',
            'body' => 'Certificate body',
            'is_active' => true,
            'sort_order' => 0,
        ]);
        $certificate->update(['active_template_id' => $template->id]);

        $response = $this->withoutMiddleware(CheckPermission::class)
            ->actingAs($user)
            ->delete(route('certificates.templates.destroy', [$certificate, $template]));

        $response->assertRedirect(route('certificates.edit', $certificate));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('certificate_templates', ['id' => $template->id]);
    }

    public function test_principal_and_school_name_typography_settings_are_saved_separately(): void
    {
        $user = User::create([
            'name' => 'Certificate Admin',
            'email' => 'certificate-layout-admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $certificate = Certificate::create([
            'name' => 'Testimonial',
            'slug' => 'testimonial-layout',
            'description' => 'Test certificate',
            'is_active' => true,
        ]);

        $template = CertificateTemplate::create([
            'certificate_id' => $certificate->id,
            'name' => 'Default Testimonial',
            'body' => 'Certificate body',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $response = $this->withoutMiddleware(CheckPermission::class)
            ->actingAs($user)
            ->post(route('certificates.update', $certificate), [
                'name' => $certificate->name,
                'slug' => $certificate->slug,
                'description' => $certificate->description,
                'is_active' => 1,
                'active_template_id' => $template->id,
                'layout_settings' => [
                    'footer' => [
                        'principal_label' => ['font_size' => 12, 'font_weight' => 700, 'color' => '#111827', 'text_align' => 'center', 'font_family' => 'Arial, Helvetica, sans-serif'],
                        'school_name' => ['font_size' => 22, 'font_weight' => 400, 'color' => '#1d4ed8', 'text_align' => 'center', 'font_family' => 'Georgia, Times New Roman, serif'],
                    ],
                ],
            ]);

        $response->assertRedirect(route('certificates.edit', $certificate));
        $layout = $certificate->refresh()->layoutSettings();

        $this->assertSame(12, $layout['footer']['principal_label']['font_size']);
        $this->assertSame(22, $layout['footer']['school_name']['font_size']);
        $this->assertSame('#1d4ed8', $layout['footer']['school_name']['color']);
        $this->assertSame('Georgia, Times New Roman, serif', $layout['footer']['school_name']['font_family']);
    }

    public function test_saving_the_certificate_template_updates_one_canonical_template(): void
    {
        $user = User::create([
            'name' => 'Certificate Admin',
            'email' => 'certificate-single-template@example.com',
            'password' => bcrypt('password'),
        ]);

        $certificate = Certificate::create([
            'name' => 'Transfer Certificate',
            'slug' => 'transfer-single-template',
            'is_active' => true,
        ]);

        $template = CertificateTemplate::create([
            'certificate_id' => $certificate->id,
            'name' => 'Default Transfer Certificate',
            'body' => 'Old body',
            'is_active' => true,
            'sort_order' => 0,
        ]);
        $certificate->update(['active_template_id' => $template->id]);

        $response = $this->withoutMiddleware(CheckPermission::class)
            ->actingAs($user)
            ->post(route('certificates.update', $certificate), [
                'name' => $certificate->name,
                'slug' => $certificate->slug,
                'description' => null,
                'is_active' => 1,
                'active_template_id' => $template->id,
                'template_name' => 'Transfer Certificate Template',
                'template_body' => 'New body {{ student.full_name_en }}',
            ]);

        $response->assertRedirect(route('certificates.edit', $certificate));
        $this->assertSame(1, $certificate->templates()->count());
        $this->assertDatabaseHas('certificate_templates', [
            'id' => $template->id,
            'name' => 'Transfer Certificate Template',
            'body' => 'New body {{ student.full_name_en }}',
        ]);
    }
}
