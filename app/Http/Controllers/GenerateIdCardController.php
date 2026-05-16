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

        return view('pages.generate-id-cards.index', compact(
            'sessions', 'classes', 'sections', 'groups', 'students', 'setting'
        ));
    }

    public function pdf(Request $request)
    {
        [, , , , $students, $setting] = $this->buildData($request);

        if ($students->isEmpty()) {
            return redirect()->route('students.id-cards')->with('error', 'No data to export.');
        }

        $html = view('pages.generate-id-cards.pdf', compact('students', 'setting'))->render();

        $mpdf = new Mpdf([
            'margin_top'               => 8,
            'margin_bottom'            => 8,
            'margin_left'              => 8,
            'margin_right'             => 8,
            'img_dpi'                  => 150,
            'allow_charset_conversion' => false,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->WriteHTML($html);
        $mpdf->Output('id-cards.pdf', 'D');
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

        if ($request->filled('session_id')) {
            $students = Student::with([
                'academicInformations' => fn($q) => $q
                    ->where('academic_session_id', $request->session_id)
                    ->with(['schoolClass', 'section', 'group', 'academicSession']),
            ])
            ->whereHas('academicInformations', fn($q) =>
                $q->where('academic_session_id', $request->session_id)
                  ->when($request->filled('class_id'),   fn($q) => $q->where('school_class_id', $request->class_id))
                  ->when($request->filled('section_id'), fn($q) => $q->where('section_id', $request->section_id))
                  ->when($request->filled('group_id'),   fn($q) => $q->where('group_id', $request->group_id))
            )
            ->orderBy('full_name_en')
            ->get();
        }

        return [$sessions, $classes, $sections, $groups, $students, $setting];
    }
}
