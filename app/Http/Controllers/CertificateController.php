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

    private function normalizeLayoutSettings(array $input, ?array $existing = null): array
    {
        $layout = array_replace_recursive(Certificate::defaultLayoutSettings(), $existing ?? [], $input);
        $number = static fn ($value, float $minimum, float $maximum, float $fallback): float => min($maximum, max($minimum, is_numeric($value) ? (float) $value : $fallback));

        return [
            'page' => [
                'width' => $number(data_get($layout, 'page.width'), 50, 1000, 210),
                'height' => $number(data_get($layout, 'page.height'), 50, 1400, 297),
            ],
            'margins' => collect(['top', 'right', 'bottom', 'left'])->mapWithKeys(fn (string $side) => [
                $side => $number(data_get($layout, "margins.{$side}"), 0, 100, data_get(Certificate::defaultLayoutSettings(), "margins.{$side}")),
            ])->all(),
            'typography' => [
                'title_size' => $number(data_get($layout, 'typography.title_size'), 8, 72, 18),
                'title_font_family' => $this->normalizeFontFamily(data_get($layout, 'typography.title_font_family'), 'Arial, Helvetica, sans-serif'),
                'title_color' => $this->normalizeHexColor(data_get($layout, 'typography.title_color'), '#111827'),
                'title_background' => $this->normalizeHexColor(data_get($layout, 'typography.title_background'), '#eef2e3'),
                'title_border_color' => $this->normalizeHexColor(data_get($layout, 'typography.title_border_color'), '#4b5563'),
                'title_border_width' => $number(data_get($layout, 'typography.title_border_width'), 0, 10, 1),
                'title_border_style' => in_array(data_get($layout, 'typography.title_border_style'), ['none', 'solid', 'dashed', 'dotted', 'double'], true) ? data_get($layout, 'typography.title_border_style') : 'solid',
                'title_padding_top' => $number(data_get($layout, 'typography.title_padding_top'), 0, 100, 9),
                'title_padding_right' => $number(data_get($layout, 'typography.title_padding_right'), 0, 100, 42),
                'title_padding_bottom' => $number(data_get($layout, 'typography.title_padding_bottom'), 0, 100, 9),
                'title_padding_left' => $number(data_get($layout, 'typography.title_padding_left'), 0, 100, 42),
                'title_font_weight' => (int) $number(data_get($layout, 'typography.title_font_weight'), 300, 900, 500),
                'title_text_align' => in_array(data_get($layout, 'typography.title_text_align'), ['left', 'center', 'right'], true) ? data_get($layout, 'typography.title_text_align') : 'center',
                'title_letter_spacing' => $number(data_get($layout, 'typography.title_letter_spacing'), -5, 20, 0.01),
                'title_text_transform' => in_array(data_get($layout, 'typography.title_text_transform'), ['none', 'uppercase', 'lowercase', 'capitalize'], true) ? data_get($layout, 'typography.title_text_transform') : 'uppercase',
                'body_size' => $number(data_get($layout, 'typography.body_size'), 8, 48, 17),
                'line_height' => $number(data_get($layout, 'typography.line_height'), 1, 3, 2.05),
                'font_family' => in_array(data_get($layout, 'typography.font_family'), [
                    'Georgia, Times New Roman, serif',
                    'Arial, Helvetica, sans-serif',
                    'Times New Roman, Times, serif',
                    'Verdana, Geneva, sans-serif',
                    'DejaVu Serif, serif',
                    'DejaVu Sans, sans-serif',
                ], true) ? data_get($layout, 'typography.font_family') : 'Georgia, Times New Roman, serif',
                'font_color' => preg_match('/^#[0-9a-fA-F]{6}$/', (string) data_get($layout, 'typography.font_color'))
                    ? data_get($layout, 'typography.font_color')
                    : '#111827',
                'font_weight' => (int) $number(data_get($layout, 'typography.font_weight'), 300, 900, 400),
                'placeholder_font_family' => in_array(data_get($layout, 'typography.placeholder_font_family'), [
                    'Georgia, Times New Roman, serif',
                    'Arial, Helvetica, sans-serif',
                    'Times New Roman, Times, serif',
                    'Verdana, Geneva, sans-serif',
                    'DejaVu Serif, serif',
                    'DejaVu Sans, sans-serif',
                ], true) ? data_get($layout, 'typography.placeholder_font_family') : 'Georgia, Times New Roman, serif',
                'placeholder_font_size' => $number(data_get($layout, 'typography.placeholder_font_size'), 8, 48, 17),
                'placeholder_font_weight' => (int) $number(data_get($layout, 'typography.placeholder_font_weight'), 300, 900, 700),
                'placeholder_color' => preg_match('/^#[0-9a-fA-F]{6}$/', (string) data_get($layout, 'typography.placeholder_color'))
                    ? data_get($layout, 'typography.placeholder_color')
                    : '#b45309',
                'text_align' => in_array(data_get($layout, 'typography.text_align'), ['left', 'center', 'right', 'justify'], true)
                    ? data_get($layout, 'typography.text_align')
                    : 'justify',
            ],
            'watermark' => [
                'opacity' => $number(data_get($layout, 'watermark.opacity'), 0, 1, 0.12),
                'size' => $number(data_get($layout, 'watermark.size'), 10, 100, 55),
            ],
            'visibility' => collect([
                'title', 'body', 'watermark', 'reason', 'principal',
                'principal_name', 'school_name', 'contact_number',
            ])->mapWithKeys(fn (string $key) => [$key => filter_var(data_get($layout, "visibility.{$key}"), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true])->all(),
            'header' => [
                'logo' => [
                    'width' => $number(data_get($layout, 'header.logo.width'), 16, 200, 48),
                    'height' => $number(data_get($layout, 'header.logo.height'), 16, 200, 48),
                ],
                'bangla' => $this->normalizeFooterTypography(data_get($layout, 'header.bangla'), [
                    'font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 29, 'font_weight' => 800, 'color' => '#111827', 'text_align' => 'left',
                ]),
                'english' => $this->normalizeFooterTypography(data_get($layout, 'header.english'), [
                    'font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 17, 'font_weight' => 800, 'color' => '#111827', 'text_align' => 'left',
                ]),
            ],
            'footer' => [
                'reason' => $this->normalizeFooterTypography(data_get($layout, 'footer.reason'), [
                    'font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 15, 'font_weight' => 400, 'color' => '#111827', 'text_align' => 'left',
                ]),
                'principal' => $this->normalizeFooterTypography(data_get($layout, 'footer.principal'), [
                    'font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 16, 'font_weight' => 400, 'color' => '#111827', 'text_align' => 'center',
                ]),
                'principal_label' => $this->normalizeFooterTypography(data_get($layout, 'footer.principal_label'), [
                    'font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 16, 'font_weight' => 700, 'color' => '#111827', 'text_align' => 'center',
                ]),
                'school_name' => $this->normalizeFooterTypography(data_get($layout, 'footer.school_name'), [
                    'font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 16, 'font_weight' => 700, 'color' => '#111827', 'text_align' => 'center',
                ]),
                'contact_number' => $this->normalizeFooterTypography(data_get($layout, 'footer.contact_number'), [
                    'font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 14, 'font_weight' => 400, 'color' => '#111827', 'text_align' => 'center',
                ]),
            ],
            'positions' => collect(['title', 'body', 'bottom', 'watermark'])->mapWithKeys(fn (string $key) => [
                $key => [
                    'x' => $number(data_get($layout, "positions.{$key}.x"), -100, 100, 0),
                    'y' => $number(data_get($layout, "positions.{$key}.y"), -100, 100, 0),
                ],
            ])->all(),
        ];
    }

    private function normalizeFooterTypography(?array $input, array $defaults): array
    {
        $fontFamilies = [
            'Georgia, Times New Roman, serif', 'Arial, Helvetica, sans-serif',
            'Times New Roman, Times, serif', 'Verdana, Geneva, sans-serif',
            'DejaVu Serif, serif', 'DejaVu Sans, sans-serif',
        ];
        $value = fn (string $key) => data_get($input, $key, $defaults[$key]);

        return [
            'font_family' => in_array($value('font_family'), $fontFamilies, true) ? $value('font_family') : $defaults['font_family'],
            'font_size' => min(48, max(8, is_numeric($value('font_size')) ? (float) $value('font_size') : $defaults['font_size'])),
            'font_weight' => min(900, max(300, (int) $value('font_weight'))),
            'color' => preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value('color')) ? $value('color') : $defaults['color'],
            'text_align' => in_array($value('text_align'), ['left', 'center', 'right', 'justify'], true) ? $value('text_align') : $defaults['text_align'],
        ];
    }

    private function normalizeFontFamily(?string $value, string $fallback): string
    {
        return in_array($value, [
            'Georgia, Times New Roman, serif', 'Arial, Helvetica, sans-serif',
            'Times New Roman, Times, serif', 'Verdana, Geneva, sans-serif',
            'DejaVu Serif, serif', 'DejaVu Sans, sans-serif',
        ], true) ? $value : $fallback;
    }

    private function normalizeHexColor(?string $value, string $fallback): string
    {
        return preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value) ? $value : $fallback;
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
        $template = $certificate->activeTemplate ?: $certificate->templates->sortBy('sort_order')->first();

        return view('pages.certificates.edit', [
            'certificate' => $certificate,
            'template' => $template,
            'placeholders' => $this->placeholderGroups(),
            'setting' => SchoolSetting::current(),
        ]);
    }

    public function update(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|alpha_dash|unique:certificates,slug,' . $certificate->id,
            'description' => 'nullable|string|max:1000',
            'template_name' => 'nullable|string|max:255',
            'template_body' => 'nullable|string|max:10000',
            'is_active' => 'nullable|boolean',
            'active_template_id' => [
                'nullable',
                Rule::exists('certificate_templates', 'id')->where(fn ($query) => $query->where('certificate_id', $certificate->id)),
            ],
            'layout_settings' => ['nullable', 'array'],
            'layout_settings.page' => ['nullable', 'array'],
            'layout_settings.margins' => ['nullable', 'array'],
            'layout_settings.typography' => ['nullable', 'array'],
            'layout_settings.watermark' => ['nullable', 'array'],
            'layout_settings.visibility' => ['nullable', 'array'],
            'layout_settings.header' => ['nullable', 'array'],
            'layout_settings.header.logo' => ['nullable', 'array'],
            'layout_settings.header.bangla' => ['nullable', 'array'],
            'layout_settings.header.english' => ['nullable', 'array'],
            'layout_settings.footer' => ['nullable', 'array'],
            'layout_settings.footer.reason' => ['nullable', 'array'],
            'layout_settings.footer.principal' => ['nullable', 'array'],
            'layout_settings.footer.principal_label' => ['nullable', 'array'],
            'layout_settings.footer.school_name' => ['nullable', 'array'],
            'layout_settings.footer.contact_number' => ['nullable', 'array'],
            'layout_settings.positions' => ['nullable', 'array'],
        ]);

        $certificate->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'active_template_id' => $validated['active_template_id'] ?? $certificate->active_template_id,
            'layout_settings' => $this->normalizeLayoutSettings($validated['layout_settings'] ?? [], $certificate->layout_settings),
        ]);

        if ($request->filled('template_name') || $request->has('template_body')) {
            $template = $certificate->activeTemplate ?: $certificate->templates()->orderBy('sort_order')->first();
            $templateData = [
                'name' => $validated['template_name'] ?? $template?->name ?? $certificate->name,
                'body' => $validated['template_body'] ?? $template?->body ?? '',
                'is_active' => true,
                'sort_order' => 0,
            ];
            $template = $template
                ? tap($template)->update($templateData)
                : $certificate->templates()->create($templateData);
            $certificate->update(['active_template_id' => $template->id]);
        }

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

        $template = $certificate->activeTemplate ?: $certificate->templates()->orderBy('sort_order')->first();
        $templateData = [
            'name' => $validated['name'],
            'body' => $validated['body'],
            'is_active' => true,
            'sort_order' => 0,
        ];
        $template = $template ? tap($template)->update($templateData) : $certificate->templates()->create($templateData);

        if (!$certificate->active_template_id) {
            $certificate->update(['active_template_id' => $template->id]);
        }

        return redirect()->route('certificates.edit', $certificate)->with('success', 'Certificate template saved.');
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

        if ($certificate->templates()->count() <= 1) {
            return redirect()->route('certificates.edit', $certificate)
                ->with('error', 'Keep at least one template for this certificate type. Add another template before deleting this one.');
        }

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
