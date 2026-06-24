<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\Group;
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
        [$sessions, $classes, $sections, $groups, $exams, $students, $setting, $examType, $selectedExam, $layout] = $this->buildData($request);
        $cardType = $this->normalizeCardType($request->input('card_type', 'admit_card'));

        return view('pages.admit-seat-cards.index', compact(
            'sessions', 'classes', 'sections', 'groups', 'exams', 'students', 'setting', 'cardType', 'examType', 'selectedExam', 'layout'
        ));
    }

    public function pdf(Request $request)
    {
        [, , , , , $students, $setting, $examType, $selectedExam, $layout] = $this->buildData($request);
        $cardType = $this->normalizeCardType($request->input('card_type', 'admit_card'));

        if ($students->isEmpty()) {
            return redirect()->route('results.admit-seat-cards.index')->with('error', 'No data to export.');
        }

        $html = view('pages.admit-seat-cards.pdf', compact('students', 'setting', 'cardType', 'examType', 'selectedExam', 'layout'))->render();
        $filename = $cardType === 'seat_card' ? 'seat-cards.pdf' : 'admit-cards.pdf';

        $mpdf = new Mpdf([
            'format'                   => 'A4',
            'margin_top'               => 19.05,
            'margin_bottom'            => 19.05,
            'margin_left'              => 19.05,
            'margin_right'             => 19.05,
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
        $examType = $request->input('exam_type');
        $selectedExam = null;
        $studentCid = trim((string) $request->input('student_cid', ''));
        $layout = $this->buildLayout($request);

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

        return [$sessions, $classes, $sections, $groups, $exams, $students, $setting, $examType, $selectedExam, $layout];
    }

    private function normalizeCardType(?string $cardType): string
    {
        return in_array($cardType, ['admit_card', 'seat_card'], true) ? $cardType : 'admit_card';
    }

    private function buildLayout(Request $request): array
    {
        $cardsPerPage = max(1, min(12, (int) $request->input('cards_per_page', 8)));
        $cardsPerRow = max(1, min(10, (int) $request->input('cards_per_row', 2)));
        $cardsPerRow = min($cardsPerRow, $cardsPerPage);
        $pageRows = (int) ceil($cardsPerPage / $cardsPerRow);

        while ($pageRows > 4 && $cardsPerRow < min(3, $cardsPerPage)) {
            $cardsPerRow++;
            $pageRows = (int) ceil($cardsPerPage / $cardsPerRow);
        }

        $marginXmm = 6.35; // 24px at 96dpi
        $marginYmm = 4;
        $pageWidthMm = 210 - ($marginXmm * 2);
        $pageHeightMm = 297 - ($marginYmm * 2);
        $gapXmm = 8.5;
        $gapYmm = 8.5;

        $cardWidthMm = ($pageWidthMm - ($gapXmm * ($cardsPerRow - 1))) / $cardsPerRow;
        $cardHeightMm = ($pageHeightMm - ($gapYmm * ($pageRows - 1))) / $pageRows;

        return [
            'cardsPerPage' => $cardsPerPage,
            'cardsPerRow' => $cardsPerRow,
            'pageRows' => $pageRows,
            'cardWidthMm' => round($cardWidthMm, 2),
            'cardHeightMm' => round($cardHeightMm, 2),
            'gapXmm' => $gapXmm,
            'gapYmm' => $gapYmm,
            'marginMm' => $marginXmm,
        ];
    }
}
