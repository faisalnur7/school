<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\Section;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentDueSummaryService
{
    public function build(Request $request): array
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes  = SchoolClass::get();
        $sections = $request->filled('class_id')
            ? Section::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();

        $rows   = collect();
        $totals = ['amount' => 0.0, 'paid' => 0.0, 'due' => 0.0];

        if (!$request->filled('session_id')) {
            return [$sessions, $classes, $sections, $rows, $totals];
        }

        $students = Student::query()
            ->with([
                'academicInformations' => fn($q) => $q
                    ->where('academic_session_id', $request->session_id)
                    ->with(['schoolClass', 'section']),
                'fees' => fn($q) => $q
                    ->whereHas('feeSet', fn($fs) => $fs->where('academic_session_id', $request->session_id))
                    ->where('is_active', 1)
                    ->with('feeSet'),
            ])
            ->whereHas('academicInformations', fn($q) =>
                $q->where('academic_session_id', $request->session_id)
                  ->when($request->filled('class_id'), fn($q) => $q->where('school_class_id', $request->class_id))
                  ->when($request->filled('section_id'), fn($q) => $q->where('section_id', $request->section_id))
            )
            ->get();

        foreach ($students as $student) {
            $academicInfo = $student->academicInformations->first();

            $lines = $student->fees
                ->groupBy('fee_set_id')
                ->map(function ($group) {
                    $amount = $group->sum(fn($f) => (float) $f->amount - (float) $f->scholarship_discount);
                    $paid   = $group->sum(fn($f) => (float) $f->paid_amount);
                    return (object)[
                        'description' => $group->first()->feeSet?->name ?? '—',
                        'amount'      => $amount,
                        'paid'        => $paid,
                        'due'         => max(0, $amount - $paid),
                    ];
                })->values();

            $rows->push((object)[
                'student_id'   => $student->id,
                'cid'          => $student->student_cid,
                'name'         => $student->full_name_en,
                'class_name'   => $academicInfo?->schoolClass?->name_en ?? '—',
                'section_name' => $academicInfo?->section?->name_en ?? '—',
                'lines'        => $lines,
                'fees_total'   => $lines->sum('amount'),
                'paid_amount'  => $lines->sum('paid'),
                'due'          => $lines->sum('due'),
            ]);
        }

        $rows = $rows->sortBy('name')->values();

        $totals['amount'] = $rows->sum('fees_total');
        $totals['paid']   = $rows->sum('paid_amount');
        $totals['due']    = $rows->sum('due');

        return [$sessions, $classes, $sections, $rows, $totals];
    }
}
