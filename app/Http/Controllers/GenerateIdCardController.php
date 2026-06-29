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
use Mpdf\Mpdf;

class GenerateIdCardController extends Controller
{
    public function index(Request $request)
    {
        [$sessions, $classes, $sections, $groups, $students, $setting, $cardType, $cardSettingsMap, $cardSettings, $layout] = $this->buildData($request);

        return view('pages.generate-id-cards.index', compact(
            'sessions', 'classes', 'sections', 'groups', 'students', 'setting', 'cardType', 'cardSettingsMap', 'cardSettings', 'layout'
        ));
    }

    public function pdf(Request $request)
    {
        [, , , , $students, $setting, $cardType, $cardSettingsMap, $cardSettings, $layout] = $this->buildData($request);

        if ($students->isEmpty()) {
            return redirect()->route('students.id-cards')->with('error', 'No data to export.');
        }

        $html = view('pages.generate-id-cards.pdf', compact('students', 'setting', 'cardType', 'cardSettings', 'layout'))->render();

        $filename = $cardType === 'library_card' ? 'library-cards.pdf' : 'id-cards.pdf';

        $mpdf = new Mpdf([
            'format'                   => 'A4-L',
            'margin_top'               => 10,
            'margin_bottom'            => 8,
            'margin_left'              => 8,
            'margin_right'             => 8,
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
        $classes  = SchoolClass::get();
        $setting  = SchoolSetting::first();
        $cardType = $this->normalizeCardType($request->input('card_type', 'id_card'));
        $cardTypeId = $this->cardTypeToSettingType($cardType);

        $sections = $request->filled('class_id')
            ? Section::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();
        $groups = Group::orderBy('name_en')->get();

        $students = collect();
        $studentCid = trim((string) $request->input('student_cid', ''));
        $cardSettingsMap = AdmitSeatCardSetting::query()
            ->whereIn('card_type', [3, 4])
            ->get()
            ->keyBy('card_type');
        $cardSettingsMap->put(3, $cardSettingsMap->get(3) ?? AdmitSeatCardSetting::current(3));
        $cardSettingsMap->put(4, $cardSettingsMap->get(4) ?? AdmitSeatCardSetting::current(4));
        $cardSettings = $this->applyCardThemeDefaults($cardSettingsMap->get($cardTypeId));
        $layout = $this->buildLayout($cardSettings);

        $academicInfoConstraint = function ($query) use ($request) {
            $query->when($request->filled('session_id'), fn ($q) => $q->where('academic_session_id', $request->session_id))
                ->when($request->filled('class_id'), fn ($q) => $q->where('school_class_id', $request->class_id))
                ->when($request->filled('section_id'), fn ($q) => $q->where('section_id', $request->section_id))
                ->when($request->filled('group_id'), fn ($q) => $q->where('group_id', $request->group_id))
                ->with(['schoolClass', 'section', 'group', 'academicSession'])
                ->orderByDesc('academic_session_id')
                ->orderByDesc('id');
        };

        if ($studentCid !== '') {
            $students = Student::with(['academicInformations' => $academicInfoConstraint])
                ->where('student_cid', $studentCid)
                ->orderBy('full_name_en')
                ->get();
        } elseif ($request->filled('session_id')) {
            $students = Student::with(['academicInformations' => $academicInfoConstraint])
                ->whereHas('academicInformations', function ($query) use ($request) {
                    $query->where('academic_session_id', $request->session_id)
                        ->when($request->filled('class_id'), fn ($q) => $q->where('school_class_id', $request->class_id))
                        ->when($request->filled('section_id'), fn ($q) => $q->where('section_id', $request->section_id))
                        ->when($request->filled('group_id'), fn ($q) => $q->where('group_id', $request->group_id));
                })
                ->orderBy('full_name_en')
                ->get();
        }

        return [$sessions, $classes, $sections, $groups, $students, $setting, $cardType, $cardSettingsMap, $cardSettings, $layout];
    }

    private function normalizeCardType(?string $cardType): string
    {
        return in_array($cardType, ['id_card', 'library_card'], true) ? $cardType : 'id_card';
    }

    public function saveSettings(Request $request)
    {
        $cardType = $this->normalizeCardType($request->input('card_type', 'id_card'));
        $cardTypeId = $this->cardTypeToSettingType($cardType);

        $validated = $request->validate([
            'card_type' => ['required', 'in:id_card,library_card'],
            'cards_per_page' => ['required', 'integer', 'min:1', 'max:12'],
            'cards_per_row' => ['required', 'integer', 'min:1', 'max:10'],
            'card_width_value' => ['required', 'numeric', 'min:0.1'],
            'card_height_value' => ['required', 'numeric', 'min:0.1'],
            'grid_gap_value' => ['required', 'numeric', 'min:0.1'],
            'card_dimension_unit' => ['required', 'in:cm,px'],
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
            'card_is_transparent' => $isTransparent,
            'card_color_type' => $validated['card_color_type'],
            'card_color_gradient_1' => $validated['card_color_gradient_1'] ?: '#1e3a5f',
            'card_color_gradient_2' => $validated['card_color_gradient_2'] ?: '#2563eb',
            'card_solid_color' => $validated['card_solid_color'] ?: '#1e3a5f',
            'card_school_name_text_color' => data_get($validated, 'card_school_name_text_color') ?: ($isTransparent ? '#111827' : '#ffffff'),
            'card_school_detail_text_color' => data_get($validated, 'card_school_detail_text_color') ?: ($isTransparent ? '#334155' : '#e5e7eb'),
            'card_slogan_text_color' => data_get($validated, 'card_slogan_text_color') ?: ($isTransparent ? '#111827' : '#e5e7eb'),
            'card_back_notice_text_color' => data_get($validated, 'card_back_notice_text_color') ?: ($isTransparent ? '#64748b' : '#94a3b8'),
            'card_footer_text_color' => data_get($validated, 'card_footer_text_color') ?: ($isTransparent ? '#111827' : '#e5e7eb'),
            'card_title_text_color' => data_get($validated, 'card_title_text_color') ?: ($isTransparent ? '#111827' : '#ffffff'),
            'card_exam_type_text_color' => data_get($validated, 'card_exam_type_text_color') ?: ($isTransparent ? '#111827' : '#ffffff'),
            'card_exam_name_text_color' => data_get($validated, 'card_exam_name_text_color') ?: ($isTransparent ? '#334155' : '#e5e7eb'),
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

        AdmitSeatCardSetting::current($cardTypeId)->fill($payload)->save();

        return back()->with('success', 'Card settings saved.');
    }

    private function cardTypeToSettingType(string $cardType): int
    {
        return $cardType === 'library_card' ? 4 : 3;
    }

    private function buildLayout(AdmitSeatCardSetting $settings): array
    {
        $cardsPerPage = max(1, min(12, (int) ($settings->cards_per_page ?? 4)));
        $cardsPerRow = max(1, min(10, (int) ($settings->cards_per_row ?? 2)));
        $cardsPerRow = min($cardsPerRow, $cardsPerPage);
        $pageRows = (int) ceil($cardsPerPage / $cardsPerRow);

        $marginLeftMm = 8;
        $marginRightMm = 8;
        $marginTopMm = 10;
        $marginBottomMm = 8;
        $pageWidthMm = 297 - ($marginLeftMm + $marginRightMm);
        $pageHeightMm = 210 - ($marginTopMm + $marginBottomMm);

        $dimensionUnit = strtolower((string) ($settings->card_dimension_unit ?? 'cm'));
        $dimensionUnit = in_array($dimensionUnit, ['cm', 'px'], true) ? $dimensionUnit : 'cm';

        $gapValue = $this->normalizeDimensionValue($settings->grid_gap_value ?? null, $dimensionUnit, 0.5);
        $cardWidthValue = $this->normalizeDimensionValue($settings->card_width_value ?? null, $dimensionUnit, 5.4);
        $cardHeightValue = $this->normalizeDimensionValue($settings->card_height_value ?? null, $dimensionUnit, 8.4);

        $gapMm = $this->dimensionToMm($gapValue, $dimensionUnit);
        $cardWidthMm = $this->dimensionToMm($cardWidthValue, $dimensionUnit);
        $cardHeightMm = $this->dimensionToMm($cardHeightValue, $dimensionUnit);

        $maxCardsPerRow = max(1, (int) floor(($pageWidthMm + $gapMm) / ($cardWidthMm + $gapMm)));
        $maxPageRows = max(1, (int) floor(($pageHeightMm + $gapMm) / ($cardHeightMm + $gapMm)));

        $cardsPerRow = min($cardsPerRow, $maxCardsPerRow);
        $pageRows = min($pageRows, $maxPageRows);
        $cardsPerPage = min($cardsPerPage, max(1, $cardsPerRow * $pageRows));

        return [
            'cardsPerPage' => $cardsPerPage,
            'cardsPerRow' => $cardsPerRow,
            'pageRows' => $pageRows,
            'cardWidthMm' => round($cardWidthMm, 2),
            'cardHeightMm' => round($cardHeightMm, 2),
            'gridGapMm' => round($gapMm, 2),
            'gridGapValue' => round($gapValue, 2),
            'cardWidthValue' => round($cardWidthValue, 2),
            'cardHeightValue' => round($cardHeightValue, 2),
            'cardDimensionUnit' => $dimensionUnit,
            'cardWidthDefaultCm' => round($cardWidthMm / 10, 2),
            'cardHeightDefaultCm' => round($cardHeightMm / 10, 2),
            'gridGapDefaultCm' => round($gapMm / 10, 2),
            'cardWidthDefaultPx' => round($cardWidthMm / 25.4 * 96, 2),
            'cardHeightDefaultPx' => round($cardHeightMm / 25.4 * 96, 2),
            'gridGapDefaultPx' => round($gapMm / 25.4 * 96, 2),
        ];
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
        ]);
    }
}
