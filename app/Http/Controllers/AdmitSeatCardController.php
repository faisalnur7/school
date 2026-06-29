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

        $html = view('pages.admit-seat-cards.pdf', compact('students', 'setting', 'cardType', 'examType', 'selectedExam', 'layout'))->render();
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
        $setting = SchoolSetting::first();
        $cardType = $this->normalizeCardType($request->input('card_type', 'admit_card'));
        $cardTypeId = $this->cardTypeToSettingType($cardType);
        $examType = $request->input('exam_type');
        $selectedExam = null;
        $studentCid = trim((string) $request->input('student_cid', ''));
        $cardSettingsMap = AdmitSeatCardSetting::query()
            ->whereIn('card_type', [1, 2])
            ->get()
            ->keyBy('card_type');
        $cardSettings = $cardSettingsMap->get($cardTypeId) ?? AdmitSeatCardSetting::current($cardTypeId);
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
        ]);

        AdmitSeatCardSetting::current($cardTypeId)->fill([
            'card_type' => $cardTypeId,
            'cards_per_page' => $validated['cards_per_page'],
            'cards_per_row' => $validated['cards_per_row'],
            'card_width_value' => $validated['card_width_value'],
            'card_height_value' => $validated['card_height_value'],
            'grid_gap_value' => $validated['grid_gap_value'],
            'card_dimension_unit' => $validated['card_dimension_unit'],
        ])->save();

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
        $cardsPerPage = max(1, min(12, (int) ($settings->cards_per_page ?? 8)));
        $cardsPerRow = max(1, min(10, (int) ($settings->cards_per_row ?? 2)));
        $cardsPerRow = min($cardsPerRow, $cardsPerPage);
        $pageRows = (int) ceil($cardsPerPage / $cardsPerRow);

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
}
