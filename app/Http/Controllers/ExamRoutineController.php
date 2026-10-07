<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\AdmitSeatCardSetting;
use App\Models\Exam;
use App\Models\ExamRoutine;
use App\Models\Group;
use App\Models\Holiday;
use App\Models\SchoolSetting;
use App\Models\SchoolClass;
use App\Models\SubjectClassAssignment;
use App\Models\WeekendSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class ExamRoutineController extends Controller
{
    private const EXAM_TYPES = ['terminal' => 'Terminal Exam', 'tutorial' => 'Tutorial Exam'];

    public function index(Request $request)
    {
        $sessionId = $request->integer('academic_session_id') ?: null;
        $examType = $request->input('exam_type');
        $examId = $request->integer('exam_id') ?: null;
        $classId = $request->integer('school_class_id') ?: null;
        $groupId = $request->integer('group_id') ?: null;
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes = SchoolClass::where('status', 1)->orderBy('order')->orderBy('id')->get();
        $examTypes = self::EXAM_TYPES;
        $exams = $sessionId && isset(self::EXAM_TYPES[$examType])
            ? Exam::where('academic_session_id', $sessionId)->where('exam_category', $examType)->orderByDesc('pair_no')->orderByDesc('id')->get()
            : collect();
        $groups = $sessionId && $classId ? $this->groupsFor($sessionId, $classId) : collect();
        $weekendDays = array_map('intval', WeekendSetting::current()->days());
        $holidayNames = $this->holidayNames();
        $holidayDates = array_keys($holidayNames);
        $subjects = collect();
        $routine = null;

        if ($sessionId && $examId && $classId) {
            $exam = $exams->firstWhere('id', $examId);
            if ($exam && (! $groupId || $groups->contains('id', $groupId))) {
                $subjects = $this->subjectsFor($classId, $groupId);
                $routineQuery = ExamRoutine::with('items.subject')->where('exam_id', $exam->id)->where('school_class_id', $classId);
                $routine = $groupId ? $routineQuery->where('group_id', $groupId)->first() : $routineQuery->whereNull('group_id')->first();
                if ($routine) {
                    $savedItems = $routine->items->keyBy('subject_id');
                    $subjects = $subjects->sortBy(fn ($subject) => [
                        $savedItems->get($subject->id)?->exam_date?->toDateString() ?? '9999-12-31',
                        $savedItems->get($subject->id)?->sort_order ?? PHP_INT_MAX,
                    ])->values();
                }
            }
        }

        return view('pages.results.exam-routines.index', compact('sessions', 'classes', 'examTypes', 'exams', 'groups', 'subjects', 'routine', 'sessionId', 'examType', 'examId', 'classId', 'groupId', 'weekendDays', 'holidayDates', 'holidayNames'));
    }

    public function print(Request $request)
    {
        return view('pages.results.exam-routines.print', $this->exportData($request));
    }

    public function pdf(Request $request)
    {
        $data = $this->exportData($request);
        $html = view('pages.results.exam-routines.pdf', $data)->render();
        $mpdf = new Mpdf([
            // Explicit portrait format prevents mPDF or printer defaults from
            // interpreting the routine as landscape.
            'format' => 'A4-P',
            'margin_top' => 10,
            'margin_right' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'img_dpi' => 150,
            'allow_charset_conversion' => false,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetTitle('Exam Routine - ' . $data['exam']->name);
        $mpdf->WriteHTML($html);

        $filename = Str::slug($data['exam']->name . '-' . $data['schoolClass']->name_en . '-exam-routine') . '.pdf';

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exams(Request $request)
    {
        $data = $request->validate(['academic_session_id' => ['required', 'exists:academic_sessions,id'], 'exam_type' => ['required', 'in:terminal,tutorial']]);
        return response()->json(['exams' => Exam::where('academic_session_id', $data['academic_session_id'])->where('exam_category', $data['exam_type'])->orderByDesc('pair_no')->orderByDesc('id')->get(['id', 'name', 'exam_category'])]);
    }

    public function groups(Request $request)
    {
        $data = $request->validate(['academic_session_id' => ['required', 'exists:academic_sessions,id'], 'school_class_id' => ['required', 'exists:school_classes,id']]);
        return response()->json(['groups' => $this->groupsFor($data['academic_session_id'], $data['school_class_id'])]);
    }

    public function store(Request $request)
    {
        $weekendDays = array_map('intval', WeekendSetting::current()->days());
        $holidayNames = $this->holidayNames();
        $holidayDates = array_keys($holidayNames);
        $timeAfter = function (string $startField, string $label) use ($request) {
            return function ($attribute, $value, $fail) use ($request, $startField, $label) {
                $start = $request->input($startField);
                if (! is_string($start) || ! is_string($value) || ! preg_match('/^\d{2}:\d{2}$/', $start) || ! preg_match('/^\d{2}:\d{2}$/', $value)) {
                    return;
                }

                try {
                    $startTime = Carbon::createFromFormat('H:i', $start);
                    $endTime = Carbon::createFromFormat('H:i', $value);
                } catch (\Throwable) {
                    return;
                }

                if ($endTime->lessThanOrEqualTo($startTime)) {
                    $fail("{$label} end time must be later than {$label} start time.");
                }
            };
        };
        $data = $request->validate([
            'academic_session_id' => ['required', 'exists:academic_sessions,id'], 'exam_type' => ['required', 'in:terminal,tutorial'],
            'exam_id' => ['required', 'integer', 'exists:exams,id'], 'school_class_id' => ['required', 'exists:school_classes,id'], 'group_id' => ['nullable', 'integer', 'exists:groups,id'],
            'morning_start_time' => ['required', 'date_format:H:i'], 'morning_end_time' => ['required', 'date_format:H:i', $timeAfter('morning_start_time', 'Morning')],
            'noon_start_time' => ['required', 'date_format:H:i'], 'noon_end_time' => ['required', 'date_format:H:i', $timeAfter('noon_start_time', 'Noon')],
            'items' => ['required', 'array', 'min:1'], 'items.*.subject_id' => ['required', 'integer', 'distinct'], 'items.*.slot' => ['required', 'in:morning,noon'],
            'items.*.exam_date' => ['required', 'date', function ($attribute, $value, $fail) use ($weekendDays, $holidayDates) {
                if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) return;
                try {
                    $date = Carbon::createFromFormat('Y-m-d', $value);
                } catch (\Throwable) {
                    return;
                }
                if (in_array($date->dayOfWeek, $weekendDays, true)) {
                    $fail('Exam dates cannot be scheduled on a configured off day.');
                } elseif (isset($holidayNames[$date->toDateString()])) {
                    $fail('Exam dates cannot be scheduled on the holiday: ' . $holidayNames[$date->toDateString()] . '.');
                }
            }],
        ]);
        $exam = Exam::whereKey($data['exam_id'])->where('academic_session_id', $data['academic_session_id'])->where('exam_category', $data['exam_type'])->firstOrFail();
        abort_unless(SchoolClass::whereKey($data['school_class_id'])->where('status', 1)->exists(), 422);
        $allowed = $this->subjectsFor($data['school_class_id'], $data['group_id'])->pluck('id');
        $submitted = collect($data['items'])->pluck('subject_id')->map(fn ($id) => (int) $id);
        abort_unless($submitted->diff($allowed)->isEmpty(), 422, 'One or more selected subjects are not assigned to this class and group.');
        abort_unless($submitted->count() === $allowed->count(), 422, 'Please include every subject in the routine.');

        DB::transaction(function () use ($data, $exam) {
            $routine = ExamRoutine::updateOrCreate(
                ['exam_id' => $exam->id, 'school_class_id' => $data['school_class_id'], 'group_id' => $data['group_id']],
                ['academic_session_id' => $data['academic_session_id'], 'morning_start_time' => $data['morning_start_time'], 'morning_end_time' => $data['morning_end_time'], 'noon_start_time' => $data['noon_start_time'], 'noon_end_time' => $data['noon_end_time']]
            );
            $routine->items()->delete();
            $routine->items()->createMany(collect($data['items'])->values()->map(fn ($item, $index) => ['subject_id' => $item['subject_id'], 'sort_order' => $index + 1, 'slot' => $item['slot'], 'exam_date' => $item['exam_date']])->all());
        });

        return redirect()->route('exam-routines.index', ['academic_session_id' => $data['academic_session_id'], 'exam_type' => $data['exam_type'], 'exam_id' => $data['exam_id'], 'school_class_id' => $data['school_class_id'], 'group_id' => $data['group_id']])->with('success', __('Exam routine saved successfully.'));
    }

    private function groupsFor(int $sessionId, int $classId)
    {
        return Group::where('status', 1)->orderBy('name_en')->get(['id', 'name_en', 'name_bn', 'status']);
    }

    private function holidayNames(): array
    {
        return Holiday::query()->get(['date', 'title'])->mapWithKeys(function (Holiday $holiday) {
            return [$holiday->date->toDateString() => trim((string) $holiday->title) ?: 'Holiday'];
        })->all();
    }

    private function subjectsFor(int $classId, ?int $groupId)
    {
        $assignments = SubjectClassAssignment::where('school_class_id', $classId)->where('is_active', true)
            ->when($groupId, fn ($query) => $query->where(fn ($groupQuery) => $groupQuery->whereNull('group_id')->orWhere('group_id', $groupId)))
            ->whereHas('subject', fn ($query) => $query->where('is_active', true))
            ->with(['subject' => fn ($query) => $query
                ->where('is_active', true)
                ->with(['papers' => fn ($paperQuery) => $paperQuery->where('is_active', true)])
            ])->get();
        $subjects = collect();
        foreach ($assignments as $assignment) {
            if (! $assignment->subject) continue;
            $subjects = $subjects->merge($assignment->subject->is_parent && $assignment->subject->papers->isNotEmpty() ? $assignment->subject->papers : collect([$assignment->subject]));
        }
        return $subjects->unique('id')->sortBy('name')->values();
    }

    private function exportData(Request $request): array
    {
        $data = $request->validate([
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'exam_type' => ['required', 'in:terminal,tutorial'],
            'exam_id' => ['required', 'integer', 'exists:exams,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'group_id' => ['nullable', 'integer', 'exists:groups,id'],
        ]);

        $exam = Exam::query()
            ->whereKey($data['exam_id'])
            ->where('academic_session_id', $data['academic_session_id'])
            ->where('exam_category', $data['exam_type'])
            ->firstOrFail();
        $academicSession = AcademicSession::findOrFail($data['academic_session_id']);
        $schoolClass = SchoolClass::whereKey($data['school_class_id'])->where('status', 1)->firstOrFail();
        $group = filled($data['group_id'] ?? null) ? Group::findOrFail($data['group_id']) : null;
        $routine = ExamRoutine::with(['items.subject', 'exam', 'academicSession', 'schoolClass', 'group'])
            ->where('academic_session_id', $academicSession->id)
            ->where('exam_id', $exam->id)
            ->where('school_class_id', $schoolClass->id)
            ->when($group, fn ($query) => $query->where('group_id', $group->id), fn ($query) => $query->whereNull('group_id'))
            ->firstOrFail();

        return [
            'routine' => $routine,
            'items' => $routine->items,
            'setting' => SchoolSetting::current(),
            'cardSettings' => AdmitSeatCardSetting::current(1),
            'academicSession' => $academicSession,
            'exam' => $exam,
            'schoolClass' => $schoolClass,
            'group' => $group,
        ];
    }
}
