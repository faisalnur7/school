<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\Group;
use App\Models\AdmitSeatCardSetting;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Mpdf\Mpdf;

class AdmitSeatCardController extends Controller
{
    private const TYPOGRAPHY_SPACING_FIELDS = [
        'school_name', 'school_detail', 'slogan', 'title', 'name',
        'exam_type', 'exam_name', 'student_detail', 'footer', 'logo',
        'photo', 'signature', 'vertical_label',
    ];

    private const STUDENT_FIELD_ORDER = [
        'student_name', 'student_id', 'father_name', 'mother_name',
        'roll', 'class', 'section', 'session',
    ];

    private const CARD_ELEMENT_POSITION_KEYS = [
        'school_name', 'school_detail', 'slogan', 'title', 'name', 'exam_type', 'exam_name',
        'student_detail', 'footer', 'logo', 'photo', 'signature', 'vertical_label',
    ];

    public function index(Request $request)
    {
        [$sessions, $classes, $sections, $groups, $exams, $students, $setting, $cardType, $cardSettingsMap, $cardSettings, $examType, $selectedExam, $layout] = $this->buildData($request);

        return view('pages.admit-seat-cards.index', compact(
            'sessions', 'classes', 'sections', 'groups', 'exams', 'students', 'setting', 'cardSettings', 'cardSettingsMap', 'cardType', 'examType', 'selectedExam', 'layout'
        ));
    }

    public function settings(Request $request)
    {
        [$sessions, $classes, $sections, $groups, $exams, $students, $setting, $cardType, $cardSettingsMap, $cardSettings, $examType, $selectedExam, $layout] = $this->buildData($request);

        return view('pages.admit-seat-cards.index', compact(
            'sessions', 'classes', 'sections', 'groups', 'exams', 'students', 'setting', 'cardSettings', 'cardSettingsMap', 'cardType', 'examType', 'selectedExam', 'layout'
        ))->with('settingsOnly', true);
    }

