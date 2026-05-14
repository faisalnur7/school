<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\SchoolClass;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class StudentReceivableReportController extends Controller
{
    public function index(Request $request)
    {
        [$sessions, $classes, $sections, $rows, $months, $categories, $totals, $fromDate, $toDate] = $this->buildData($request);

        return view('pages.student-receivable-report.index', compact(
            'sessions', 'classes', 'sections', 'rows', 'months', 'categories', 'totals', 'fromDate', 'toDate'
        ));
    }

    public function pdf(Request $request)
    {
        [, , , $rows, $months, $categories, $totals, $fromDate, $toDate] = $this->buildData($request);

        $html = view('pages.student-receivable-report.pdf', compact(
            'rows', 'months', 'categories', 'totals', 'fromDate', 'toDate'
        ))->render();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 'margin_top' => 10, 'margin_bottom' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('student-receivable-report.pdf', 'D');
    }

    private function buildData(Request $request): array
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes  = SchoolClass::orderBy('name_en')->get();
        $sections = $request->filled('class_id')
            ? Section::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();

        $rows       = collect();
        $months     = [];
        $categories = collect();
        $totals     = ['months' => [], 'categories' => [], 'total' => 0.0];
        $fromDate   = $request->filled('from_date') ? Carbon::parse($request->from_date) : null;
        $toDate     = $request->filled('to_date')   ? Carbon::parse($request->to_date)   : null;

        if (!$fromDate || !$toDate) {
            return [$sessions, $classes, $sections, $rows, $months, $categories, $totals,
                $fromDate?->toDateString(), $toDate?->toDateString()];
        }

        if ($toDate->lt($fromDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        // Build month columns
        $current = $fromDate->copy()->startOfMonth();
        $end     = $toDate->copy()->startOfMonth();
        while ($current->lte($end)) {
            $key = $current->format('Y-m');
            $months[$key] = $current->format('M-y');
            $totals['months'][$key] = 0.0;
            $current->addMonth();
        }

        if (empty($months)) {
            return [$sessions, $classes, $sections, $rows, $months, $categories, $totals,
                $fromDate->toDateString(), $toDate->toDateString()];
        }

        // Query active fees with due_date in range
        $feesQuery = Fee::with([
                'student.academicInformations.schoolClass',
                'student.academicInformations.section',
                'feeSet.items.category',
            ])
            ->where('is_active', 1)
            ->whereBetween('due_date', [$fromDate->toDateString(), $toDate->toDateString()]);

        if ($request->filled('session_id')) {
            $feesQuery->whereHas('feeSet', fn($q) => $q->where('academic_session_id', $request->session_id));
        }

        $fees = $feesQuery->get();

        // Collect all categories used
        $allCategories = collect();
        foreach ($fees as $fee) {
            foreach ($fee->feeSet->items as $item) {
                if ($item->category && $item->category->status) {
                    $allCategories->put($item->category->id, $item->category);
                }
            }
        }
        $categories = $allCategories->sortBy('name');

        // Init category totals
        foreach ($categories as $cat) {
            $totals['categories'][$cat->id] = array_fill_keys(array_keys($months), 0.0);
        }

        // Pre-load all academic info counts per student to determine new vs old
        $studentAcademicCounts = \App\Models\StudentAcademicInformation::selectRaw('student_id, COUNT(*) as total')
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        // Build student map: [studentId => [monthKey => [catId => amount]]]
        $studentMap = [];

        foreach ($fees as $fee) {
            $student = $fee->student;
            if (!$student) continue;

            $academicInfo = $student->academicInformations
                ->when($request->filled('session_id'), fn($c) => $c->where('academic_session_id', $request->session_id))
                ->first() ?? $student->academicInformations->first();

            if ($request->filled('session_id') && !$academicInfo) continue;
            if ($request->filled('class_id') && $academicInfo?->school_class_id != $request->class_id) continue;
            if ($request->filled('section_id') && $academicInfo?->section_id != $request->section_id) continue;

            $monthKey = Carbon::parse($fee->due_date)->format('Y-m');
            if (!isset($months[$monthKey])) continue;

            $feeSetItems = $fee->feeSet->items;
            if ($feeSetItems->isEmpty()) continue;

            $sid = $student->id;
            $isNewStudent = ($studentAcademicCounts[$sid] ?? 1) <= 1;

            if (!isset($studentMap[$sid])) {
                $studentMap[$sid] = [
                    'student_id'    => $sid,
                    'student_cid'   => $student->student_cid,
                    'student_name'  => $student->full_name_en,
                    'class_name'    => $academicInfo?->schoolClass?->name_en ?? '—',
                    'section_name'  => $academicInfo?->section?->name_en ?? '—',
                    'is_new'        => $isNewStudent,
                    'months'        => array_fill_keys(array_keys($months), 0.0),
                    'categories'    => [],
                    'total'         => 0.0,
                ];
            }

            foreach ($feeSetItems as $item) {
                $cat = $item->category;
                if (!$cat || !$cat->status) continue;

                // Filter by student_type: new students skip 'old'-only categories and vice versa
                $studentType = $cat->student_type ?? 'both';
                if ($isNewStudent && $studentType === 'old') continue;
                if (!$isNewStudent && $studentType === 'new') continue;

                $catId  = $cat->id;
                // Use the fee set item amount directly — fee.amount is already the
                // student-type-adjusted sum, so proportional splitting gives wrong values
                // when new/old-only categories are excluded from the total.
                $amount = (float) $item->amount;
                if ($amount <= 0) continue;

                // Apply scholarship discount proportionally across applicable items
                if ($fee->scholarship_discount > 0) {
                    $applicableTotal = $feeSetItems->filter(function ($i) use ($isNewStudent) {
                        $t = $i->category->student_type ?? 'both';
                        if ($isNewStudent && $t === 'old') return false;
                        if (!$isNewStudent && $t === 'new') return false;
                        return true;
                    })->sum('amount');
                    if ($applicableTotal > 0) {
                        $amount -= (float) $fee->scholarship_discount * ($item->amount / $applicableTotal);
                    }
                }

                if (!isset($studentMap[$sid]['categories'][$catId])) {
                    $studentMap[$sid]['categories'][$catId] = array_fill_keys(array_keys($months), 0.0);
                }

                $studentMap[$sid]['categories'][$catId][$monthKey]  += $amount;
                $studentMap[$sid]['months'][$monthKey]               += $amount;
                $studentMap[$sid]['total']                           += $amount;
                $totals['categories'][$catId][$monthKey]             += $amount;
                $totals['months'][$monthKey]                         += $amount;
                $totals['total']                                     += $amount;
            }
        }

        $rows = collect(array_values($studentMap))
            ->map(fn($s) => (object) $s)
            ->sortBy('student_name')
            ->values();

        return [$sessions, $classes, $sections, $rows, $months, $categories, $totals,
            $fromDate->toDateString(), $toDate->toDateString()];
    }
}
