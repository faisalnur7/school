<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\AttendanceItem;
use App\Models\AttendanceSetting;
use App\Models\Holiday;
use App\Models\SchoolClass;
use App\Models\StudentAcademicInformation;
use App\Models\WeekendSetting;
use App\Models\TeacherSectionAssignment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes = SchoolClass::where('status', 1)->orderBy('id')->get();
        $isOpenForAll = AttendanceSetting::current()->is_open_for_all;

        $defaultDate = $request->input('date', now()->toDateString());
        $selectedSessionId = $request->input('session_id');
        $selectedClassId = $request->input('school_class_id');
        $selectedSectionId = $request->input('section_id');

        return view('pages.attendance.index', compact(
            'sessions',
            'classes',
            'defaultDate',
            'isOpenForAll',
            'selectedSessionId',
            'selectedClassId',
            'selectedSectionId'
        ));
    }

    public function load(Request $request)
    {
        $rawDate = (string) $request->input('date', '');
        $normalizedDate = null;
        foreach (['Y-m-d', 'd/m/Y'] as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $rawDate);
                if ($parsed && $parsed->format($format) === $rawDate) {
                    $normalizedDate = $parsed->toDateString();
                    break;
                }
            } catch (\Throwable $e) {
                // Try next format.
            }
        }

        $validator = Validator::make(array_merge($request->all(), [
            'date' => $normalizedDate,
        ]), [
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'date' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $sessionId = (int) $request->session_id;
        $classId = (int) $request->class_id;
        $sectionId = (int) $request->section_id;
        $date = $normalizedDate;

        if ($message = $this->blockedAttendanceMessage($date)) {
            return response()->json(['message' => $message], 422);
        }

        $this->authorizeSection($sessionId, $classId, $sectionId);

        $academicInfos = StudentAcademicInformation::query()
            ->where('academic_session_id', $sessionId)
            ->where('school_class_id', $classId)
            ->where('section_id', $sectionId)
            ->with('student:id,full_name_en,full_name_bn')
            ->orderByRaw('CAST(roll AS UNSIGNED) ASC')
            ->orderBy('roll')
            ->get();

        $students = $academicInfos
            ->filter(fn ($info) => (bool) $info->student)
            ->values();

        $attendance = Attendance::query()
            ->where('session_id', $sessionId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->whereDate('date', $date)
            ->with('items:attendance_id,student_id,status')
            ->first();

        $statusByStudentId = $attendance
            ? $attendance->items->pluck('status', 'student_id')->all()
            : [];

        return view('pages.attendance._students_table', [
            'students' => $students,
            'sessionId' => $sessionId,
            'classId' => $classId,
            'sectionId' => $sectionId,
            'date' => $date,
            'attendance' => $attendance,
            'statusByStudentId' => $statusByStudentId,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'date' => ['required', 'date'],
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['integer', 'exists:students,id'],
            'present_ids' => ['nullable', 'array'],
            'present_ids.*' => ['integer', 'exists:students,id'],
        ]);

        $sessionId = (int) $data['session_id'];
        $classId = (int) $data['class_id'];
        $sectionId = (int) $data['section_id'];
        $date = $data['date'];

        if ($message = $this->blockedAttendanceMessage($date)) {
            return back()->with('error', $message);
        }

        $this->authorizeSection($sessionId, $classId, $sectionId);

        $allowedStudentIds = $this->allowedStudentIds($sessionId, $classId, $sectionId);
        $postedStudentIds = collect($data['student_ids'])->map(fn ($v) => (int) $v)->unique()->values();

        if ($postedStudentIds->diff($allowedStudentIds)->isNotEmpty()) {
            return back()->with('error', 'Invalid student list for the selected section.');
        }

        $presentIds = collect($data['present_ids'] ?? [])->map(fn ($v) => (int) $v)->unique();

        DB::transaction(function () use ($sessionId, $classId, $sectionId, $date, $postedStudentIds, $presentIds) {
            $attendance = Attendance::query()->firstOrNew([
                'session_id' => $sessionId,
                'class_id' => $classId,
                'section_id' => $sectionId,
                'date' => $date,
            ]);

            $attendance->taken_by = auth()->id();
            $attendance->save();

            $rows = $postedStudentIds->map(function (int $studentId) use ($attendance, $presentIds) {
                return [
                    'attendance_id' => $attendance->id,
                    'student_id' => $studentId,
                    'status' => $presentIds->contains($studentId) ? 'present' : 'absent',
                    'note' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->all();

            AttendanceItem::query()
                ->where('attendance_id', $attendance->id)
                ->delete();

            AttendanceItem::query()->insert($rows);
        });

        return back()->with('success', 'Attendance saved successfully.');
    }

    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'session_id' => ['required', 'integer'],
            'class_id' => ['required', 'integer'],
            'section_id' => ['required', 'integer'],
            'date' => ['required', 'date'],
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['integer', 'exists:students,id'],
            'present_ids' => ['nullable', 'array'],
            'present_ids.*' => ['integer', 'exists:students,id'],
        ]);

        $sessionId = (int) $data['session_id'];
        $classId = (int) $data['class_id'];
        $sectionId = (int) $data['section_id'];

        if ($message = $this->blockedAttendanceMessage($data['date'])) {
            return back()->with('error', $message);
        }

        if (
            $attendance->session_id !== $sessionId ||
            $attendance->class_id !== $classId ||
            $attendance->section_id !== $sectionId ||
            $attendance->date->toDateString() !== $data['date']
        ) {
            return back()->with('error', 'Attendance key mismatch.');
        }

        $this->authorizeSection($attendance->session_id, $attendance->class_id, $attendance->section_id);

        $allowedStudentIds = $this->allowedStudentIds($attendance->session_id, $attendance->class_id, $attendance->section_id);
        $postedStudentIds = collect($data['student_ids'])->map(fn ($v) => (int) $v)->unique()->values();

        if ($postedStudentIds->diff($allowedStudentIds)->isNotEmpty()) {
            return back()->with('error', 'Invalid student list for the selected section.');
        }

        $presentIds = collect($data['present_ids'] ?? [])->map(fn ($v) => (int) $v)->unique();

        DB::transaction(function () use ($attendance, $postedStudentIds, $presentIds) {
            $attendance->taken_by = auth()->id();
            $attendance->save();

            $rows = $postedStudentIds->map(function (int $studentId) use ($attendance, $presentIds) {
                return [
                    'attendance_id' => $attendance->id,
                    'student_id' => $studentId,
                    'status' => $presentIds->contains($studentId) ? 'present' : 'absent',
                    'note' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->all();

            AttendanceItem::query()
                ->where('attendance_id', $attendance->id)
                ->delete();

            AttendanceItem::query()->insert($rows);
        });

        return back()->with('success', 'Attendance updated successfully.');
    }

    private function authorizeSection(int $sessionId, int $classId, int $sectionId): void
    {
        if ($this->isOpenForAll()) {
            return;
        }

        $ok = TeacherSectionAssignment::query()
            ->where('user_id', auth()->id())
            ->where('session_id', $sessionId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->exists();

        abort_unless($ok, 403, 'You are not assigned as class teacher for this section.');
    }

    private function isOpenForAll(): bool
    {
        return AttendanceSetting::current()->is_open_for_all;
    }

    private function allowedStudentIds(int $sessionId, int $classId, int $sectionId)
    {
        return StudentAcademicInformation::query()
            ->where('academic_session_id', $sessionId)
            ->where('school_class_id', $classId)
            ->where('section_id', $sectionId)
            ->pluck('student_id')
            ->filter()
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values();
    }

    private function blockedAttendanceMessage(string $date): ?string
    {
        $attendanceDate = Carbon::parse($date);
        $blockedReasons = [];

        if (in_array($attendanceDate->dayOfWeek, WeekendSetting::current()->days(), true)) {
            $blockedReasons[] = 'weekends';
        }

        if (Holiday::query()->whereDate('date', $attendanceDate->toDateString())->exists()) {
            $blockedReasons[] = 'holidays';
        }

        if ($blockedReasons === []) {
            return null;
        }

        return 'Attendance cannot be taken on ' . implode(' or ', $blockedReasons) . '.';
    }
}
