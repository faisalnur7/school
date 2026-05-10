<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\AttendanceItem;
use App\Models\Holiday;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Section;
use App\Models\StudentAcademicInformation;
use App\Models\TeacherSectionAssignment;
use App\Models\WeekendSetting;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class MonthlyAttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes  = SchoolClass::where('status', 1)->orderBy('id')->get();
        $defaultMonth = now()->format('Y-m');

        return view('pages.attendance.monthly-report', compact('sessions', 'classes', 'defaultMonth'));
    }

    public function load(Request $request)
    {
        $request->validate([
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id'   => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'month'      => ['required', 'regex:/^\d{4}-\d{2}$/'],
        ]);

        $this->authorizeSection(
            (int) $request->session_id,
            (int) $request->class_id,
            (int) $request->section_id
        );

        $data = $this->buildReport(
            (int) $request->session_id,
            (int) $request->class_id,
            (int) $request->section_id,
            $request->month
        );

        return view('pages.attendance._monthly_report_table', $data);
    }

    public function pdf(Request $request)
    {
        $request->validate([
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id'   => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'month'      => ['required', 'regex:/^\d{4}-\d{2}$/'],
        ]);

        $sessionId = (int) $request->session_id;
        $classId   = (int) $request->class_id;
        $sectionId = (int) $request->section_id;
        $month     = $request->month;

        $this->authorizeSection($sessionId, $classId, $sectionId);

        $data = $this->buildReport($sessionId, $classId, $sectionId, $month);

        $data['school']   = SchoolSetting::current();
        $data['session']  = AcademicSession::find($sessionId);
        $data['class']    = SchoolClass::find($classId);
        $data['section']  = Section::find($sectionId);
        $data['monthLabel'] = Carbon::createFromFormat('Y-m', $month)->format('F Y');

        $html = view('pages.attendance.pdf.monthly-report', $data)->render();

        $mpdf = new \Mpdf\Mpdf([
            'orientation' => 'L',
            'margin_top'  => 8,
            'margin_bottom' => 8,
            'margin_left'   => 8,
            'margin_right'  => 8,
        ]);
        $mpdf->WriteHTML($html);
        $filename = 'attendance_' . $month . '.pdf';
        $mpdf->Output($filename, 'D');
        exit;
    }

    // ---------------------------------------------------------------
    // Shared report builder
    // ---------------------------------------------------------------
    private function buildReport(int $sessionId, int $classId, int $sectionId, string $month): array
    {
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $allDates = collect(CarbonPeriod::create($start, $end))->map(fn ($d) => $d->copy());

        $weekendDays  = WeekendSetting::current()->days();
        $holidayDates = Holiday::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('date')
            ->map(fn ($d) => $d->toDateString())
            ->flip();

        $nonWorkingDates = $allDates
            ->filter(fn ($d) => in_array($d->dayOfWeek, $weekendDays) || isset($holidayDates[$d->toDateString()]))
            ->map(fn ($d) => $d->toDateString())
            ->flip();

        $workingDaysCount = $allDates->filter(fn ($d) => !isset($nonWorkingDates[$d->toDateString()]))->count();

        $academicInfos = StudentAcademicInformation::query()
            ->where('academic_session_id', $sessionId)
            ->where('school_class_id', $classId)
            ->where('section_id', $sectionId)
            ->with('student:id,full_name_en,student_cid')
            ->orderByRaw('CAST(roll AS UNSIGNED) ASC')
            ->orderBy('roll')
            ->get()
            ->filter(fn ($i) => (bool) $i->student)
            ->values();

        $attendances = Attendance::query()
            ->where('session_id', $sessionId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('id', 'date');

        $attendanceIdByDate = $attendances->mapWithKeys(
            fn ($id, $date) => [Carbon::parse($date)->toDateString() => $id]
        );

        $items = AttendanceItem::query()
            ->whereIn('attendance_id', $attendances->values())
            ->get(['attendance_id', 'student_id', 'status']);

        $statusMap = [];
        foreach ($items as $item) {
            $statusMap[$item->attendance_id][$item->student_id] = $item->status;
        }

        $rows = $academicInfos->map(function ($info) use ($allDates, $nonWorkingDates, $attendanceIdByDate, $statusMap, $workingDaysCount) {
            $student = $info->student;
            $cells   = [];
            $presentCount = 0;
            $absentCount  = 0;

            foreach ($allDates as $date) {
                $dateStr = $date->toDateString();
                if (isset($nonWorkingDates[$dateStr])) {
                    $cells[$dateStr] = '-';
                    continue;
                }
                $attId  = $attendanceIdByDate[$dateStr] ?? null;
                $status = $attId ? ($statusMap[$attId][$student->id] ?? 'absent') : 'absent';
                $cells[$dateStr] = $status === 'present' ? 'P' : 'A';
                $status === 'present' ? $presentCount++ : $absentCount++;
            }

            $percentage = $workingDaysCount > 0
                ? round(($presentCount / $workingDaysCount) * 100, 1)
                : 0;

            return [
                'roll'        => $info->roll,
                'student_id'  => $student->id,
                'student_cid' => $student->student_cid ?? $student->id,
                'name'        => $student->full_name_en,
                'cells'       => $cells,
                'present'     => $presentCount,
                'absent'      => $absentCount,
                'percentage'  => $percentage,
            ];
        });

        return compact('allDates', 'nonWorkingDates', 'workingDaysCount', 'rows');
    }

    private function authorizeSection(int $sessionId, int $classId, int $sectionId): void
    {
        $ok = TeacherSectionAssignment::query()
            ->where('user_id', auth()->id())
            ->where('session_id', $sessionId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->exists();

        abort_unless($ok, 403, 'You are not assigned as class teacher for this section.');
    }
}
