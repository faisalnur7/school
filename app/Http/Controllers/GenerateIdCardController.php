<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
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
        [$sessions, $classes, $sections, $groups, $students, $setting] = $this->buildData($request);
        $cardType = $this->normalizeCardType($request->input('card_type', 'id_card'));

        return view('pages.generate-id-cards.index', compact(
            'sessions', 'classes', 'sections', 'groups', 'students', 'setting', 'cardType'
        ));
    }

    public function pdf(Request $request)
    {
        [, , , , $students, $setting] = $this->buildData($request);
        $cardType = $this->normalizeCardType($request->input('card_type', 'id_card'));

        if ($students->isEmpty()) {
            return redirect()->route('students.id-cards')->with('error', 'No data to export.');
        }

        $html = view('pages.generate-id-cards.pdf', compact('students', 'setting', 'cardType'))->render();

        $filename = $cardType === 'library_card' ? 'library-cards.pdf' : 'id-cards.pdf';

        $mpdf = new Mpdf([
            'format'                   => 'A4-L',
            'margin_top'               => 8,
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

        $sections = $request->filled('class_id')
            ? Section::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();
        $groups = Group::orderBy('name_en')->get();

        $students = collect();
        $studentCid = trim((string) $request->input('student_cid', ''));

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

        return [$sessions, $classes, $sections, $groups, $students, $setting];
    }

    private function normalizeCardType(?string $cardType): string
    {
        return in_array($cardType, ['id_card', 'library_card'], true) ? $cardType : 'id_card';
    }
}
