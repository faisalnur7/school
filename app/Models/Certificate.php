<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'active_template_id',
        'sort_order',
        'layout_settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'layout_settings' => 'array',
    ];

    public static function defaultLayoutSettings(): array
    {
        return [
            'page' => ['width' => 210, 'height' => 297],
            'margins' => ['top' => 28, 'right' => 16, 'bottom' => 18, 'left' => 16],
            'typography' => [
                'title_size' => 18,
                'title_font_family' => 'Arial, Helvetica, sans-serif',
                'title_color' => '#111827',
                'title_background' => '#eef2e3',
                'title_border_color' => '#4b5563',
                'title_border_width' => 1,
                'title_border_style' => 'solid',
                'title_padding_top' => 9,
                'title_padding_right' => 42,
                'title_padding_bottom' => 9,
                'title_padding_left' => 42,
                'title_font_weight' => 500,
                'title_text_align' => 'center',
                'title_letter_spacing' => 0.01,
                'title_text_transform' => 'uppercase',
                'body_size' => 17,
                'line_height' => 2.05,
                'font_family' => 'Georgia, Times New Roman, serif',
                'font_color' => '#111827',
                'font_weight' => 400,
                'placeholder_font_family' => 'Georgia, Times New Roman, serif',
                'placeholder_font_size' => 17,
                'placeholder_font_weight' => 700,
                'placeholder_color' => '#b45309',
                'text_align' => 'justify',
            ],
            'watermark' => ['opacity' => 0.12, 'size' => 55],
            'visibility' => [
                'title' => true,
                'body' => true,
                'watermark' => true,
                'reason' => true,
                'principal' => true,
                'principal_name' => true,
                'school_name' => true,
                'contact_number' => true,
            ],
            'header' => [
                'logo' => ['width' => 48, 'height' => 48],
                'bangla' => ['font_size' => 29, 'font_weight' => 800, 'color' => '#111827', 'text_align' => 'left'],
                'english' => ['font_size' => 17, 'font_weight' => 800, 'color' => '#111827', 'text_align' => 'left'],
            ],
            'footer' => [
                'reason' => ['font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 15, 'font_weight' => 400, 'color' => '#111827', 'text_align' => 'left'],
                'principal' => ['font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 16, 'font_weight' => 400, 'color' => '#111827', 'text_align' => 'center'],
                'principal_label' => ['font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 16, 'font_weight' => 700, 'color' => '#111827', 'text_align' => 'center'],
                'school_name' => ['font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 16, 'font_weight' => 700, 'color' => '#111827', 'text_align' => 'center'],
                'contact_number' => ['font_family' => 'Arial, Helvetica, sans-serif', 'font_size' => 14, 'font_weight' => 400, 'color' => '#111827', 'text_align' => 'center'],
            ],
            'positions' => [
                'title' => ['x' => 0, 'y' => 0],
                'body' => ['x' => 0, 'y' => 0],
                'bottom' => ['x' => 0, 'y' => 0],
                'watermark' => ['x' => 0, 'y' => 0],
            ],
        ];
    }

    public function layoutSettings(): array
    {
        return array_replace_recursive(static::defaultLayoutSettings(), $this->layout_settings ?? []);
    }

    public static function placeholderGroups(): array
    {
        return [
            'shared' => [
                ['token' => '{{ student.full_name_en }}', 'label' => 'Student name', 'note' => 'Primary name printed on the certificate.'],
                ['token' => '{{ student.full_name_bn }}', 'label' => 'Bangla name', 'note' => 'Optional Bangla name line.'],
                ['token' => '{{ student.student_cid }}', 'label' => 'Student CID', 'note' => 'Student identification number.'],
                ['token' => '{{ student.father_name }}', 'label' => 'Father name', 'note' => 'Father or guardian name.'],
                ['token' => '{{ student.mother_name }}', 'label' => 'Mother name', 'note' => 'Mother name.'],
                ['token' => '{{ student.date_of_birth }}', 'label' => 'Date of birth', 'note' => 'Formatted student birth date.'],
                ['token' => '{{ student.birth_certificate_number }}', 'label' => 'Birth certificate no.', 'note' => 'Birth registration number.'],
                ['token' => '{{ student.present_address }}', 'label' => 'Present address', 'note' => 'Student present address line.'],
                ['token' => '{{ student.present_division.name }}', 'label' => 'Present division', 'note' => 'Present division name.'],
                ['token' => '{{ student.present_district.name }}', 'label' => 'Present district', 'note' => 'Present district name.'],
                ['token' => '{{ student.present_police_station.name }}', 'label' => 'Present upozilla', 'note' => 'Present police station/upozilla name.'],
                ['token' => '{{ student.present_post_office.name }}', 'label' => 'Present post office', 'note' => 'Present post office name.'],
                ['token' => '{{ student.permanent_address }}', 'label' => 'Permanent address', 'note' => 'Student permanent address line.'],
                ['token' => '{{ student.permanent_division.name }}', 'label' => 'Permanent division', 'note' => 'Permanent division name.'],
                ['token' => '{{ student.permanent_district.name }}', 'label' => 'Permanent district', 'note' => 'Permanent district name.'],
                ['token' => '{{ student.permanent_police_station.name }}', 'label' => 'Permanent upozilla', 'note' => 'Permanent police station/upozilla name.'],
                ['token' => '{{ student.permanent_post_office.name }}', 'label' => 'Permanent post office', 'note' => 'Permanent post office name.'],
                ['token' => '{{ academicInfo.schoolClass.name_en }}', 'label' => 'Last class', 'note' => 'Latest class name.'],
                ['token' => '{{ academicInfo.section.name_en }}', 'label' => 'Section', 'note' => 'Latest section name.'],
                ['token' => '{{ academicInfo.academicSession.name_en }}', 'label' => 'Academic session', 'note' => 'Latest academic session.'],
                ['token' => '{{ academicInfo.roll }}', 'label' => 'Roll', 'note' => 'Latest roll number.'],
                ['token' => '{{ academicInfo.checkout_date }}', 'label' => 'Date of leaving', 'note' => 'Leaving date shown on transfer certificate.'],
                ['token' => '{{ academicInfo.academic_status }}', 'label' => 'Reason for leaving', 'note' => 'Transferred, graduated, withdrawn, or expelled.'],
                ['token' => '{{ academicInfo.notes }}', 'label' => 'Leaving reason text', 'note' => 'Free-text reason for leaving or remarks.'],
                ['token' => '{{ student.previous_school }}', 'label' => 'Previous school', 'note' => 'Previous school name.'],
                ['token' => '{{ student.previous_class_appeared }}', 'label' => 'Previous class appeared', 'note' => 'Class student appeared in before admission.'],
                ['token' => '{{ student.tc_number }}', 'label' => 'TC number', 'note' => 'Transfer certificate reference number.'],
                ['token' => '{{ issueYear }}', 'label' => 'Issue year', 'note' => 'Printable year value for the certificate body.'],
                ['token' => '{{ issueDate }}', 'label' => 'Issue date', 'note' => 'Date of printing.'],
                ['token' => '{{ setting.name }}', 'label' => 'School name', 'note' => 'Current school name.'],
                ['token' => '{{ setting.address }}', 'label' => 'School address', 'note' => 'Current school address.'],
            ],
            'transfer_certificate' => [
                ['token' => '{{ academicInfo.checkout_date }}', 'label' => 'Date of leaving', 'note' => 'Leaving date shown on transfer certificate.'],
                ['token' => '{{ academicInfo.academic_status }}', 'label' => 'Reason for leaving', 'note' => 'Transferred, graduated, withdrawn, or expelled.'],
                ['token' => '{{ academicInfo.notes }}', 'label' => 'Leaving notes', 'note' => 'Custom reason or notes for leaving.'],
                ['token' => '{{ academicInfo.schoolClass.name_en }}', 'label' => 'Current class', 'note' => 'Class the student was in when leaving.'],
                ['token' => '{{ student.present_address }}', 'label' => 'Village/address', 'note' => 'Village or local address line.'],
                ['token' => '{{ student.present_post_office.name }}', 'label' => 'Post office', 'note' => 'Post office name.'],
                ['token' => '{{ student.present_police_station.name }}', 'label' => 'Upozilla', 'note' => 'Police station/upozilla name.'],
                ['token' => '{{ student.present_district.name }}', 'label' => 'District', 'note' => 'District name.'],
                ['token' => '{{ student.previous_class_appeared }}', 'label' => 'Class appeared', 'note' => 'Class student appeared in before admission.'],
                ['token' => '{{ student.tc_number }}', 'label' => 'TC number', 'note' => 'Transfer certificate reference number.'],
            ],
            'testimonial' => [
                ['token' => '{{ academicInfo.schoolClass.name_en }}', 'label' => 'Last class attended', 'note' => 'Use inside testimonial narrative if needed.'],
                ['token' => '{{ academicInfo.section.name_en }}', 'label' => 'Section attended', 'note' => 'Use inside testimonial narrative if needed.'],
                ['token' => '{{ academicInfo.checkout_date }}', 'label' => 'Leaving date', 'note' => 'Date when testimonial was issued or student left.'],
                ['token' => '{{ academicInfo.academic_status }}', 'label' => 'Reason/Status', 'note' => 'Graduated, transferred, withdrawn, or expelled.'],
                ['token' => '{{ academicInfo.notes }}', 'label' => 'Notes', 'note' => 'Free-text testimonial note.'],
                ['token' => '{{ student.present_address }}', 'label' => 'Address line', 'note' => 'Address line to mention in testimonial.'],
                ['token' => '{{ student.present_post_office.name }}', 'label' => 'Post office', 'note' => 'Post office name.'],
                ['token' => '{{ student.present_police_station.name }}', 'label' => 'Upozilla', 'note' => 'Police station/upozilla name.'],
                ['token' => '{{ student.present_district.name }}', 'label' => 'District', 'note' => 'District name.'],
            ],
        ];
    }

    public static function defaultTypeDefinitions(?SchoolSetting $setting = null): array
    {
        $setting = $setting ?? SchoolSetting::current();

        return [
            [
                'name' => 'Transfer Certificate',
                'slug' => 'transfer-certificate',
                'description' => 'Issue a transfer certificate for a student leaving the institution.',
                'templates' => [
                    [
                        'name' => 'Default Transfer Certificate',
                        'body' => trim((string) ($setting->transfer_certificate_template ?? '')) !== '' ? $setting->transfer_certificate_template : <<<TEXT
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
                        'body' => trim((string) ($setting->testimonial_template ?? '')) !== '' ? $setting->testimonial_template : <<<TEXT
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

    public static function ensureDefaults(): void
    {
        foreach (static::defaultTypeDefinitions() as $index => $definition) {
            $certificate = static::firstOrCreate(
                ['slug' => $definition['slug']],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'is_active' => true,
                    'sort_order' => $index,
                ]
            );

            if (!$certificate->templates()->exists()) {
                $activeTemplateId = null;
                foreach ($definition['templates'] as $templateIndex => $template) {
                    $created = $certificate->templates()->create([
                        'name' => $template['name'],
                        'body' => $template['body'],
                        'is_active' => true,
                        'sort_order' => $templateIndex,
                    ]);
                    $activeTemplateId = $activeTemplateId ?? $created->id;
                }

                $certificate->update(['active_template_id' => $activeTemplateId]);
            }
        }
    }

    public function templates(): HasMany
    {
        return $this->hasMany(CertificateTemplate::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeTemplate(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'active_template_id');
    }
}
