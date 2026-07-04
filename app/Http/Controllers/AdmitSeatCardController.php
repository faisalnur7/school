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
use Mpdf\Mpdf;

class AdmitSeatCardController extends Controller
{
    public function index(Request $request)
    {
        [$sessions, $classes, $sections, $groups, $exams, $students, $setting, $cardType, $cardSettingsMap, $cardSettings, $examType, $selectedExam, $layout] = $this->buildData($request);

        return view('pages.admit-seat-cards.index', compact(
            'sessions', 'classes', 'sections', 'groups', 'exams', 'students', 'setting', 'cardSettings', 'cardSettingsMap', 'cardType', 'examType', 'selectedExam', 'layout'
        ));
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
            'format'                   => 'A4',
            'margin_top'               => 10,
            'margin_bottom'            => 4,
            'margin_left'              => 6.35,
            'margin_right'             => 6.35,
            'img_dpi'                  => 150,
            'allow_charset_conversion' => false,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->WriteHTML($html);
        $mpdf->Output($filename, 'D');
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
        $examsQuery = Exam::query()->orderByDesc('id');

        if ($request->filled('exam_type')) {
            $examsQuery->where('type', $request->exam_type);
        }

        $selectedExam = $request->filled('exam_id')
            ? Exam::find($request->exam_id)
            : null;

        if (!$examType && $selectedExam) {
            $examType = $selectedExam->type;
        }

        if ($examType) {
            $examsQuery->where('type', $examType);
        }

        $exams = $examsQuery->get();

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
            'card_front_alignment' => ['nullable', 'in:left,center,right'],
            'card_back_alignment' => ['nullable', 'in:left,center,right'],
            'card_front_padding_value' => ['nullable', 'numeric', 'min:0'],
            'card_back_padding_value' => ['nullable', 'numeric', 'min:0'],
            'card_photo_width_value' => ['nullable', 'numeric', 'min:0.1'],
            'card_photo_height_value' => ['nullable', 'numeric', 'min:0.1'],
            'card_photo_fit' => ['nullable', 'in:cover,contain'],
            'card_logo_size_value' => ['nullable', 'numeric', 'min:0.1'],
            'card_school_name_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_school_detail_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_slogan_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_title_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_name_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_name_text_color' => ['nullable', 'string', 'max:20'],
            'card_exam_type_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_exam_name_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_student_detail_alignment' => ['nullable', 'in:left,center,right'],
            'card_student_detail_font_size' => ['nullable', 'numeric', 'min:1'],
            'card_student_detail_text_color' => ['nullable', 'string', 'max:20'],
            'card_is_transparent' => ['nullable', 'boolean'],
            'card_color_type' => ['required', 'in:gradient,solid'],
            'card_color_gradient_1' => ['nullable', 'string', 'max:20'],
            'card_color_gradient_2' => ['nullable', 'string', 'max:20'],
            'card_solid_color' => ['nullable', 'string', 'max:20'],
            'card_school_name_text_color' => ['nullable', 'string', 'max:20'],
            'card_school_detail_text_color' => ['nullable', 'string', 'max:20'],
            'card_slogan_text_color' => ['nullable', 'string', 'max:20'],
            'card_title_text_color' => ['nullable', 'string', 'max:20'],
            'card_exam_type_text_color' => ['nullable', 'string', 'max:20'],
            'card_exam_name_text_color' => ['nullable', 'string', 'max:20'],
            'card_logo' => ['nullable', 'image', 'max:100'],
            'card_principal_signature' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:100'],
        ]);

        $isTransparent = $request->boolean('card_is_transparent');

        $payload = [
            'card_type' => $cardTypeId,
            'cards_per_page' => $validated['cards_per_page'],
            'cards_per_row' => $validated['cards_per_row'],
            'card_width_value' => $validated['card_width_value'],
            'card_height_value' => $validated['card_height_value'],
            'grid_gap_value' => $validated['grid_gap_value'],
            'card_dimension_unit' => $validated['card_dimension_unit'],
            'card_front_alignment' => data_get($validated, 'card_front_alignment', 'center'),
            'card_back_alignment' => data_get($validated, 'card_back_alignment', 'center'),
            'card_front_padding_value' => data_get($validated, 'card_front_padding_value', 0.8),
            'card_back_padding_value' => data_get($validated, 'card_back_padding_value', 0.8),
            'card_photo_width_value' => data_get($validated, 'card_photo_width_value', 1.8),
            'card_photo_height_value' => data_get($validated, 'card_photo_height_value', 2.7),
            'card_photo_fit' => data_get($validated, 'card_photo_fit', 'cover'),
            'card_logo_size_value' => data_get($validated, 'card_logo_size_value', 0.8),
            'card_school_name_font_size' => data_get($validated, 'card_school_name_font_size', 7.2),
            'card_school_detail_font_size' => data_get($validated, 'card_school_detail_font_size', 5.4),
            'card_slogan_font_size' => data_get($validated, 'card_slogan_font_size', 4.8),
            'card_title_font_size' => data_get($validated, 'card_title_font_size', 4.7),
            'card_name_font_size' => data_get($validated, 'card_name_font_size', 7.2),
            'card_name_text_color' => data_get($validated, 'card_name_text_color') ?: '#111827',
            'card_exam_type_font_size' => data_get($validated, 'card_exam_type_font_size', 7.4),
            'card_exam_name_font_size' => data_get($validated, 'card_exam_name_font_size', 6.8),
            'card_student_detail_alignment' => data_get($validated, 'card_student_detail_alignment', 'left'),
            'card_student_detail_font_size' => data_get($validated, 'card_student_detail_font_size', 8.5),
            'card_student_detail_text_color' => data_get($validated, 'card_student_detail_text_color') ?: ($isTransparent ? '#111827' : '#111827'),
            'card_is_transparent' => $isTransparent,
            'card_color_type' => $validated['card_color_type'],
            'card_color_gradient_1' => $validated['card_color_gradient_1'] ?: '#1e3a5f',
            'card_color_gradient_2' => $validated['card_color_gradient_2'] ?: '#2563eb',
            'card_solid_color' => $validated['card_solid_color'] ?: '#1e3a5f',
            'card_school_name_text_color' => data_get($validated, 'card_school_name_text_color') ?: ($isTransparent ? '#111827' : '#ffffff'),
            'card_school_detail_text_color' => data_get($validated, 'card_school_detail_text_color') ?: ($isTransparent ? '#334155' : '#e5e7eb'),
            'card_slogan_text_color' => data_get($validated, 'card_slogan_text_color') ?: ($isTransparent ? '#334155' : '#e5e7eb'),
            'card_title_text_color' => data_get($validated, 'card_title_text_color') ?: ($isTransparent ? '#111827' : '#ffffff'),
            'card_exam_type_text_color' => data_get($validated, 'card_exam_type_text_color') ?: ($isTransparent ? '#111827' : '#ffffff'),
            'card_exam_name_text_color' => data_get($validated, 'card_exam_name_text_color') ?: ($isTransparent ? '#334155' : '#e5e7eb'),
            'card_show_logo_front' => $request->boolean('card_show_logo_front'),
            'card_show_logo_back' => $request->boolean('card_show_logo_back'),
            'card_show_photo_front' => $request->boolean('card_show_photo_front'),
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

        $marginLeftMm = 6.35; // 24px at 96dpi
        $marginRightMm = 6.35;
        $marginTopMm = 10;
        $marginBottomMm = 4;
        $pageWidthMm = 210 - ($marginLeftMm + $marginRightMm);
        $pageHeightMm = 297 - ($marginTopMm + $marginBottomMm);

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
            'card_back_alignment' => $settings->card_back_alignment ?? 'center',
            'card_front_padding_value' => $settings->card_front_padding_value ?? 0.8,
            'card_back_padding_value' => $settings->card_back_padding_value ?? 0.8,
            'card_photo_width_value' => $settings->card_photo_width_value ?? 1.8,
            'card_photo_height_value' => $settings->card_photo_height_value ?? 2.7,
            'card_photo_fit' => $settings->card_photo_fit ?? 'cover',
            'card_logo_size_value' => $settings->card_logo_size_value ?? 0.8,
            'card_school_name_font_size' => $settings->card_school_name_font_size ?? 7.2,
            'card_school_detail_font_size' => $settings->card_school_detail_font_size ?? 5.4,
            'card_slogan_font_size' => $settings->card_slogan_font_size ?? 4.8,
            'card_title_font_size' => $settings->card_title_font_size ?? 4.7,
            'card_name_font_size' => $settings->card_name_font_size ?? 7.2,
            'card_name_text_color' => $settings->card_name_text_color ?? '#111827',
            'card_exam_type_font_size' => $settings->card_exam_type_font_size ?? 7.4,
            'card_exam_name_font_size' => $settings->card_exam_name_font_size ?? 6.8,
            'card_student_detail_alignment' => $settings->card_student_detail_alignment ?? 'left',
            'card_student_detail_font_size' => $settings->card_student_detail_font_size ?? 8.5,
            'card_student_detail_text_color' => $settings->card_student_detail_text_color ?? '#111827',
            'card_color_gradient_1' => $settings->card_color_gradient_1 ?? '#1e3a5f',
            'card_color_gradient_2' => $settings->card_color_gradient_2 ?? '#2563eb',
            'card_solid_color' => $settings->card_solid_color ?? '#1e3a5f',
            'card_school_name_text_color' => $settings->card_school_name_text_color ?? '#ffffff',
            'card_school_detail_text_color' => $settings->card_school_detail_text_color ?? '#e5e7eb',
            'card_title_text_color' => $settings->card_title_text_color ?? '#ffffff',
            'card_exam_type_text_color' => $settings->card_exam_type_text_color ?? '#ffffff',
            'card_exam_name_text_color' => $settings->card_exam_name_text_color ?? '#e5e7eb',
            'card_show_logo_front' => $settings->card_show_logo_front ?? true,
            'card_show_logo_back' => $settings->card_show_logo_back ?? true,
            'card_show_photo_front' => $settings->card_show_photo_front ?? true,
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
