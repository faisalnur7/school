<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class StudentDueReportController extends Controller
{
    public function index(Request $request)
    {
        [$sessions, $classes, $sections, $groups, $rows] = $this->buildData($request);

        return view('pages.student-due-report.index', compact(
            'sessions', 'classes', 'sections', 'groups', 'rows'
        ));
    }

    public function pdf(Request $request)
    {
        [, , , , $rows] = $this->buildData($request);

        $session = AcademicSession::find($request->session_id);
        $month   = $request->filled('month') ? date('F', mktime(0, 0, 0, $request->month, 1)) : 'All Months';

        $html = view('pages.student-due-report.pdf', compact('rows', 'session', 'month'))->render();

        $mpdf = new Mpdf(['orientation' => 'L', 'margin_top' => 10, 'margin_bottom' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('students-due-report.pdf', 'D');
    }

    private function buildData(Request $request): array
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes  = SchoolClass::orderBy('name_en')->get();
        $sections = $request->filled('class_id')
            ? Section::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();
        $groups = Group::orderBy('name_en')->get();

        $rows = collect();

        if (!$request->filled('session_id')) {
            return [$sessions, $classes, $sections, $groups, $rows];
        }

        $students = Student::query()
            ->with([
                'academicInformations' => fn($q) => $q
                    ->where('academic_session_id', $request->session_id)
                    ->with(['schoolClass', 'section', 'group']),
                'fees' => fn($q) => $q
                    ->whereHas('feeSet', fn($fs) => $fs->where('academic_session_id', $request->session_id))
                    ->when($request->filled('month'), fn($q) =>
                        $q->whereMonth('due_date', $request->month)
                    )
                    ->where('is_active', 1)
                    ->with('paymentItems'),
            ])
            ->whereHas('academicInformations', fn($q) =>
                $q->where('academic_session_id', $request->session_id)
                  ->when($request->filled('class_id'),   fn($q) => $q->where('school_class_id', $request->class_id))
                  ->when($request->filled('section_id'), fn($q) => $q->where('section_id', $request->section_id))
                  ->when($request->filled('group_id'),   fn($q) => $q->where('group_id', $request->group_id))
            )
            ->whereHas('fees', fn($q) =>
                $q->whereHas('feeSet', fn($fs) => $fs->where('academic_session_id', $request->session_id))
                  ->where('is_active', 1)
            )
            ->get();

        $rows = $students->map(function (Student $s) {
            $ai        = $s->academicInformations->first();
            $totalFees = $s->fees->sum(fn($f) => (float)$f->amount - (float)$f->scholarship_discount);
            $totalPaid = $s->fees->sum(fn($f) => $f->paymentItems->sum('amount'));
            $due       = max(0, $totalFees - $totalPaid);
            return (object)[
                'student_id'   => $s->id,
                'roll'         => $ai?->roll,
                'cid'          => $s->student_cid,
                'name'         => $s->full_name_en,
                'class_name'   => $ai?->schoolClass?->name_en ?? '—',
                'section_name' => $ai?->section?->name_en ?? '—',
                'group_name'   => $ai?->group?->name_en ?? '—',
                'total_fees'   => $totalFees,
                'total_paid'   => $totalPaid,
                'due'          => $due,
            ];
        })
        ->sortBy(fn($r) => (int) $r->roll)
        ->values();

        return [$sessions, $classes, $sections, $groups, $rows];
    }
}
