<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Group;
use App\Models\Payment;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class DiscountListController extends Controller
{
    public function index(Request $request)
    {
        [$sessions, $classes, $sections, $groups, $rows] = $this->buildData($request);

        return view('pages.discount-list.index', compact(
            'sessions', 'classes', 'sections', 'groups', 'rows'
        ));
    }

    public function pdf(Request $request)
    {
        [, , , , $rows] = $this->buildData($request);

        $session = AcademicSession::find($request->session_id);
        $month   = $request->filled('month') ? date('F', mktime(0, 0, 0, $request->month, 1)) : 'All Months';

        $html = view('pages.discount-list.pdf', compact('rows', 'session', 'month'))->render();

        $mpdf = new Mpdf(['orientation' => 'L', 'margin_top' => 10, 'margin_bottom' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('discount-list.pdf', 'D');
    }

    private function buildData(Request $request): array
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes  = SchoolClass::orderBy('name_en')->get();
        $sections = $request->filled('class_id')
            ? Section::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();
        $groups = $request->filled('class_id')
            ? Group::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();

        $rows = collect();

        if (!$request->filled('session_id')) {
            return [$sessions, $classes, $sections, $groups, $rows];
        }

        $query = Payment::with([
                'student.academicInformations' => fn($q) => $q
                    ->where('academic_session_id', $request->session_id)
                    ->with(['schoolClass', 'section', 'group']),
            ])
            ->where(function($q) {
                $q->where('discount_amount', '>', 0)
                  ->orWhere('scholarship_amount', '>', 0);
            })
            ->whereHas('student.academicInformations', fn($q) =>
                $q->where('academic_session_id', $request->session_id)
                  ->when($request->filled('class_id'),   fn($q) => $q->where('school_class_id', $request->class_id))
                  ->when($request->filled('section_id'), fn($q) => $q->where('section_id', $request->section_id))
                  ->when($request->filled('group_id'),   fn($q) => $q->where('group_id', $request->group_id))
            )
            ->when($request->filled('month'), fn($q) =>
                $q->whereMonth('payment_date', $request->month)
            )
            ->orderBy('payment_date');

        $rows = $query->get()->map(function (Payment $p) {
            $ai = $p->student->academicInformations->first();
            return (object)[
                'receipt_no'    => $p->receipt_no,
                'payment_date'  => \Carbon\Carbon::parse($p->payment_date)->format('d M Y'),
                'cid'           => $p->student->student_cid,
                'name'          => $p->student->full_name_en,
                'class_name'    => $ai?->schoolClass?->name_en ?? '—',
                'section_name'  => $ai?->section?->name_en ?? '—',
                'group_name'    => $ai?->group?->name_en ?? '—',
                'gross_amount'  => (float) $p->gross_amount,
                'scholarship'   => (float) $p->scholarship_amount,
                'discount'      => (float) $p->discount_amount,
                'discount_type' => $p->discount_type,
                'paid'          => (float) $p->amount,
            ];
        });

        return [$sessions, $classes, $sections, $groups, $rows];
    }
}