    public function pdf(Request $request)
    {
        [, , , , , $students, $setting, $cardType, $cardSettingsMap, $cardSettings, $examType, $selectedExam, $layout] = $this->buildData($request);

        if ($students->isEmpty()) {
            return redirect()->route('results.admit-seat-cards.index')->with('error', 'No data to export.');
        }

        $html = view('pages.admit-seat-cards.pdf', compact('students', 'setting', 'cardType', 'cardSettings', 'examType', 'selectedExam', 'layout'))->render();
        $filename = $cardType === 'seat_card' ? 'seat-cards.pdf' : 'admit-cards.pdf';

        $mpdf = new Mpdf([
            'format'                   => [$layout['pageWidthMm'], $layout['pageHeightMm']],
            'margin_top'               => $layout['marginTopMm'],
            'margin_bottom'            => $layout['marginBottomMm'],
            'margin_left'              => $layout['marginLeftMm'],
            'margin_right'             => $layout['marginRightMm'],
            'img_dpi'                  => 150,
            'allow_charset_conversion' => false,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->WriteHTML($html);
        $mpdf->Output($filename, 'D');
    }

    public function examsByType(Request $request): JsonResponse
    {
        $examType = $request->query('exam_type');
        $sessionId = $request->query('session_id');

        if (!in_array($examType, [Exam::TYPE_TERMINAL, Exam::TYPE_TUTORIAL], true) || !$sessionId) {
            return response()->json(['exams' => []]);
        }

        $exams = Exam::query()
            ->where('academic_session_id', $sessionId)
            ->where('type', $examType)
            ->orderByDesc('id')
            ->get()
            ->map(fn (Exam $exam) => [
                'id' => $exam->id,
                'name' => $exam->name,
                'type' => $exam->type,
                'type_label' => $exam->type_label,
            ])
            ->values();

        return response()->json(['exams' => $exams]);
    }

    private function buildData(Request $request): array
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes = SchoolClass::get();
        $setting = SchoolSetting::current();
        $cardType = $this->normalizeCardType($request->input('card_type', 'admit_card'));
        $cardTypeId = $this->cardTypeToSettingType($cardType);
        $examType = $request->input('exam_type');
        $selectedExam = null;
        $studentCid = trim((string) $request->input('student_cid', ''));
        $cardSettingsMap = AdmitSeatCardSetting::query()
            ->whereIn('card_type', [1, 2])
            ->get()
            ->keyBy('card_type');
        $cardSettingsMap->put(1, $this->applyCardDefaults($cardSettingsMap->get(1)));
        $cardSettingsMap->put(2, $this->applyCardDefaults($cardSettingsMap->get(2)));
        $cardSettings = $cardSettingsMap->get($cardTypeId) ?? $this->applyCardDefaults(AdmitSeatCardSetting::current($cardTypeId));
        $layout = $this->buildLayout($cardSettings);

        $sections = $request->filled('class_id')
            ? Section::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();

        $groups = Group::orderBy('name_en')->get();
        $selectedExam = $request->filled('exam_id')
            ? Exam::find($request->exam_id)
            : null;
        $selectedSessionId = $request->input('session_id');

        if (!$examType && $selectedExam) {
            $examType = $selectedExam->type;
        }

        $exams = collect();
        if ($examType && $selectedSessionId) {
            $exams = Exam::query()
                ->where('type', $examType)
                ->where('academic_session_id', $selectedSessionId)
                ->orderByDesc('id')
                ->get();
        }

        $students = collect();

        $academicInfoConstraint = function ($query) use ($request) {
            $query->when($request->filled('session_id'), fn ($q) => $q->where('academic_session_id', $request->session_id))
                ->when($request->filled('class_id'), fn ($q) => $q->where('school_class_id', $request->class_id))
                ->when($request->filled('section_id'), fn ($q) => $q->where('section_id', $request->section_id))
                ->when($request->filled('group_id'), fn ($q) => $q->where('group_id', $request->group_id))
                ->where('is_current', true)
                ->where('academic_status', 'active')
                ->with(['schoolClass', 'section', 'group', 'academicSession'])
                ->orderByDesc('academic_session_id')
                ->orderByDesc('id');
        };

        if ($studentCid !== '') {
            $students = Student::with(['academicInformations' => $academicInfoConstraint])
                ->where('student_cid', $studentCid)
                ->whereHas('academicInformations', function ($query) use ($request) {
                    $query->when($request->filled('session_id'), fn ($q) => $q->where('academic_session_id', $request->session_id))
                        ->when($request->filled('class_id'), fn ($q) => $q->where('school_class_id', $request->class_id))
                        ->when($request->filled('section_id'), fn ($q) => $q->where('section_id', $request->section_id))
                        ->when($request->filled('group_id'), fn ($q) => $q->where('group_id', $request->group_id))
                        ->where('is_current', true)
                        ->where('academic_status', 'active');
                })
                ->orderBy('full_name_en')
                ->get();
        } elseif ($request->filled('session_id')) {
            $students = Student::with(['academicInformations' => $academicInfoConstraint])
                ->whereHas('academicInformations', function ($query) use ($request) {
                    $query->where('academic_session_id', $request->session_id)
                        ->when($request->filled('class_id'), fn ($q) => $q->where('school_class_id', $request->class_id))
                        ->when($request->filled('section_id'), fn ($q) => $q->where('section_id', $request->section_id))
                        ->when($request->filled('group_id'), fn ($q) => $q->where('group_id', $request->group_id))
                        ->where('is_current', true)
                        ->where('academic_status', 'active');
                })
                ->orderBy('full_name_en')
                ->get();
        }

        return [$sessions, $classes, $sections, $groups, $exams, $students, $setting, $cardType, $cardSettingsMap, $cardSettings, $examType, $selectedExam, $layout];
    }

    public function saveSettings(Request $request)
    {
        $cardType = $this->normalizeCardType($request->input('card_type', 'admit_card'));
        $cardTypeId = $this->cardTypeToSettingType($cardType);

        $validated = $request->validate([
            'card_type' => ['required', 'in:admit_card,seat_card'],
            'cards_per_page' => ['required', 'integer', 'min:1', 'max:12'],
            'cards_per_row' => ['required', 'integer', 'min:1', 'max:10'],
            'card_width_value' => ['required', 'numeric', 'min:0.1'],
            'card_height_value' => ['required', 'numeric', 'min:0.1'],
            'grid_gap_value' => ['required', 'numeric', 'min:0.1'],
            'card_dimension_unit' => ['required', 'in:cm,px'],
            'page_width_mm' => ['nullable', 'numeric', 'min:50', 'max:1000'],
            'page_height_mm' => ['nullable', 'numeric', 'min:50', 'max:1400'],
            'page_margin_top_mm' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'page_margin_right_mm' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'page_margin_bottom_mm' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'page_margin_left_mm' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'card_front_alignment' => ['nullable', 'in:left,center,right'],
            'card_back_alignment' => ['nullable', 'in:left,center,right'],
            'card_front_padding_value' => ['nullable', 'numeric', 'min:0'],
            'card_back_padding_value' => ['nullable', 'numeric', 'min:0'],
            'card_photo_width_value' => ['nullable', 'numeric', 'min:0.1'],
            'card_photo_height_value' => ['nullable', 'numeric', 'min:0.1'],
            'card_photo_fit' => ['nullable', 'in:cover,contain'],
            'card_logo_size_value' => ['nullable', 'numeric', 'min:0.1'],
            'card_signature_size_value' => ['nullable', 'numeric', 'min:0.1'],
            'card_school_name_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_school_detail_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_slogan_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_title_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_vertical_label_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_name_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_name_text_color' => ['nullable', 'string', 'max:20'],
            'card_exam_type_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_exam_name_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_footer_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_student_detail_alignment' => ['nullable', 'in:left,center,right'],
            'card_student_detail_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_student_detail_text_color' => ['nullable', 'string', 'max:20'],
            'card_text_padding_value' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'card_text_margin_value' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'card_is_transparent' => ['nullable', 'boolean'],
            'card_student_field_order' => ['nullable', 'array'],
            'card_student_field_order.*' => ['nullable', 'integer', 'min:1', 'max:8'],
            'card_element_positions' => ['nullable', 'array'],
            'card_element_sizes' => ['nullable', 'array'],
            'card_element_sizes.exam_name' => ['nullable', 'array'],
            'card_element_sizes.exam_name.width' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'card_element_sizes.exam_name.height' => ['nullable', 'numeric', 'min:0.5', 'max:30'],
            'card_element_sizes.header' => ['nullable', 'array'],
            'card_element_sizes.header.height' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'card_border_colors' => ['nullable', 'array'],
            'card_border_colors.*' => ['nullable', 'string', 'max:20'],
            'card_border_transparent' => ['nullable', 'array'],
            'card_color_type' => ['required', 'in:gradient,solid'],
            'card_color_gradient_1' => ['nullable', 'string', 'max:20'],
            'card_color_gradient_2' => ['nullable', 'string', 'max:20'],
            'card_solid_color' => ['nullable', 'string', 'max:20'],
            'card_school_name_text_color' => ['nullable', 'string', 'max:20'],
            'card_school_detail_text_color' => ['nullable', 'string', 'max:20'],
            'card_slogan_text_color' => ['nullable', 'string', 'max:20'],
            'card_title_text_color' => ['nullable', 'string', 'max:20'],
            'card_vertical_label_text_color' => ['nullable', 'string', 'max:20'],
            'card_exam_type_text_color' => ['nullable', 'string', 'max:20'],
            'card_exam_name_text_color' => ['nullable', 'string', 'max:20'],
            'card_footer_text_color' => ['nullable', 'string', 'max:20'],
            'card_logo' => ['nullable', 'image', 'max:100'],
            'card_principal_signature' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:100'],
        ]);

        $isTransparent = $request->boolean('card_is_transparent');
        $submittedFieldOrder = $request->input('card_student_field_order', []);
        $studentFieldOrder = collect(self::STUDENT_FIELD_ORDER)
            ->sortBy(fn (string $field, int $index) => [
                (int) ($submittedFieldOrder[$field] ?? ($index + 1)),
                $index,
            ])
            ->values()
            ->all();
        $submittedPositions = $request->input('card_element_positions', []);
        $elementPositions = collect(self::CARD_ELEMENT_POSITION_KEYS)->mapWithKeys(function (string $key) use ($submittedPositions): array {
            $position = data_get($submittedPositions, $key, []);

            return [$key => [
                'x' => min(100, max(-100, (float) data_get($position, 'x', 0))),
                'y' => min(100, max(-100, (float) data_get($position, 'y', 0))),
            ]];
        })->all();
        $submittedSizes = $request->input('card_element_sizes', []);
        $existingSetting = AdmitSeatCardSetting::query()->where('card_type', $cardTypeId)->first();
        $elementSizes = [
            'exam_name' => [
                'width' => min(100, max(1, (float) data_get($submittedSizes, 'exam_name.width', data_get($existingSetting?->card_element_sizes, 'exam_name.width', 1)))),
                'height' => min(30, max(0.5, (float) data_get($submittedSizes, 'exam_name.height', data_get($existingSetting?->card_element_sizes, 'exam_name.height', 0.5)))),
            ],
            'header' => [
                'height' => filled(data_get($submittedSizes, 'header.height'))
                    ? min(100, max(1, (float) data_get($submittedSizes, 'header.height')))
                    : data_get($existingSetting?->card_element_sizes, 'header.height'),
            ],
        ];
        $borderColorDefaults = [
            'school_name' => '#ffffff',
            'school_detail' => '#ffffff',
            'slogan' => '#ffffff',
            'title' => '#ffffff',
            'exam_type' => '#ffffff',
            'exam_name' => '#fff200',
            'vertical_label' => '#16a085',
        ];
        $submittedBorderColors = $request->input('card_border_colors', []);
        $borderColors = collect($borderColorDefaults)->mapWithKeys(function (string $default, string $key) use ($submittedBorderColors, $existingSetting): array {
            return [$key => data_get($submittedBorderColors, $key)
                ?: data_get($existingSetting?->card_border_colors, $key, $default)];
        })->all();
        $borderTransparent = collect($borderColorDefaults)->mapWithKeys(function (string $default, string $key) use ($request): array {
            return [$key => $request->boolean("card_border_transparent.$key")];
        })->all();
        $submittedSpacing = $request->input('card_typography_spacing', []);
        $spacingSides = ['top', 'right', 'bottom', 'left'];
        $textSpacing = collect(self::TYPOGRAPHY_SPACING_FIELDS)->mapWithKeys(function (string $field) use ($submittedSpacing, $request, $spacingSides) {
            $fallback = [
                'padding' => (float) $request->input('card_text_padding_value', 0),
                'margin' => (float) $request->input('card_text_margin_value', 0),
            ];

            $normalizeBox = static function ($value, float $default, string $spacingType) use ($spacingSides): array {
                if (!is_array($value)) {
                    $value = array_fill_keys($spacingSides, $value ?? $default);
                }

                $minimum = $spacingType === 'margin' ? -100 : 0;
                $maximum = $spacingType === 'margin' ? 100 : 10;

                return collect($spacingSides)->mapWithKeys(fn (string $side) => [
                    $side => min($maximum, max($minimum, (float) ($value[$side] ?? $default))),
                ])->all();
            };

            return [$field => [
                'padding' => $normalizeBox(data_get($submittedSpacing, "$field.padding"), $fallback['padding'], 'padding'),
                'margin' => $normalizeBox(data_get($submittedSpacing, "$field.margin"), $fallback['margin'], 'margin'),
            ]];
        })->all();

        $payload = [
            'card_type' => $cardTypeId,
            'cards_per_page' => $validated['cards_per_page'],
            'cards_per_row' => $validated['cards_per_row'],
            'card_width_value' => $validated['card_width_value'],
            'card_height_value' => $validated['card_height_value'],
            'grid_gap_value' => $validated['grid_gap_value'],
            'card_dimension_unit' => $validated['card_dimension_unit'],
            'page_width_mm' => data_get($validated, 'page_width_mm', 210),
            'page_height_mm' => data_get($validated, 'page_height_mm', 297),
            'page_margin_top_mm' => data_get($validated, 'page_margin_top_mm', 10),
            'page_margin_right_mm' => data_get($validated, 'page_margin_right_mm', 6.35),
            'page_margin_bottom_mm' => data_get($validated, 'page_margin_bottom_mm', 4),
            'page_margin_left_mm' => data_get($validated, 'page_margin_left_mm', 6.35),
            'card_front_alignment' => data_get($validated, 'card_front_alignment', 'center'),
            'card_back_alignment' => data_get($validated, 'card_back_alignment', 'center'),
            'card_front_padding_value' => data_get($validated, 'card_front_padding_value', 0.8),
            'card_back_padding_value' => data_get($validated, 'card_back_padding_value', 0.8),
            'card_photo_width_value' => data_get($validated, 'card_photo_width_value', 1.8),
            'card_photo_height_value' => data_get($validated, 'card_photo_height_value', 2.7),
            'card_photo_fit' => data_get($validated, 'card_photo_fit', 'cover'),
            'card_logo_size_value' => data_get($validated, 'card_logo_size_value', 0.8),
            'card_signature_size_value' => data_get($validated, 'card_signature_size_value', 1.4),
            'card_school_name_font_size' => data_get($validated, 'card_school_name_font_size', 7.2),
            'card_school_detail_font_size' => data_get($validated, 'card_school_detail_font_size', 5.4),
            'card_slogan_font_size' => data_get($validated, 'card_slogan_font_size', 4.8),
            'card_title_font_size' => data_get($validated, 'card_title_font_size', 4.7),
            'card_vertical_label_font_size' => data_get($validated, 'card_vertical_label_font_size', 5.2),
            'card_name_font_size' => data_get($validated, 'card_name_font_size', 7.2),
            'card_name_text_color' => data_get($validated, 'card_name_text_color') ?: '#111827',
            'card_exam_type_font_size' => data_get($validated, 'card_exam_type_font_size', 7.4),
            'card_exam_name_font_size' => data_get($validated, 'card_exam_name_font_size', 6.8),
            'card_footer_font_size' => data_get($validated, 'card_footer_font_size', 4.5),
            'card_student_detail_alignment' => data_get($validated, 'card_student_detail_alignment', 'left'),
            'card_student_detail_font_size' => data_get($validated, 'card_student_detail_font_size', 8.5),
            'card_student_detail_text_color' => data_get($validated, 'card_student_detail_text_color') ?: ($isTransparent ? '#111827' : '#111827'),
            'card_text_padding_value' => data_get($validated, 'card_text_padding_value', 0),
            'card_text_margin_value' => data_get($validated, 'card_text_margin_value', 0),
            'card_typography_spacing' => $textSpacing,
            'card_student_field_order' => $studentFieldOrder,
            'card_element_positions' => $elementPositions,
            'card_element_sizes' => $elementSizes,
            'card_border_colors' => $borderColors,
            'card_border_transparent' => $borderTransparent,
            'card_is_transparent' => $isTransparent,
            'card_color_type' => $validated['card_color_type'],
            'card_color_gradient_1' => $validated['card_color_gradient_1'] ?: '#1e3a5f',
            'card_color_gradient_2' => $validated['card_color_gradient_2'] ?: '#2563eb',
            'card_solid_color' => $validated['card_solid_color'] ?: '#1e3a5f',
            'card_school_name_text_color' => data_get($validated, 'card_school_name_text_color') ?: ($isTransparent ? '#111827' : '#ffffff'),
            'card_school_detail_text_color' => data_get($validated, 'card_school_detail_text_color') ?: ($isTransparent ? '#334155' : '#e5e7eb'),
            'card_slogan_text_color' => data_get($validated, 'card_slogan_text_color') ?: ($isTransparent ? '#334155' : '#e5e7eb'),
            'card_title_text_color' => data_get($validated, 'card_title_text_color') ?: ($isTransparent ? '#111827' : '#ffffff'),
            'card_vertical_label_text_color' => data_get($validated, 'card_vertical_label_text_color') ?: '#16a085',
            'card_exam_type_text_color' => data_get($validated, 'card_exam_type_text_color') ?: ($isTransparent ? '#111827' : '#ffffff'),
            'card_exam_name_text_color' => data_get($validated, 'card_exam_name_text_color') ?: ($isTransparent ? '#334155' : '#e5e7eb'),
            'card_footer_text_color' => data_get($validated, 'card_footer_text_color') ?: ($isTransparent ? '#334155' : '#e5e7eb'),
            'card_show_logo_front' => $request->boolean('card_show_logo_front'),
            'card_show_logo_back' => $request->boolean('card_show_logo_back'),
            'card_show_photo_front' => $request->boolean('card_show_photo_front'),
            'card_show_father_name_front' => $request->boolean('card_show_father_name_front'),
            'card_show_mother_name_front' => $request->boolean('card_show_mother_name_front'),
            'card_show_student_name_label_front' => $request->boolean('card_show_student_name_label_front'),
            'card_show_roll_front' => $request->boolean('card_show_roll_front'),
            'card_show_class_front' => $request->boolean('card_show_class_front'),
            'card_show_section_front' => $request->boolean('card_show_section_front'),
            'card_show_session_front' => $request->boolean('card_show_session_front'),
            'card_show_vertical_label_front' => $request->boolean('card_show_vertical_label_front'),
            'card_exam_name_badge_front' => $request->boolean('card_exam_name_badge_front'),
            'card_show_footer_front' => $request->boolean('card_show_footer_front'),
            'card_show_footer_back' => $request->boolean('card_show_footer_back'),
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

        AdmitSeatCardSetting::current($cardTypeId)->fill($payload)->save();

        return back()->with('success', 'Card settings saved.');
    }

    private function normalizeCardType(?string $cardType): string
    {
        return in_array($cardType, ['admit_card', 'seat_card'], true) ? $cardType : 'admit_card';
    }

    private function cardTypeToSettingType(string $cardType): int
    {
        return $cardType === 'seat_card' ? 2 : 1;
    }

    private function buildLayout(AdmitSeatCardSetting $settings): array
    {
        $requestedCardsPerPage = max(1, min(12, (int) ($settings->cards_per_page ?? 8)));
        $requestedCardsPerRow = max(1, min(10, (int) ($settings->cards_per_row ?? 2)));
        $requestedCardsPerRow = min($requestedCardsPerRow, $requestedCardsPerPage);
        $requestedPageRows = (int) ceil($requestedCardsPerPage / $requestedCardsPerRow);

        $pageDocumentWidthMm = max(50, (float) ($settings->page_width_mm ?? 210));
        $pageDocumentHeightMm = max(50, (float) ($settings->page_height_mm ?? 297));
        $marginLeftMm = max(0, (float) ($settings->page_margin_left_mm ?? 6.35));
        $marginRightMm = max(0, (float) ($settings->page_margin_right_mm ?? 6.35));
        $marginTopMm = max(0, (float) ($settings->page_margin_top_mm ?? 10));
        $marginBottomMm = max(0, (float) ($settings->page_margin_bottom_mm ?? 4));
        $pageWidthMm = max(1, $pageDocumentWidthMm - ($marginLeftMm + $marginRightMm));
        $pageHeightMm = max(1, $pageDocumentHeightMm - ($marginTopMm + $marginBottomMm));

        $dimensionUnit = strtolower((string) ($settings->card_dimension_unit ?? 'cm'));
        $dimensionUnit = in_array($dimensionUnit, ['cm', 'px'], true) ? $dimensionUnit : 'cm';

        $gapValue = $this->normalizeDimensionValue($settings->grid_gap_value ?? null, $dimensionUnit, 8.5);
        $gapMm = $this->dimensionToMm($gapValue, $dimensionUnit);

        $widthValue = $this->normalizeDimensionValue($settings->card_width_value ?? null, $dimensionUnit, 9.4);
        $heightValue = $this->normalizeDimensionValue($settings->card_height_value ?? null, $dimensionUnit, 6.6);

        $cardWidthMm = $this->dimensionToMm($widthValue, $dimensionUnit);
        $cardHeightMm = $this->dimensionToMm($heightValue, $dimensionUnit);

        $maxCardsPerRow = max(1, (int) floor(($pageWidthMm + $gapMm) / ($cardWidthMm + $gapMm)));
        $maxPageRows = max(1, (int) floor(($pageHeightMm + $gapMm) / ($cardHeightMm + $gapMm)));
        $maxCardsPerPage = max(1, $maxCardsPerRow * $maxPageRows);

        $recommendedCardHeightMm = max(0, ($pageHeightMm - (max(0, $requestedPageRows - 1) * $gapMm)) / $requestedPageRows);
        $recommendedCardHeightValue = $dimensionUnit === 'px'
            ? ($recommendedCardHeightMm / 25.4) * 96
            : $recommendedCardHeightMm / 10;

        $typographySpacing = is_array($settings->card_typography_spacing ?? null)
            ? $settings->card_typography_spacing
            : [];
        $spacingVertical = static function (string $key, string $type) use ($typographySpacing): float {
            $value = data_get($typographySpacing, "$key.$type", 0);

            if (is_array($value)) {
                return max(0, (float) ($value['top'] ?? 0)) + max(0, (float) ($value['bottom'] ?? 0));
            }

            return max(0, (float) $value) * 2;
        };
        $ptToMm = static fn ($value): float => max(0, (float) $value) * 0.352778;
        $isVisible = static fn (string $key, bool $default = true): bool => (bool) ($settings->{$key} ?? $default);

        $schoolNameFont = $settings->card_school_name_font_size ?? 7.2;
        $schoolDetailFont = $settings->card_school_detail_font_size ?? 5.4;
        $sloganFont = $settings->card_slogan_font_size ?? 4.8;
        $titleFont = $settings->card_title_font_size ?? 4.7;
        $examTypeFont = $settings->card_exam_type_font_size ?? 7.4;
        $examNameFont = $settings->card_exam_name_font_size ?? 6.8;
        $nameFont = $settings->card_name_font_size ?? 7.2;
        $studentDetailFont = $settings->card_student_detail_font_size ?? 8.5;
        $frontPaddingMm = (float) ($settings->card_front_padding_value ?? 0.8);
        $photoHeightMm = (float) ($settings->card_photo_height_value ?? 2.7) * 10;
        $logoSizeMm = (float) ($settings->card_logo_size_value ?? 0.8) * 10;

        $brandHeightMm = $ptToMm($schoolNameFont) + $spacingVertical('school_name', 'padding') + $spacingVertical('school_name', 'margin');
        if ($isVisible('card_show_school_detail_front')) {
            $brandHeightMm += $ptToMm($schoolDetailFont * 1.15) + 0.55 + $spacingVertical('school_detail', 'padding') + $spacingVertical('school_detail', 'margin');
        }
        if ($isVisible('card_show_slogan_front')) {
            $brandHeightMm += $ptToMm($sloganFont * 1.1) + $spacingVertical('slogan', 'padding') + $spacingVertical('slogan', 'margin');
        }
        if ($isVisible('card_show_logo_front')) {
            $brandHeightMm = max($brandHeightMm, $logoSizeMm);
        }

        $examHeightMm = 0;
        if ($isVisible('card_show_title_front')) {
            $examHeightMm += $ptToMm($titleFont) + 1.5 + $spacingVertical('title', 'padding') + $spacingVertical('title', 'margin');
        }
        if ($isVisible('card_show_exam_type_front')) {
            $examHeightMm += $ptToMm($examTypeFont * 1.05) + 0.6 + $spacingVertical('exam_type', 'padding') + $spacingVertical('exam_type', 'margin');
        }
        if ($isVisible('card_show_exam_name_front') && $isVisible('card_exam_name_badge_front', false)) {
            $examHeightMm += $ptToMm($examNameFont * 1.08) + 1.5 + 0.35 + $spacingVertical('exam_name', 'padding') + $spacingVertical('exam_name', 'margin');
        }

        $visibleStudentRows = 1;
        foreach ([
            'card_show_father_name_front',
            'card_show_mother_name_front',
            'card_show_roll_front',
            'card_show_class_front',
            'card_show_section_front',
            'card_show_session_front',
        ] as $visibilityKey) {
            if ($isVisible($visibilityKey, str_contains($visibilityKey, 'roll') || str_contains($visibilityKey, 'class') || str_contains($visibilityKey, 'section') || str_contains($visibilityKey, 'session'))) {
                $visibleStudentRows++;
            }
        }

        $studentRowHeightMm = $ptToMm($studentDetailFont * 1.08) + $spacingVertical('student_detail', 'padding') + $spacingVertical('student_detail', 'margin');
        $studentNameHeightMm = $ptToMm(max($nameFont, $studentDetailFont)) + $spacingVertical('name', 'padding') + $spacingVertical('name', 'margin');
        $infoHeightMm = $studentNameHeightMm + 1.3 + ($visibleStudentRows * $studentRowHeightMm) + max(0, $visibleStudentRows - 1) * 1.05;
        $contentHeightMm = max($infoHeightMm, $isVisible('card_show_photo_front') ? $photoHeightMm : 0);
        $bodyHeightMm = (2 * $frontPaddingMm) + $contentHeightMm + 2 + 12;
        $footerHeightMm = $isVisible('card_show_footer_front')
            ? 1.4 + $ptToMm($settings->card_footer_font_size ?? 4.5) * 1.05 + $spacingVertical('footer', 'padding') + $spacingVertical('footer', 'margin')
            : 0;
        $minimumCardHeightMm = 0.9 + (2 * $frontPaddingMm) + max($brandHeightMm, $examHeightMm) + $bodyHeightMm + $footerHeightMm + 1;
        $minimumCardHeightValue = $dimensionUnit === 'px'
            ? ($minimumCardHeightMm / 25.4) * 96
            : $minimumCardHeightMm / 10;

        $cardsPerRow = min($requestedCardsPerRow, $maxCardsPerRow);
        $pageRows = min($requestedPageRows, $maxPageRows);
        $cardsPerPage = min($requestedCardsPerPage, max(1, $cardsPerRow * $pageRows));

        return [
            'requestedCardsPerPage' => $requestedCardsPerPage,
            'requestedCardsPerRow' => $requestedCardsPerRow,
            'requestedPageRows' => $requestedPageRows,
            'cardsPerPage' => $cardsPerPage,
            'cardsPerRow' => $cardsPerRow,
            'pageRows' => $pageRows,
            'maxCardsPerPage' => $maxCardsPerPage,
            'maxCardsPerRow' => $maxCardsPerRow,
            'maxPageRows' => $maxPageRows,
            'recommendedCardHeightMm' => round($recommendedCardHeightMm, 2),
            'recommendedCardHeightValue' => round($recommendedCardHeightValue, 2),
            'minimumCardHeightMm' => round($minimumCardHeightMm, 2),
            'minimumCardHeightValue' => round($minimumCardHeightValue, 2),
            'contentFitsRecommendedHeight' => $recommendedCardHeightMm >= $minimumCardHeightMm,
            'requestedColumnsFit' => $maxCardsPerRow >= $requestedCardsPerRow,
            'cardWidthMm' => round($cardWidthMm, 2),
            'cardHeightMm' => round($cardHeightMm, 2),
            'gridGapMm' => round($gapMm, 2),
            'gridGapValue' => round($gapValue, 2),
            'cardWidthValue' => round($widthValue, 2),
            'cardHeightValue' => round($heightValue, 2),
            'cardDimensionUnit' => $dimensionUnit,
            'cardWidthDefaultCm' => round($cardWidthMm / 10, 2),
            'cardHeightDefaultCm' => round($cardHeightMm / 10, 2),
            'gridGapDefaultCm' => round($gapMm / 10, 2),
            'gridGapDefaultPx' => round($gapMm / 25.4 * 96, 2),
            'cardWidthDefaultPx' => round($cardWidthMm / 25.4 * 96, 2),
            'cardHeightDefaultPx' => round($cardHeightMm / 25.4 * 96, 2),
            'gapXmm' => $gapMm,
            'gapYmm' => $gapMm,
            'marginMm' => $marginLeftMm,
            'marginTopMm' => $marginTopMm,
            'marginBottomMm' => $marginBottomMm,
            'marginLeftMm' => $marginLeftMm,
            'marginRightMm' => $marginRightMm,
            'pageWidthMm' => round($pageDocumentWidthMm, 2),
            'pageHeightMm' => round($pageDocumentHeightMm, 2),
            'usableWidthMm' => round($pageWidthMm, 2),
            'usableHeightMm' => round($pageHeightMm, 2),
        ];
    }

    private function normalizeDimensionValue(mixed $value, string $unit, float $fallbackMm): float
    {
        $numeric = is_numeric($value) ? (float) $value : null;

        if ($numeric !== null && $numeric > 0) {
            return $numeric;
        }

        return $unit === 'px'
            ? round($fallbackMm / 25.4 * 96, 2)
            : round($fallbackMm / 10, 2);
    }

    private function dimensionToMm(float $value, string $unit): float
    {
        return $unit === 'px'
            ? ($value / 96) * 25.4
            : $value * 10;
    }

    private function applyCardDefaults(?AdmitSeatCardSetting $settings): AdmitSeatCardSetting
    {
        $settings ??= new AdmitSeatCardSetting();

        return $settings->fill([
            'card_is_transparent' => $settings->card_is_transparent ?? false,
            'card_color_type' => $settings->card_color_type ?? 'gradient',
            'card_front_alignment' => $settings->card_front_alignment ?? 'center',
            'page_width_mm' => $settings->page_width_mm ?? 210,
            'page_height_mm' => $settings->page_height_mm ?? 297,
            'page_margin_top_mm' => $settings->page_margin_top_mm ?? 10,
            'page_margin_right_mm' => $settings->page_margin_right_mm ?? 6.35,
            'page_margin_bottom_mm' => $settings->page_margin_bottom_mm ?? 4,
            'page_margin_left_mm' => $settings->page_margin_left_mm ?? 6.35,
            'card_back_alignment' => $settings->card_back_alignment ?? 'center',
            'card_front_padding_value' => $settings->card_front_padding_value ?? 0.8,
            'card_back_padding_value' => $settings->card_back_padding_value ?? 0.8,
            'card_photo_width_value' => $settings->card_photo_width_value ?? 1.8,
            'card_photo_height_value' => $settings->card_photo_height_value ?? 2.7,
            'card_photo_fit' => $settings->card_photo_fit ?? 'cover',
            'card_logo_size_value' => $settings->card_logo_size_value ?? 0.8,
            'card_signature_size_value' => $settings->card_signature_size_value ?? 1.4,
            'card_school_name_font_size' => $settings->card_school_name_font_size ?? 7.2,
            'card_school_detail_font_size' => $settings->card_school_detail_font_size ?? 5.4,
            'card_slogan_font_size' => $settings->card_slogan_font_size ?? 4.8,
            'card_title_font_size' => $settings->card_title_font_size ?? 4.7,
            'card_vertical_label_font_size' => $settings->card_vertical_label_font_size ?? 5.2,
            'card_name_font_size' => $settings->card_name_font_size ?? 7.2,
            'card_name_text_color' => $settings->card_name_text_color ?? '#111827',
            'card_exam_type_font_size' => $settings->card_exam_type_font_size ?? 7.4,
            'card_exam_name_font_size' => $settings->card_exam_name_font_size ?? 6.8,
            'card_footer_font_size' => $settings->card_footer_font_size ?? 4.5,
            'card_student_detail_alignment' => $settings->card_student_detail_alignment ?? 'left',
            'card_student_detail_font_size' => $settings->card_student_detail_font_size ?? 8.5,
            'card_student_detail_text_color' => $settings->card_student_detail_text_color ?? '#111827',
            'card_text_padding_value' => $settings->card_text_padding_value ?? 0,
            'card_text_margin_value' => $settings->card_text_margin_value ?? 0,
            'card_typography_spacing' => $settings->card_typography_spacing ?? [],
            'card_student_field_order' => $settings->card_student_field_order ?? self::STUDENT_FIELD_ORDER,
            'card_element_positions' => $settings->card_element_positions ?? [],
            'card_color_gradient_1' => $settings->card_color_gradient_1 ?? '#1e3a5f',
            'card_color_gradient_2' => $settings->card_color_gradient_2 ?? '#2563eb',
            'card_solid_color' => $settings->card_solid_color ?? '#1e3a5f',
            'card_school_name_text_color' => $settings->card_school_name_text_color ?? '#ffffff',
            'card_school_detail_text_color' => $settings->card_school_detail_text_color ?? '#e5e7eb',
            'card_title_text_color' => $settings->card_title_text_color ?? '#ffffff',
            'card_vertical_label_text_color' => $settings->card_vertical_label_text_color ?? '#16a085',
            'card_exam_type_text_color' => $settings->card_exam_type_text_color ?? '#ffffff',
            'card_exam_name_text_color' => $settings->card_exam_name_text_color ?? '#e5e7eb',
            'card_footer_text_color' => $settings->card_footer_text_color ?? '#e5e7eb',
            'card_show_logo_front' => $settings->card_show_logo_front ?? true,
            'card_show_logo_back' => $settings->card_show_logo_back ?? true,
            'card_show_photo_front' => $settings->card_show_photo_front ?? true,
            'card_show_father_name_front' => $settings->card_show_father_name_front ?? false,
            'card_show_mother_name_front' => $settings->card_show_mother_name_front ?? false,
            'card_show_student_name_label_front' => $settings->card_show_student_name_label_front ?? false,
            'card_show_roll_front' => $settings->card_show_roll_front ?? true,
            'card_show_class_front' => $settings->card_show_class_front ?? true,
            'card_show_section_front' => $settings->card_show_section_front ?? true,
            'card_show_session_front' => $settings->card_show_session_front ?? true,
            'card_show_vertical_label_front' => $settings->card_show_vertical_label_front ?? false,
            'card_exam_name_badge_front' => $settings->card_exam_name_badge_front ?? false,
            'card_show_footer_front' => $settings->card_show_footer_front ?? true,
            'card_show_footer_back' => $settings->card_show_footer_back ?? true,
            'card_show_school_detail_front' => $settings->card_show_school_detail_front ?? true,
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
