<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\AdmitSeatCardSetting;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Group;
use App\Models\Student;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Mpdf\Mpdf;

class GenerateIdCardController extends Controller
{
    /** PDF page margins in mm. Keep in sync with Mpdf options in pdf() and the @page rule in _styles. */
    private const MARGIN_SIDE_MM = 8;
    private const MARGIN_TOP_MM = 10;
    private const MARGIN_BOTTOM_MM = 8;
    private const DEFAULT_DETAIL_FONT_SIZE = 4.6;

    public function index(Request $request)
    {
        return $this->renderPage($request, false);
    }

    /** Dedicated settings page (same view as index, rendered with only the settings form). */
    public function settings(Request $request)
    {
        return $this->renderPage($request, true);
    }

    private function renderPage(Request $request, bool $settingsOnly)
    {
        [$sessions, $classes, $sections, $groups, $students, $setting, $cardType, $cardSettingsMap, $cardSettings, $layout, $schoolLogoUrl, $currentCardLogoUrl, $currentCardPrincipalSignatureUrl, $cardSettingsPayload, $allStudents, $readyToLoad] = $this->buildData($request);

        $cardDefaults = AdmitSeatCardSetting::idCardDefaults();

        return view('pages.generate-id-cards.index', compact(
            'sessions', 'classes', 'sections', 'groups', 'students', 'setting', 'cardType', 'cardSettingsMap', 'cardSettings', 'layout', 'schoolLogoUrl', 'currentCardLogoUrl', 'currentCardPrincipalSignatureUrl', 'cardSettingsPayload', 'allStudents', 'readyToLoad', 'cardDefaults', 'settingsOnly'
        ));
    }

