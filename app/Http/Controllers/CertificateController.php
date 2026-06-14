<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CertificateController extends Controller
{
    private function placeholderGroups(): array
    {
        return Certificate::placeholderGroups();
    }

    private function defaultTypeDefinitions(): array
    {
        return [
            [
                'name' => 'Transfer Certificate',
                'slug' => 'transfer-certificate',
                'description' => 'Issue a transfer certificate for a student leaving the institution.',
                'templates' => [
                    [
                        'name' => 'Default Transfer Certificate',
                        'body' => <<<TEXT
This is to certify that {{ student.full_name_en }}
son/daughter of {{ student.father_name }} and {{ student.mother_name }},
was a bonafide student of this institution.

His/Her conduct and character during the period of study was good.

He/She is hereby granted this Transfer Certificate to seek admission in another institution.
TEXT,
                    ],
                ],
            ],
            [
                'name' => 'Testimonial',
                'slug' => 'testimonial',
                'description' => 'Issue a testimonial for a student with the active template assigned here.',
                'templates' => [
                    [
                        'name' => 'Default Testimonial',
                        'body' => <<<TEXT
This is to certify that {{ student.full_name_en }}
son/daughter of {{ student.father_name }} and {{ student.mother_name }},
was a student of this institution.

During his/her stay at this institution, his/her conduct and behaviour were found to be satisfactory
and he/she was regular in attendance.

We wish him/her every success in life and recommend him/her for any purpose for which this testimonial may be required.
TEXT,
                    ],
                ],
            ],
        ];
    }

    private function schoolFallbackDefaults(): array
    {
        return [
            'transfer-certificate' => $this->defaultTypeDefinitions()[0]['templates'][0]['body'],
            'testimonial' => $this->defaultTypeDefinitions()[1]['templates'][0]['body'],
        ];
    }

    private function seedDefaultsIfNeeded(): void
    {
        Certificate::ensureDefaults();
    }

    public function index()
    {
        $this->seedDefaultsIfNeeded();

        $certificates = Certificate::with(['templates', 'activeTemplate'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('pages.certificates.index', [
            'certificates' => $certificates,
            'placeholders' => $this->placeholderGroups(),
        ]);
    }

    public function create()
    {
        return view('pages.certificates.create', [
            'placeholders' => $this->placeholderGroups(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|alpha_dash|unique:certificates,slug',
            'description' => 'nullable|string|max:1000',
            'template_name' => 'required|string|max:255',
            'template_body' => 'required|string|max:10000',
        ]);

        $certificate = Certificate::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_active' => true,
            'sort_order' => (int) Certificate::max('sort_order') + 1,
        ]);

        $template = $certificate->templates()->create([
            'name' => $validated['template_name'],
            'body' => $validated['template_body'],
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $certificate->update(['active_template_id' => $template->id]);

        return redirect()->route('certificates.edit', $certificate)->with('success', 'Certificate type created successfully.');
    }

    public function edit(Certificate $certificate)
    {
        $certificate->load(['templates', 'activeTemplate']);

        return view('pages.certificates.edit', [
            'certificate' => $certificate,
            'placeholders' => $this->placeholderGroups(),
        ]);
    }

    public function update(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|alpha_dash|unique:certificates,slug,' . $certificate->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'active_template_id' => [
                'nullable',
                Rule::exists('certificate_templates', 'id')->where(fn ($query) => $query->where('certificate_id', $certificate->id)),
            ],
        ]);

        $certificate->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'active_template_id' => $validated['active_template_id'] ?? $certificate->active_template_id,
        ]);

        return redirect()->route('certificates.edit', $certificate)->with('success', 'Certificate type updated.');
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();

        return redirect()->route('certificates.index')->with('success', 'Certificate type deleted.');
    }

    public function templateStore(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
            'is_active' => 'nullable|boolean',
        ]);

        $template = $certificate->templates()->create([
            'name' => $validated['name'],
            'body' => $validated['body'],
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) ($certificate->templates()->max('sort_order') ?? -1) + 1,
        ]);

        if ($request->boolean('is_active') || !$certificate->active_template_id) {
            $certificate->update(['active_template_id' => $template->id]);
        }

        return redirect()->route('certificates.edit', $certificate)->with('success', 'Certificate template added.');
    }

    public function templateUpdate(Request $request, Certificate $certificate, CertificateTemplate $template)
    {
        abort_unless($template->certificate_id === $certificate->id, 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
            'is_active' => 'nullable|boolean',
        ]);

        $template->update([
            'name' => $validated['name'],
            'body' => $validated['body'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if (!$request->boolean('is_active') && $certificate->active_template_id === $template->id) {
            $replacementId = $certificate->templates()->where('id', '!=', $template->id)->orderBy('sort_order')->value('id');
            $certificate->update(['active_template_id' => $replacementId]);
        }

        return redirect()->route('certificates.edit', $certificate)->with('success', 'Certificate template updated.');
    }

    public function templateDestroy(Certificate $certificate, CertificateTemplate $template)
    {
        abort_unless($template->certificate_id === $certificate->id, 404);

        $templateId = $template->id;
        $template->delete();

        if ($certificate->active_template_id === $templateId) {
            $replacementId = $certificate->templates()->orderBy('sort_order')->value('id');
            $certificate->update(['active_template_id' => $replacementId]);
        }

        return redirect()->route('certificates.edit', $certificate)->with('success', 'Certificate template deleted.');
    }

    public function templateActivate(Certificate $certificate, CertificateTemplate $template)
    {
        abort_unless($template->certificate_id === $certificate->id, 404);

        $certificate->update(['active_template_id' => $template->id]);

        return redirect()->route('certificates.edit', $certificate)->with('success', 'Active template updated.');
    }

    public function createOrEditFallback()
    {
        abort(404);
    }
}