    public function pdf(Request $request)
    {
        [, , , , $students, $setting, $cardType, $cardSettingsMap, $cardSettings, $layout] = $this->buildData($request);

        if ($students->isEmpty()) {
            return redirect()->route('students.id-cards')->with('error', 'Select Academic Year, Class and Section (or a Student ID) to export.');
        }

        $html = view('pages.generate-id-cards.pdf', compact('students', 'setting', 'cardType', 'cardSettings', 'layout'))->render();

        $filename = $cardType === 'library_card' ? 'library-cards.pdf' : 'id-cards.pdf';

        $mpdf = new Mpdf([
            'format'                   => $layout['pageFormat'],
            'margin_top'               => self::MARGIN_TOP_MM,
            'margin_bottom'            => self::MARGIN_BOTTOM_MM,
            'margin_left'              => self::MARGIN_SIDE_MM,
            'margin_right'             => self::MARGIN_SIDE_MM,
            'img_dpi'                  => 150,
            'allow_charset_conversion' => false,
        ]);
        $mpdf->showImageErrors = true;
        // Switch to a Bengali-capable font automatically for Bangla names/addresses.
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->WriteHTML($html);
        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            // ?inline=1 opens the PDF in the browser tab (used by the settings page preview).
            'Content-Disposition' => ($request->boolean('inline') ? 'inline' : 'attachment') . '; filename="' . $filename . '"',
        ]);
    }

    private function buildData(Request $request): array
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes  = SchoolClass::get();
        $setting  = SchoolSetting::current();
        $cardType = $this->normalizeCardType($request->input('card_type', 'id_card'));
        $cardTypeId = $this->cardTypeToSettingType($cardType);

        $sections = $request->filled('class_id')
            ? Section::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();
        $groups = Group::orderBy('name_en')->get();

        $studentCid = trim((string) $request->input('student_cid', ''));
        $allStudents = collect();
        $cardSettingsMap = AdmitSeatCardSetting::query()
            ->whereIn('card_type', [3, 4])
            ->get()
            ->keyBy('card_type');
        $cardSettingsMap->put(3, $cardSettingsMap->get(3) ?? AdmitSeatCardSetting::current(3));
        $cardSettingsMap->put(4, $cardSettingsMap->get(4) ?? AdmitSeatCardSetting::current(4));
        $cardSettings = $this->applyCardThemeDefaults($cardSettingsMap->get($cardTypeId));
        $layout = $this->buildLayout($cardSettings);
        $schoolLogoUrl = $this->resolveLogoUrl($setting?->logo ?? null);
        $currentCardLogoUrl = $this->resolveLogoUrl($cardSettings?->card_logo ?? null) ?: $schoolLogoUrl;
        $currentCardPrincipalSignatureUrl = $this->resolveLogoUrl($cardSettings?->card_principal_signature ?? null);
        $cardSettingsPayload = $cardSettingsMap->mapWithKeys(function ($setting) use ($schoolLogoUrl) {
            return [
                (string) $setting->card_type => [
                    'cards_per_page' => $setting->cards_per_page,
                    'cards_per_row' => $setting->cards_per_row,
                    'card_width_value' => $setting->card_width_value,
                    'card_height_value' => $setting->card_height_value,
                    'grid_gap_value' => $setting->grid_gap_value,
                    'card_dimension_unit' => $setting->card_dimension_unit,
                    'card_page_size' => $setting->card_page_size,
                    'card_page_orientation' => $setting->card_page_orientation,
                    'card_front_alignment' => $setting->card_front_alignment,
                    'card_back_alignment' => $setting->card_back_alignment,
                    'card_front_padding_value' => $setting->card_front_padding_value,
                    'card_back_padding_value' => $setting->card_back_padding_value,
                    'card_front_header_padding_value' => $setting->card_front_header_padding_value,
                    'card_back_header_padding_value' => $setting->card_back_header_padding_value,
                    'card_photo_width_value' => $setting->card_photo_width_value,
                    'card_photo_height_value' => $setting->card_photo_height_value,
                    'card_photo_fit' => $setting->card_photo_fit,
                    'card_logo_size_value' => $setting->card_logo_size_value,
                    'card_logo_fit' => $setting->card_logo_fit,
                    'card_school_name_font_size' => $setting->card_school_name_font_size,
                    'card_school_detail_font_size' => $setting->card_school_detail_font_size,
                    'card_slogan_font_size' => $setting->card_slogan_font_size,
                    'card_title_font_size' => $setting->card_title_font_size,
                    'card_name_font_size' => $setting->card_name_font_size,
                    'card_student_detail_alignment' => $setting->card_student_detail_alignment,
                    'card_student_detail_font_size' => $setting->card_student_detail_font_size,
                    'card_element_positions' => $setting->card_element_positions ?? [],
                    'card_student_detail_text_color' => $setting->card_student_detail_text_color,
                    'card_is_transparent' => $setting->card_is_transparent,
                    'card_color_type' => $setting->card_color_type,
                    'card_color_gradient_1' => $setting->card_color_gradient_1,
                    'card_color_gradient_2' => $setting->card_color_gradient_2,
                    'card_solid_color' => $setting->card_solid_color,
                    'card_school_name_text_color' => $setting->card_school_name_text_color,
                    'card_school_detail_text_color' => $setting->card_school_detail_text_color,
                    'card_slogan_text_color' => $setting->card_slogan_text_color,
                    'card_name_text_color' => $setting->card_name_text_color,
                    'card_back_notice_text_color' => $setting->card_back_notice_text_color,
                    'card_footer_text_color' => $setting->card_footer_text_color,
                    'card_title_text_color' => $setting->card_title_text_color,
                    'card_show_school_detail_front' => $setting->card_show_school_detail_front,
                    'card_show_school_detail_back' => $setting->card_show_school_detail_back,
                    'card_show_slogan_front' => $setting->card_show_slogan_front,
                    'card_show_slogan_back' => $setting->card_show_slogan_back,
                    'card_show_title_front' => $setting->card_show_title_front,
                    'card_show_title_back' => $setting->card_show_title_back,
                    'card_show_logo_front' => $setting->card_show_logo_front,
                    'card_show_logo_back' => $setting->card_show_logo_back,
                    'card_show_photo_front' => $setting->card_show_photo_front,
                    'card_show_footer_front' => $setting->card_show_footer_front,
                    'card_show_footer_back' => $setting->card_show_footer_back,
                    'card_show_back_student_details' => $setting->card_show_back_student_details,
                    'card_show_back_school_contact' => $setting->card_show_back_school_contact,
                    'card_show_back_qr' => $setting->card_show_back_qr,
                    'card_show_back_signature' => $setting->card_show_back_signature,
                    'card_show_back_notice' => $setting->card_show_back_notice,
                    'card_logo_url' => $this->resolveLogoUrl($setting->card_logo ?? null) ?: $schoolLogoUrl,
                    'card_principal_signature_url' => $this->resolveLogoUrl($setting->card_principal_signature ?? null),
                ],
            ];
        })->toArray();

        $academicInfoConstraint = function ($query) use ($request) {
            $query->when($request->filled('session_id'), fn ($q) => $q->where('academic_session_id', $request->session_id))
                ->when($request->filled('class_id'), fn ($q) => $q->where('school_class_id', $request->class_id))
                ->when($request->filled('section_id'), fn ($q) => $q->where('section_id', $request->section_id))
                ->when($request->filled('group_id'), fn ($q) => $q->where('group_id', $request->group_id))
                ->with(['schoolClass', 'section', 'group', 'academicSession'])
                ->orderByDesc('academic_session_id')
                ->orderByDesc('id');
        };

        // Academic Year, Class and Section are all required to load a list; a single Student ID is the only shortcut.
        // The settings page can ask for a one-student PDF to check the real output: 'first' means any student.
        $previewStudentId = trim((string) $request->input('preview_student_id', ''));
        $readyToLoad = $studentCid !== '' || $previewStudentId !== ''
            || ($request->filled('session_id') && $request->filled('class_id') && $request->filled('section_id'));

        if ($previewStudentId !== '') {
            $previewQuery = Student::with(['academicInformations' => $academicInfoConstraint]);
            $allStudents = ($previewStudentId === 'first'
                ? $previewQuery->orderBy('id')->limit(1)
                : $previewQuery->whereKey((int) $previewStudentId))->get();
        } elseif ($studentCid !== '') {
            $allStudents = Student::with(['academicInformations' => $academicInfoConstraint])
                ->where('student_cid', $studentCid)
                ->orderBy('full_name_en')
                ->get();
        } elseif ($readyToLoad) {
            $allStudents = Student::with(['academicInformations' => $academicInfoConstraint])
                ->whereHas('academicInformations', function ($query) use ($request) {
                    $query->where('academic_session_id', $request->session_id)
                        ->when($request->filled('class_id'), fn ($q) => $q->where('school_class_id', $request->class_id))
                        ->when($request->filled('section_id'), fn ($q) => $q->where('section_id', $request->section_id))
                        ->when($request->filled('group_id'), fn ($q) => $q->where('group_id', $request->group_id));
                })
                ->orderBy('full_name_en')
                ->get();
        }

        $allStudents = $this->sortByRoll($allStudents);

        $students = $allStudents;

        return [$sessions, $classes, $sections, $groups, $students, $setting, $cardType, $cardSettingsMap, $cardSettings, $layout, $schoolLogoUrl, $currentCardLogoUrl, $currentCardPrincipalSignatureUrl, $cardSettingsPayload, $allStudents, $readyToLoad];
    }

    /** Natural roll order (2 before 10); students without a roll go last, then by name. */
    private function sortByRoll(Collection $students): Collection
    {
        return $students->sort(function ($a, $b) {
            $rollA = trim((string) $a->academicInformations->first()?->roll);
            $rollB = trim((string) $b->academicInformations->first()?->roll);

            if (($rollA === '') !== ($rollB === '')) {
                return $rollA === '' ? 1 : -1;
            }

            return strnatcasecmp($rollA, $rollB) ?: strcasecmp((string) $a->full_name_en, (string) $b->full_name_en);
        })->values();
    }

    private function normalizeCardType(?string $cardType): string
    {
        return in_array($cardType, ['id_card', 'library_card'], true) ? $cardType : 'id_card';
    }

    private function resolveLogoUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return file_exists(public_path($path)) ? asset($path) : null;
    }

    public function saveSettings(Request $request)
    {
        $cardType = $this->normalizeCardType($request->input('card_type', 'id_card'));
        $cardTypeId = $this->cardTypeToSettingType($cardType);

        $validated = $request->validate([
            'card_type' => ['required', 'in:id_card,library_card'],
            'cards_per_page' => ['required', 'integer', 'min:1', 'max:12'],
            'cards_per_row' => ['required', 'integer', 'min:1', 'max:10', 'lte:cards_per_page'],
            'card_width_value' => ['required', 'numeric', 'min:0.1', 'max:2000'],
            'card_height_value' => ['required', 'numeric', 'min:0.1', 'max:2000'],
            'grid_gap_value' => ['required', 'numeric', 'min:0.1', 'max:500'],
            'card_dimension_unit' => ['required', 'in:cm,px'],
            'card_page_size' => ['nullable', 'in:a4,letter'],
            'card_page_orientation' => ['nullable', 'in:landscape,portrait'],
            'card_front_alignment' => ['nullable', 'in:left,center,right'],
            'card_back_alignment' => ['nullable', 'in:left,center,right'],
            'card_front_padding_value' => ['nullable', 'numeric', 'min:0'],
            'card_back_padding_value' => ['nullable', 'numeric', 'min:0'],
            'card_front_header_padding_value' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'card_back_header_padding_value' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'card_photo_width_value' => ['nullable', 'numeric', 'min:0.1'],
            'card_photo_height_value' => ['nullable', 'numeric', 'min:0.1'],
            'card_photo_fit' => ['nullable', 'in:cover,contain'],
            'card_logo_size_value' => ['nullable', 'numeric', 'min:0.1'],
            'card_logo_fit' => ['nullable', 'in:cover,contain'],
            'card_school_name_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_school_detail_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_slogan_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_title_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_name_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_student_detail_alignment' => ['nullable', 'in:left,center,right'],
            'card_student_detail_font_size' => ['nullable', 'numeric', 'min:1', 'max:24'],
            'card_student_detail_text_color' => ['nullable', 'string', 'max:20'],
            'card_element_positions' => ['nullable', 'array'],
            'card_name_text_color' => ['nullable', 'string', 'max:20'],
            'card_exam_type_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_exam_name_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_is_transparent' => ['nullable', 'boolean'],
            'card_color_type' => ['required', 'in:gradient,solid'],
            'card_color_gradient_1' => ['nullable', 'string', 'max:20'],
            'card_color_gradient_2' => ['nullable', 'string', 'max:20'],
            'card_solid_color' => ['nullable', 'string', 'max:20'],
            'card_school_name_text_color' => ['nullable', 'string', 'max:20'],
            'card_school_detail_text_color' => ['nullable', 'string', 'max:20'],
            'card_slogan_text_color' => ['nullable', 'string', 'max:20'],
            'card_back_notice_text_color' => ['nullable', 'string', 'max:20'],
            'card_footer_text_color' => ['nullable', 'string', 'max:20'],
            'card_title_text_color' => ['nullable', 'string', 'max:20'],
            'card_exam_type_text_color' => ['nullable', 'string', 'max:20'],
            'card_exam_name_text_color' => ['nullable', 'string', 'max:20'],
            'card_logo' => ['nullable', 'image', 'max:100'],
            'card_principal_signature' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:100'],
        ]);

        $this->assertCardFitsPage($validated);

        $isTransparent = $request->boolean('card_is_transparent');

        $payload = [
            'card_type' => $cardTypeId,
            'cards_per_page' => $validated['cards_per_page'],
            'cards_per_row' => $validated['cards_per_row'],
            'card_width_value' => $validated['card_width_value'],
            'card_height_value' => $validated['card_height_value'],
            'grid_gap_value' => $validated['grid_gap_value'],
            'card_dimension_unit' => $validated['card_dimension_unit'],
            'card_page_size' => data_get($validated, 'card_page_size', 'a4'),
            'card_page_orientation' => data_get($validated, 'card_page_orientation', 'landscape'),
            'card_front_alignment' => data_get($validated, 'card_front_alignment', 'center'),
            'card_back_alignment' => data_get($validated, 'card_back_alignment', 'center'),
            'card_front_padding_value' => data_get($validated, 'card_front_padding_value', 0.8),
            'card_back_padding_value' => data_get($validated, 'card_back_padding_value', 0.8),
            'card_front_header_padding_value' => ($request->filled('card_front_header_padding_value') ? $validated['card_front_header_padding_value'] : 0.3),
            'card_back_header_padding_value' => ($request->filled('card_back_header_padding_value') ? $validated['card_back_header_padding_value'] : 0.3),
            'card_photo_width_value' => data_get($validated, 'card_photo_width_value', 1.8),
            'card_photo_height_value' => data_get($validated, 'card_photo_height_value', 2.7),
            'card_logo_size_value' => data_get($validated, 'card_logo_size_value', 0.8),
            'card_school_name_font_size' => data_get($validated, 'card_school_name_font_size', 7.2),
            'card_school_detail_font_size' => data_get($validated, 'card_school_detail_font_size', 5.4),
            'card_slogan_font_size' => data_get($validated, 'card_slogan_font_size', 4.8),
            'card_title_font_size' => data_get($validated, 'card_title_font_size', 4.7),
            'card_name_font_size' => data_get($validated, 'card_name_font_size', 7.2),
            'card_student_detail_alignment' => data_get($validated, 'card_student_detail_alignment', 'left'),
            'card_student_detail_font_size' => data_get($validated, 'card_student_detail_font_size') ?: self::DEFAULT_DETAIL_FONT_SIZE,
            'card_student_detail_text_color' => data_get($validated, 'card_student_detail_text_color') ?: '#111827',
            'card_name_text_color' => data_get($validated, 'card_name_text_color') ?: '#1e3a5f',
            'card_element_positions' => AdmitSeatCardSetting::sanitizeIdCardPositions($request->input('card_element_positions')),
            'card_exam_type_font_size' => data_get($validated, 'card_exam_type_font_size', 7.4),
            'card_exam_name_font_size' => data_get($validated, 'card_exam_name_font_size', 6.8),
            'card_is_transparent' => $isTransparent,
            'card_color_type' => $validated['card_color_type'],
            'card_color_gradient_1' => data_get($validated, 'card_color_gradient_1') ?: '#1e3a5f',
            'card_color_gradient_2' => data_get($validated, 'card_color_gradient_2') ?: '#2563eb',
            'card_solid_color' => data_get($validated, 'card_solid_color') ?: '#1e3a5f',
            'card_school_name_text_color' => data_get($validated, 'card_school_name_text_color') ?: ($isTransparent ? '#111827' : '#ffffff'),
            'card_school_detail_text_color' => data_get($validated, 'card_school_detail_text_color') ?: ($isTransparent ? '#334155' : '#e5e7eb'),
            'card_slogan_text_color' => data_get($validated, 'card_slogan_text_color') ?: ($isTransparent ? '#111827' : '#e5e7eb'),
            'card_back_notice_text_color' => data_get($validated, 'card_back_notice_text_color') ?: ($isTransparent ? '#64748b' : '#94a3b8'),
            'card_footer_text_color' => data_get($validated, 'card_footer_text_color') ?: ($isTransparent ? '#111827' : '#e5e7eb'),
            'card_title_text_color' => data_get($validated, 'card_title_text_color') ?: ($isTransparent ? '#111827' : '#ffffff'),
            'card_exam_type_text_color' => data_get($validated, 'card_exam_type_text_color') ?: ($isTransparent ? '#111827' : '#ffffff'),
            'card_exam_name_text_color' => data_get($validated, 'card_exam_name_text_color') ?: ($isTransparent ? '#334155' : '#e5e7eb'),
            'card_photo_fit' => data_get($validated, 'card_photo_fit', 'cover'),
            'card_logo_fit' => data_get($validated, 'card_logo_fit', 'contain'),
            'card_show_logo_front' => $request->boolean('card_show_logo_front'),
            'card_show_logo_back' => $request->boolean('card_show_logo_back'),
            'card_show_photo_front' => $request->boolean('card_show_photo_front'),
            'card_show_footer_front' => $request->boolean('card_show_footer_front'),
            'card_show_footer_back' => $request->boolean('card_show_footer_back'),
            'card_show_back_student_details' => $request->boolean('card_show_back_student_details'),
            'card_show_back_school_contact' => $request->boolean('card_show_back_school_contact'),
            'card_show_back_qr' => $request->boolean('card_show_back_qr'),
            'card_show_back_signature' => $request->boolean('card_show_back_signature'),
            'card_show_school_detail_front' => $request->boolean('card_show_school_detail_front'),
            'card_show_school_detail_back' => $request->boolean('card_show_school_detail_back'),
            'card_show_slogan_front' => $request->boolean('card_show_slogan_front'),
            'card_show_slogan_back' => $request->boolean('card_show_slogan_back'),
            'card_show_title_front' => $request->boolean('card_show_title_front'),
            'card_show_title_back' => $request->boolean('card_show_title_back'),
            'card_show_exam_type_front' => $request->boolean('card_show_exam_type_front'),
            'card_show_exam_name_front' => $request->boolean('card_show_exam_name_front'),
            'card_show_back_notice' => $request->boolean('card_show_back_notice'),
        ];

        if ($request->hasFile('card_logo')) {
            $image = $request->file('card_logo');
            $directory = public_path('uploads/card_settings');

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($directory, $filename);
            $payload['card_logo'] = 'uploads/card_settings/' . $filename;
        }

        if ($request->hasFile('card_principal_signature')) {
            $image = $request->file('card_principal_signature');
            $directory = public_path('uploads/card_settings');

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($directory, $filename);
            $payload['card_principal_signature'] = 'uploads/card_settings/' . $filename;
        }

        $saved = AdmitSeatCardSetting::current($cardTypeId)->fill($payload);
        $saved->save();

        $layout = $this->buildLayout($saved);
        $message = 'Card settings saved.';
        if ($layout['adjusted']) {
            $message .= ' ' . $layout['adjustedMessage'];
        }

        return back()->with('success', $message);
    }

    private function cardTypeToSettingType(string $cardType): int
    {
        return $cardType === 'library_card' ? 4 : 3;
    }

    private function buildLayout(AdmitSeatCardSetting $settings): array
    {
        $requestedPerPage = max(1, min(12, (int) ($settings->cards_per_page ?? 4)));
        $requestedPerRow = max(1, min(10, (int) ($settings->cards_per_row ?? 2)));
        $cardsPerPage = $requestedPerPage;
        $cardsPerRow = min($requestedPerRow, $cardsPerPage);
        $pageRows = (int) ceil($cardsPerPage / $cardsPerRow);

        $area = $this->printableArea($settings->card_page_size ?? null, $settings->card_page_orientation ?? null);
        $pageWidthMm = $area['widthMm'];
        $pageHeightMm = $area['heightMm'];

        $dimensionUnit = strtolower((string) ($settings->card_dimension_unit ?? 'cm'));
        $dimensionUnit = in_array($dimensionUnit, ['cm', 'px'], true) ? $dimensionUnit : 'cm';

        $gapValue = $this->normalizeDimensionValue($settings->grid_gap_value ?? null, $dimensionUnit, 0.5);
        $cardWidthValue = $this->normalizeDimensionValue($settings->card_width_value ?? null, $dimensionUnit, 5.4);
        $cardHeightValue = $this->normalizeDimensionValue($settings->card_height_value ?? null, $dimensionUnit, 8.56);

        $gapMm = $this->dimensionToMm($gapValue, $dimensionUnit);
        $cardWidthMm = $this->dimensionToMm($cardWidthValue, $dimensionUnit);
        $cardHeightMm = $this->dimensionToMm($cardHeightValue, $dimensionUnit);

        // Every student occupies one slot holding a front AND a back card side by side.
        $slotWidthMm = $this->slotWidthMm($cardWidthMm, $gapMm);
        $maxCardsPerRow = max(1, (int) floor(($pageWidthMm + $gapMm) / ($slotWidthMm + $gapMm)));
        $maxPageRows = max(1, (int) floor(($pageHeightMm + $gapMm) / ($cardHeightMm + $gapMm)));

        $cardsPerRow = min($cardsPerRow, $maxCardsPerRow);
        $pageRows = min($pageRows, $maxPageRows);
        $cardsPerPage = min($cardsPerPage, max(1, $cardsPerRow * $pageRows));

        // Surface any clamping so the settings never silently differ from the printed sheet.
        $adjusted = $cardsPerPage !== $requestedPerPage || $cardsPerRow !== $requestedPerRow;
        $adjustedMessage = $adjusted
            ? sprintf(
                'Layout adjusted: %d per page / %d per row was reduced to %d per page / %d per row because the cards do not fit on the %s sheet.',
                $requestedPerPage, $requestedPerRow, $cardsPerPage, $cardsPerRow, $area['label']
            )
            : null;

        return [
            'cardsPerPage' => $cardsPerPage,
            'cardsPerRow' => $cardsPerRow,
            'pageRows' => $pageRows,
            'requestedCardsPerPage' => $requestedPerPage,
            'requestedCardsPerRow' => $requestedPerRow,
            'pageFormat' => $area['mpdf'],
            'pageCss' => $area['css'],
            'adjusted' => $adjusted,
            'adjustedMessage' => $adjustedMessage,
            'cardWidthMm' => round($cardWidthMm, 2),
            'cardHeightMm' => round($cardHeightMm, 2),
            'gridGapMm' => round($gapMm, 2),
            'gridGapValue' => round($gapValue, 2),
            'cardWidthValue' => round($cardWidthValue, 2),
            'cardHeightValue' => round($cardHeightValue, 2),
            'cardDimensionUnit' => $dimensionUnit,
            'cardWidthCm' => round($cardWidthMm / 10, 2),
            'cardHeightCm' => round($cardHeightMm / 10, 2),
            'gridGapCm' => round($gapMm / 10, 2),
            'cardWidthDefaultCm' => round($cardWidthMm / 10, 2),
            'cardHeightDefaultCm' => round($cardHeightMm / 10, 2),
            'gridGapDefaultCm' => round($gapMm / 10, 2),
            'cardWidthDefaultPx' => round($cardWidthMm / 25.4 * 96, 2),
            'cardHeightDefaultPx' => round($cardHeightMm / 25.4 * 96, 2),
            'gridGapDefaultPx' => round($gapMm / 25.4 * 96, 2),
        ];
    }

    /**
     * Reject a single card that cannot physically fit the printable area, instead of
     * silently shrinking the grid to something the user never asked for.
     */
    private function assertCardFitsPage(array $validated): void
    {
        $unit = $validated['card_dimension_unit'];
        $widthMm = $this->dimensionToMm((float) $validated['card_width_value'], $unit);
        $heightMm = $this->dimensionToMm((float) $validated['card_height_value'], $unit);

        $gapMm = $this->dimensionToMm((float) $validated['grid_gap_value'], $unit);
        $area = $this->printableArea($validated['card_page_size'] ?? null, $validated['card_page_orientation'] ?? null);
        $maxWidthMm = ($area['widthMm'] - $gapMm) / 2;

        $errors = [];
        if ($widthMm > $maxWidthMm) {
            $errors['card_width_value'] = sprintf('Front and back cards side by side must fit the sheet: card width can be at most %.1f cm with this gap.', max(0, $maxWidthMm) / 10);
        }
        if ($heightMm > $area['heightMm']) {
            $errors['card_height_value'] = sprintf('Card height is larger than the printable area (max %.1f cm).', $area['heightMm'] / 10);
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }

    /** Printable area (page minus the PDF margins) plus the matching mPDF format and CSS @page size. */
    private function printableArea(?string $size, ?string $orientation): array
    {
        $letter = $size === 'letter';
        $landscape = $orientation !== 'portrait';
        [$width, $height] = $letter ? [215.9, 279.4] : [210.0, 297.0];
        if ($landscape) {
            [$width, $height] = [$height, $width];
        }

        $name = $letter ? 'Letter' : 'A4';

        return [
            'widthMm' => $width - (self::MARGIN_SIDE_MM * 2),
            'heightMm' => $height - self::MARGIN_TOP_MM - self::MARGIN_BOTTOM_MM,
            'mpdf' => $name . ($landscape ? '-L' : ''),
            'css' => $name . ' ' . ($landscape ? 'landscape' : 'portrait'),
            'label' => $name . ' ' . ($landscape ? 'landscape' : 'portrait'),
        ];
    }

    private function slotWidthMm(float $cardWidthMm, float $gapMm): float
    {
        return ($cardWidthMm * 2) + $gapMm;
    }

    private function normalizeDimensionValue(mixed $value, string $unit, float $fallbackCm): float
    {
        $numeric = is_numeric($value) ? (float) $value : null;

        if ($numeric !== null && $numeric > 0) {
            return $numeric;
        }

        return $fallbackCm;
    }

    private function dimensionToMm(float $value, string $unit): float
    {
        return $unit === 'px'
            ? ($value / 96) * 25.4
            : $value * 10;
    }

    private function applyCardThemeDefaults(?AdmitSeatCardSetting $settings): AdmitSeatCardSetting
    {
        $settings ??= new AdmitSeatCardSetting();

        return $settings->fill([
            'card_is_transparent' => $settings->card_is_transparent ?? false,
            'card_color_type' => $settings->card_color_type ?? 'gradient',
            'card_page_size' => $settings->card_page_size ?? 'a4',
            'card_page_orientation' => $settings->card_page_orientation ?? 'landscape',
            'card_front_alignment' => $settings->card_front_alignment ?? 'center',
            'card_back_alignment' => $settings->card_back_alignment ?? 'center',
            'card_front_padding_value' => $settings->card_front_padding_value ?? 0.8,
            'card_back_padding_value' => $settings->card_back_padding_value ?? 0.8,
            'card_front_header_padding_value' => $settings->card_front_header_padding_value ?? 0.3,
            'card_back_header_padding_value' => $settings->card_back_header_padding_value ?? 0.3,
            'card_photo_width_value' => $settings->card_photo_width_value ?? 1.8,
            'card_photo_height_value' => $settings->card_photo_height_value ?? 2.7,
            'card_photo_fit' => $settings->card_photo_fit ?? 'cover',
            'card_logo_size_value' => $settings->card_logo_size_value ?? 0.8,
            'card_logo_fit' => $settings->card_logo_fit ?? 'contain',
            'card_school_name_font_size' => $settings->card_school_name_font_size ?? 7.2,
            'card_school_detail_font_size' => $settings->card_school_detail_font_size ?? 5.4,
            'card_slogan_font_size' => $settings->card_slogan_font_size ?? 4.8,
            'card_title_font_size' => $settings->card_title_font_size ?? 4.7,
            'card_name_font_size' => $settings->card_name_font_size ?? 7.2,
            'card_name_text_color' => $settings->card_name_text_color ?? '#1e3a5f',
            'card_student_detail_font_size' => $settings->card_student_detail_font_size ?? self::DEFAULT_DETAIL_FONT_SIZE,
            'card_student_detail_text_color' => $settings->card_student_detail_text_color ?? '#111827',
            'card_exam_type_font_size' => $settings->card_exam_type_font_size ?? 7.4,
            'card_exam_name_font_size' => $settings->card_exam_name_font_size ?? 6.8,
            'card_color_gradient_1' => $settings->card_color_gradient_1 ?? '#1e3a5f',
            'card_color_gradient_2' => $settings->card_color_gradient_2 ?? '#2563eb',
            'card_solid_color' => $settings->card_solid_color ?? '#1e3a5f',
            'card_school_name_text_color' => $settings->card_school_name_text_color ?? '#ffffff',
            'card_school_detail_text_color' => $settings->card_school_detail_text_color ?? '#e5e7eb',
            'card_slogan_text_color' => $settings->card_slogan_text_color ?? '#e5e7eb',
            'card_back_notice_text_color' => $settings->card_back_notice_text_color ?? '#94a3b8',
            'card_footer_text_color' => $settings->card_footer_text_color ?? '#e5e7eb',
            'card_title_text_color' => $settings->card_title_text_color ?? '#ffffff',
            'card_exam_type_text_color' => $settings->card_exam_type_text_color ?? '#ffffff',
            'card_exam_name_text_color' => $settings->card_exam_name_text_color ?? '#e5e7eb',
            'card_show_logo_front' => $settings->card_show_logo_front ?? true,
            'card_show_logo_back' => $settings->card_show_logo_back ?? true,
            'card_show_photo_front' => $settings->card_show_photo_front ?? true,
            'card_show_footer_front' => $settings->card_show_footer_front ?? true,
            'card_show_footer_back' => $settings->card_show_footer_back ?? true,
            'card_show_back_student_details' => $settings->card_show_back_student_details ?? true,
            'card_show_back_school_contact' => $settings->card_show_back_school_contact ?? true,
            'card_show_back_qr' => $settings->card_show_back_qr ?? true,
            'card_show_back_signature' => $settings->card_show_back_signature ?? true,
            'card_show_school_detail_front' => $settings->card_show_school_detail_front ?? false,
            'card_show_school_detail_back' => $settings->card_show_school_detail_back ?? true,
            'card_show_slogan_front' => $settings->card_show_slogan_front ?? true,
            'card_show_slogan_back' => $settings->card_show_slogan_back ?? true,
            'card_show_title_front' => $settings->card_show_title_front ?? true,
            'card_show_title_back' => $settings->card_show_title_back ?? true,
            'card_show_exam_type_front' => $settings->card_show_exam_type_front ?? true,
            'card_show_exam_name_front' => $settings->card_show_exam_name_front ?? true,
            'card_show_back_notice' => $settings->card_show_back_notice ?? true,
        ]);
    }
}
