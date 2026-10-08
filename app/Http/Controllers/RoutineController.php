<?php

namespace App\Http\Controllers;

use App\Models\ClassRoutine;
use App\Models\ClassSchedule;
use App\Models\Classroom;
use App\Models\AcademicSession;
use App\Models\Employee;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\SubjectClassAssignment;
use App\Models\WeekendSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mpdf\Mpdf;

class RoutineController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassRoutine::with(['academicSession', 'schoolClass', 'section', 'subject', 'teacher', 'classroom', 'timeSchedule']);
        $days = $this->workingDays();
        $viewType = match ($request->string('view')->toString()) {
            'timetable', 'teacherwise', 'playground', 'teacherwise-playground' => $request->string('view')->toString(),
            default => 'list',
        };
        $teacherwisePlayground = $viewType === 'teacherwise-playground';
        $showAllClassSections = in_array($viewType, ['timetable', 'teacherwise', 'teacherwise-playground'], true)
            && (! $request->filled('school_class_id') || ! $request->filled('section_id'));
        $activeAcademicSessionId = AcademicSession::where('status', 1)->latest('id')->value('id')
            ?? AcademicSession::latest('id')->value('id');
        $selectedAcademicSessionId = $request->filled('academic_session_id')
            ? $request->integer('academic_session_id')
            : ($showAllClassSections ? $activeAcademicSessionId : null);

        if ($viewType !== 'playground' && $request->filled('search')) {
            $search = $request->search;

            $query->where(function ($builder) use ($search) {
                $builder->whereHas('schoolClass', function ($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_bn', 'like', "%{$search}%");
                })->orWhereHas('section', function ($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_bn', 'like', "%{$search}%");
                })->orWhereHas('subject', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })->orWhereHas('teacher', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('classroom', function ($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_bn', 'like', "%{$search}%");
                });
            });
        }

        if (! $teacherwisePlayground && $request->filled('school_class_id')) {
            $query->where('school_class_id', $request->integer('school_class_id'));
        }

        if (! $teacherwisePlayground && $request->filled('section_id')) {
            $query->where('section_id', $request->integer('section_id'));
        }

        if ($selectedAcademicSessionId) {
            $query->where('academic_session_id', $selectedAcademicSessionId);
        }

        if (! in_array($viewType, ['playground', 'teacherwise-playground'], true) && $request->filled('day')) {
            $query->where('day', $request->day);
        }

        $query->orderBy('school_class_id')
            ->orderBy('section_id')
            ->orderByRaw("FIELD(day, '" . implode("','", $days) . "')")
            ->orderBy('start_time');

        $periods = ClassSchedule::where('kind', 'teaching')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get();

        $hasPlaygroundContext = $request->filled('academic_session_id')
            && $request->filled('school_class_id')
            && $request->filled('section_id');

        $routines = $viewType === 'playground' && ! $hasPlaygroundContext
            ? collect()
            : ($viewType === 'list'
                ? $query->paginate(20)->withQueryString()
                : $query->get());

        $classes = SchoolClass::where('status', 1)->orderBy('id')->get();
        // Sections are loaded asynchronously after the class filter changes.
        // This keeps the routine page from rendering every section up front.
        $sections = collect();
        $academicSessions = AcademicSession::orderByDesc('id')->get();
        $playgroundSubjects = collect();
        $playgroundTeachers = collect();
        $playgroundClassrooms = collect();
        $playgroundRoutineMap = [];
        $teacherwiseRows = [];
        $teacherwisePlaygroundRows = [];
        $teacherwisePalette = collect();
        $teacherwiseSections = collect();

        if ($viewType === 'teacherwise') {
            $teacherwiseRows = $this->buildTeacherwiseRows($routines);
        }
        if ($viewType === 'teacherwise-playground') {
            $teacherwisePlaygroundRows = $this->buildTeacherwisePlaygroundRows($routines);
            foreach (Employee::active()->where('employee_type', 'teacher')->orderBy('name')->get(['id', 'name']) as $teacher) {
                $teacherwisePlaygroundRows[(string) $teacher->id] ??= [
                    'teacher' => $teacher->name,
                    'teacher_id' => $teacher->id,
                    'periods' => [],
                ];
            }
            uasort($teacherwisePlaygroundRows, fn ($left, $right) => strnatcasecmp($left['teacher'], $right['teacher']));
            $teacherwisePalette = SubjectClassAssignment::query()
                ->where('is_active', true)
                ->with(['subject', 'schoolClass'])
                ->get()
                ->filter(fn ($assignment) => $assignment->subject?->is_active && $assignment->schoolClass?->status)
                ->groupBy('school_class_id');
            $teacherwiseSections = Section::orderBy('school_class_id')->orderBy('name_en')->get();
        }

        if ($viewType === 'playground' && $hasPlaygroundContext) {
            $playgroundSubjects = SubjectClassAssignment::query()
                ->where('school_class_id', $request->integer('school_class_id'))
                ->where('is_active', true)
                ->with('subject')
                ->get()
                ->pluck('subject')
                ->filter(fn ($subject) => $subject && $subject->is_active)
                ->unique('id')
                ->sortBy('name')
                ->values();

            $playgroundTeachers = Employee::active()
                ->where('employee_type', 'teacher')
                ->orderBy('name')
                ->get(['id', 'name']);

            $playgroundClassrooms = Classroom::orderBy('name_en')->get(['id', 'name_en', 'name_bn']);

            foreach ($routines as $routine) {
                if ($routine->time_schedule_id) {
                    $playgroundRoutineMap[$routine->day . ':id:' . $routine->time_schedule_id] = $routine;
                }

                $playgroundRoutineMap[$routine->day . ':start:' . $routine->start_time] = $routine;
            }
        }

        return view('pages.routines.index', compact(
            'routines',
            'classes',
            'sections',
            'academicSessions',
            'selectedAcademicSessionId',
            'days',
            'periods',
            'viewType',
            'playgroundSubjects',
            'playgroundTeachers',
            'playgroundClassrooms',
            'playgroundRoutineMap',
            'teacherwiseRows',
            'teacherwisePlaygroundRows',
            'teacherwisePalette',
            'teacherwiseSections'
        ));
    }

    public function saveTeacherwisePlayground(Request $request)
    {
        $routineDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
        $data = $request->validate([
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'items' => ['present', 'array'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.subject_id' => ['required', 'exists:subjects,id'],
            'items.*.school_class_id' => ['required', 'exists:school_classes,id'],
            'items.*.section_id' => ['required', 'exists:sections,id'],
            'items.*.day' => ['required', 'string', Rule::in($routineDays)],
            'items.*.teacher_id' => [
                'nullable',
                Rule::exists('employees', 'id')->where(fn ($query) => $query->where('status', 'active')->where('employee_type', 'teacher')),
            ],
            'items.*.time_schedule_id' => [
                'required',
                Rule::exists('class_schedules', 'id')->where(fn ($query) => $query->where('kind', 'teaching')->where('is_active', true)),
            ],
        ]);

        $sessionId = (int) $data['academic_session_id'];
        $items = collect($data['items']);
        $submittedIds = $items->pluck('id')->filter()->map(fn ($id) => (int) $id);
        $routines = ClassRoutine::where('academic_session_id', $sessionId)
            ->whereIn('id', $submittedIds->unique())
            ->get()
            ->keyBy('id');
        $allSessionRoutineIds = ClassRoutine::where('academic_session_id', $sessionId)->pluck('id')->all();

        abort_unless($routines->count() === $submittedIds->unique()->count(), 422, 'The teacherwise draft contains an invalid routine. Please reload the page.');

        $schedules = ClassSchedule::whereIn('id', $items->pluck('time_schedule_id')->unique())->get()->keyBy('id');
        $changes = [];
        foreach ($items as $item) {
            $routine = ! empty($item['id']) ? $routines->get((int) $item['id']) : null;
            $schedule = $schedules->get((int) $item['time_schedule_id']);
            abort_unless($schedule, 422, 'The selected period is invalid.');

            $schoolClass = SchoolClass::whereKey($item['school_class_id'])->where('status', 1)->first();
            abort_unless($schoolClass, 422, 'The selected class is invalid.');
            $section = Section::whereKey($item['section_id'])->where('school_class_id', $schoolClass->id)->first();
            abort_unless($section, 422, 'The selected section does not belong to the class.');

            if (! $routine) {
                $assignmentExists = SubjectClassAssignment::where('school_class_id', $schoolClass->id)
                    ->where('subject_id', $item['subject_id'])
                    ->where('is_active', true)
                    ->exists();
                abort_unless($assignmentExists, 422, 'The selected subject is not active for this class.');
            }

            $teacherId = filled($item['teacher_id'] ?? null) ? (int) $item['teacher_id'] : null;
            $schoolClassId = $routine?->school_class_id ?? (int) $item['school_class_id'];
            $sectionId = $routine?->section_id ?? (int) $item['section_id'];
            $subjectId = $routine?->subject_id ?? (int) $item['subject_id'];
            $day = $routine?->day ?? $item['day'];

            $changes[] = [
                'routine' => $routine,
                'academic_session_id' => $sessionId,
                'teacher_id' => $teacherId,
                'subject_id' => $subjectId,
                'school_class_id' => $schoolClassId,
                'section_id' => $sectionId,
                'day' => $day,
                'time_schedule_id' => $schedule->id,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
            ];
        }

        foreach ($changes as $change) {
            $routine = $change['routine'];
            $conflictData = [
                'academic_session_id' => $sessionId,
                'school_class_id' => $change['school_class_id'],
                'section_id' => $change['section_id'],
                'teacher_id' => $change['teacher_id'],
                'classroom_id' => $routine?->classroom_id,
                'day' => $change['day'],
                'start_time' => $change['start_time'],
                'end_time' => $change['end_time'],
            ];

            $this->ensureNoScheduleConflict($conflictData, $allSessionRoutineIds);
        }

        $seenSections = [];
        $seenTeachers = [];
        $seenClassrooms = [];
        foreach ($changes as $change) {
            $slot = $change['day'] . ':' . $change['start_time'] . ':' . $change['end_time'];
            $sectionKey = $change['school_class_id'] . ':' . $change['section_id'] . ':' . $slot;
            abort_if(isset($seenSections[$sectionKey]), 422, 'A section can have only one routine in the same period and day.');
            $seenSections[$sectionKey] = true;

            if ($change['teacher_id']) {
                $teacherKey = $change['teacher_id'] . ':' . $slot;
                abort_if(isset($seenTeachers[$teacherKey]), 422, 'This teacher has more than one routine in the same period and day.');
                $seenTeachers[$teacherKey] = true;
            }

            if ($change['routine']?->classroom_id) {
                $classroomKey = $change['routine']->classroom_id . ':' . $slot;
                abort_if(isset($seenClassrooms[$classroomKey]), 422, 'This classroom has more than one routine in the same period and day.');
                $seenClassrooms[$classroomKey] = true;
            }
        }

        $deletedIds = ClassRoutine::where('academic_session_id', $sessionId)
            ->when($submittedIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $submittedIds->all()))
            ->pluck('id');

        DB::transaction(function () use ($changes, $deletedIds) {
            if ($deletedIds->isNotEmpty()) {
                ClassRoutine::whereIn('id', $deletedIds)->delete();
            }

            foreach ($changes as $change) {
                if ($change['routine']) {
                    $change['routine']->update([
                        'teacher_id' => $change['teacher_id'],
                        'time_schedule_id' => $change['time_schedule_id'],
                        'start_time' => $change['start_time'],
                        'end_time' => $change['end_time'],
                    ]);
                    continue;
                }

                ClassRoutine::create([
                    'academic_session_id' => $change['academic_session_id'] ?? null,
                    'school_class_id' => $change['school_class_id'],
                    'section_id' => $change['section_id'],
                    'subject_id' => $change['subject_id'],
                    'teacher_id' => $change['teacher_id'],
                    'day' => $change['day'],
                    'time_schedule_id' => $change['time_schedule_id'],
                    'start_time' => $change['start_time'],
                    'end_time' => $change['end_time'],
                ]);
            }
        });

        return response()->json(['message' => 'Teacherwise routine saved successfully.']);
    }

    private function buildTeacherwiseRows($routines): array
    {
        $dayNumbers = [
            'Saturday' => 1,
            'Sunday' => 2,
            'Monday' => 3,
            'Tuesday' => 4,
            'Wednesday' => 5,
            'Thursday' => 6,
        ];
        $dayNames = [
            1 => 'Sat',
            2 => 'Sun',
            3 => 'Mon',
            4 => 'Tue',
            5 => 'Wed',
            6 => 'Thu',
        ];
        $rows = [];

        foreach ($routines as $routine) {
            $teacherKey = (string) ($routine->teacher_id ?? 'unassigned');
            $periodKey = $routine->time_schedule_id
                ? 'id:' . $routine->time_schedule_id
                : 'start:' . $routine->start_time;
            $lessonKey = implode(':', [
                $routine->subject_id,
                $routine->school_class_id,
                $routine->section_id,
            ]);

            $rows[$teacherKey]['teacher'] = $routine->teacher?->name ?? 'Teacher not assigned';
            $rows[$teacherKey]['class_ids'][$routine->school_class_id] = true;
            $rows[$teacherKey]['periods'][$periodKey][$lessonKey] ??= [
                'routine' => $routine,
                'days' => [],
            ];

            if (isset($dayNumbers[$routine->day])) {
                $rows[$teacherKey]['periods'][$periodKey][$lessonKey]['days'][] = $dayNumbers[$routine->day];
            }
        }

        foreach ($rows as &$row) {
            $row['class_count'] = count($row['class_ids'] ?? []);
            unset($row['class_ids']);
            foreach ($row['periods'] as &$lessons) {
                foreach ($lessons as &$lesson) {
                    $numbers = array_values(array_unique($lesson['days']));
                    sort($numbers);
                    $ranges = [];
                    $start = $previous = $numbers[0] ?? null;

                    foreach (array_slice($numbers, 1) as $number) {
                        if ($number === $previous + 1) {
                            $previous = $number;
                            continue;
                        }

                        $ranges[] = $start === $previous
                            ? $dayNames[$start]
                            : $dayNames[$start] . '–' . $dayNames[$previous];
                        $start = $previous = $number;
                    }

                    if ($start !== null) {
                        $ranges[] = $start === $previous
                            ? $dayNames[$start]
                            : $dayNames[$start] . '–' . $dayNames[$previous];
                    }

                    $lesson['day_range'] = implode(', ', $ranges);
                }
                unset($lesson);
            }
            unset($lessons);
        }
        unset($row);

        uasort($rows, fn ($left, $right) => strnatcasecmp($left['teacher'], $right['teacher']));

        return $rows;
    }

    private function buildTeacherwisePlaygroundRows($routines): array
    {
        $rows = [];

        foreach ($routines as $routine) {
            $teacherKey = (string) ($routine->teacher_id ?? 'unassigned');
            $periodKey = $routine->time_schedule_id
                ? 'id:' . $routine->time_schedule_id
                : 'start:' . $routine->start_time;

            $rows[$teacherKey]['teacher'] = $routine->teacher?->name ?? 'Teacher not assigned';
            $rows[$teacherKey]['teacher_id'] = $routine->teacher_id;
            $rows[$teacherKey]['periods'][$periodKey][] = $routine;
        }

        uasort($rows, fn ($left, $right) => strnatcasecmp($left['teacher'], $right['teacher']));

        return $rows;
    }

    public function savePlayground(Request $request)
    {
        $data = $request->validate([
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => [
                'required',
                Rule::exists('sections', 'id')->where(fn ($query) => $query->where('school_class_id', $request->integer('school_class_id'))),
            ],
            'items' => ['present', 'array'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.subject_id' => ['required', 'exists:subjects,id'],
            'items.*.teacher_id' => ['nullable', 'exists:employees,id'],
            'items.*.classroom_id' => ['nullable', 'exists:classrooms,id'],
            'items.*.day' => ['required', 'string', Rule::in($this->workingDays())],
            'items.*.time_schedule_id' => [
                'required',
                Rule::exists('class_schedules', 'id')->where(fn ($query) => $query->where('kind', 'teaching')->where('is_active', true)),
            ],
        ]);

        $context = [
            'academic_session_id' => (int) $data['academic_session_id'],
            'school_class_id' => (int) $data['school_class_id'],
            'section_id' => (int) $data['section_id'],
        ];

        $subjectIds = SubjectClassAssignment::query()
            ->where('school_class_id', $context['school_class_id'])
            ->where('is_active', true)
            ->pluck('subject_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $teacherIds = Employee::active()->where('employee_type', 'teacher')->pluck('id')->map(fn ($id) => (int) $id)->all();
        $classroomIds = Classroom::query()->pluck('id')->map(fn ($id) => (int) $id)->all();
        $schedules = ClassSchedule::query()
            ->where('kind', 'teaching')
            ->where('is_active', true)
            ->whereIn('id', collect($data['items'])->pluck('time_schedule_id')->unique())
            ->get()
            ->keyBy('id');

        $currentRoutines = ClassRoutine::query()
            ->where($context)
            ->get();
        $currentIds = $currentRoutines->pluck('id')->map(fn ($id) => (int) $id)->all();
        $submittedIds = collect($data['items'])->pluck('id')->filter()->map(fn ($id) => (int) $id)->all();

        abort_if(array_diff($submittedIds, $currentIds), 422, 'The routine draft contains an invalid record. Please reload the playground.');
        abort_if(count($submittedIds) !== count(array_unique($submittedIds)), 422, 'The routine draft contains a duplicate record. Please reload the playground.');

        $seenSlots = [];
        $seenTeachers = [];
        $seenClassrooms = [];
        $normalized = [];

        foreach ($data['items'] as $index => $item) {
            $subjectId = (int) $item['subject_id'];
            $teacherId = filled($item['teacher_id'] ?? null) ? (int) $item['teacher_id'] : null;
            $classroomId = filled($item['classroom_id'] ?? null) ? (int) $item['classroom_id'] : null;
            $schedule = $schedules->get((int) $item['time_schedule_id']);

            abort_unless(in_array($subjectId, $subjectIds, true), 422, "Subject at item {$index} is not assigned to this class.");
            abort_unless($teacherId === null || in_array($teacherId, $teacherIds, true), 422, "Teacher at item {$index} is not an active teacher.");
            abort_unless($classroomId === null || in_array($classroomId, $classroomIds, true), 422, "Classroom at item {$index} is invalid.");
            abort_unless($schedule, 422, "Period at item {$index} is invalid.");

            $slotKey = $item['day'] . ':' . $schedule->id;
            abort_if(isset($seenSlots[$slotKey]), 422, 'A section can have only one routine in the same period.');
            $seenSlots[$slotKey] = true;

            if ($teacherId !== null) {
                $teacherKey = $item['day'] . ':' . $schedule->id . ':' . $teacherId;
                abort_if(isset($seenTeachers[$teacherKey]), 422, 'A teacher cannot teach two classes in the same period.');
                $seenTeachers[$teacherKey] = true;
            }

            if ($classroomId !== null) {
                $roomKey = $item['day'] . ':' . $schedule->id . ':' . $classroomId;
                abort_if(isset($seenClassrooms[$roomKey]), 422, 'A classroom cannot be used twice in the same period.');
                $seenClassrooms[$roomKey] = true;
            }

            $normalized[] = [
                'id' => filled($item['id'] ?? null) ? (int) $item['id'] : null,
                'academic_session_id' => $context['academic_session_id'],
                'school_class_id' => $context['school_class_id'],
                'section_id' => $context['section_id'],
                'subject_id' => $subjectId,
                'teacher_id' => $teacherId,
                'classroom_id' => $classroomId,
                'day' => $item['day'],
                'time_schedule_id' => $schedule->id,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
            ];
        }

        foreach ($normalized as $item) {
            $conflictQuery = ClassRoutine::query()
                ->where('academic_session_id', $context['academic_session_id'])
                ->where('day', $item['day'])
                ->where('start_time', '<', $item['end_time'])
                ->where('end_time', '>', $item['start_time']);

            if ($currentIds) {
                $conflictQuery->whereNotIn('id', $currentIds);
            }

            if ($item['teacher_id'] !== null) {
                $teacherConflict = (clone $conflictQuery)
                    ->where('teacher_id', $item['teacher_id'])
                    ->with(['schoolClass', 'section', 'timeSchedule'])
                    ->first();

                if ($teacherConflict) {
                    $teacher = Employee::find($item['teacher_id']);
                    $location = trim(($teacherConflict->schoolClass?->name_en ?? 'Another class') . ' ' . ($teacherConflict->section?->name_en ?? ''));
                    $period = $teacherConflict->timeSchedule?->name ?? $item['time_schedule_id'];
                    abort(422, sprintf(
                        '%s is already assigned to %s on %s, %s.',
                        $teacher?->name ?? 'This teacher',
                        $location,
                        $item['day'],
                        $period
                    ));
                }
            }

            if ($item['classroom_id'] !== null) {
                $classroomConflict = (clone $conflictQuery)
                    ->where('classroom_id', $item['classroom_id'])
                    ->with('timeSchedule')
                    ->first();

                if ($classroomConflict) {
                    $room = Classroom::find($item['classroom_id']);
                    $period = $classroomConflict->timeSchedule?->name ?? $item['time_schedule_id'];
                    abort(422, sprintf(
                        '%s is already booked on %s, %s.',
                        $room?->name_en ?? 'This classroom',
                        $item['day'],
                        $period
                    ));
                }
            }
        }

        DB::transaction(function () use ($context, $normalized, $currentIds) {
            $keptIds = [];

            foreach ($normalized as $item) {
                $id = $item['id'];
                unset($item['id']);

                if ($id) {
                    $routine = ClassRoutine::query()->where($context)->whereKey($id)->firstOrFail();
                    $routine->update($item);
                    $keptIds[] = $id;
                } else {
                    $routine = ClassRoutine::create($item);
                    $keptIds[] = $routine->id;
                }
            }

            ClassRoutine::query()
                ->where($context)
                ->whereNotIn('id', $keptIds ?: [0])
                ->delete();
        });

        return response()->json(['message' => 'Routine saved successfully.']);
    }

    public function print(Request $request)
    {
        return view('pages.routines.print', $this->printData($request));
    }

    public function pdf(Request $request)
    {
        $data = $this->printData($request);
        $html = view('pages.routines.pdf', $data)->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'margin_top' => 10,
            'margin_right' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'allow_charset_conversion' => false,
        ]);

        $title = 'Class Routine - ' . ($data['schoolClass']?->name_en ?? 'All classes')
            . ($data['section'] ? ' - ' . $data['section']->name_en : '');
        $mpdf->SetTitle($title);
        $mpdf->WriteHTML($html);

        $filename = Str::slug($title) . '.pdf';

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function printData(Request $request): array
    {
        $data = $request->validate([
            'academic_session_id' => ['nullable', 'exists:academic_sessions,id'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
        ]);

        $academicSessionId = $data['academic_session_id']
            ?? AcademicSession::where('status', 1)->latest('id')->value('id')
            ?? AcademicSession::latest('id')->value('id');
        abort_unless($academicSessionId, 404, 'No academic session is available.');

        $academicSession = AcademicSession::findOrFail($academicSessionId);
        $schoolClass = ! empty($data['school_class_id']) ? SchoolClass::findOrFail($data['school_class_id']) : null;
        $section = null;
        if (! empty($data['section_id'])) {
            $sectionQuery = Section::whereKey($data['section_id']);
            if ($schoolClass) {
                $sectionQuery->where('school_class_id', $schoolClass->id);
            }
            $section = $sectionQuery->firstOrFail();
        }
        $days = $this->workingDays();
        $periods = ClassSchedule::query()
            ->where(function ($query) {
                $query->where('kind', 'teaching')
                    ->orWhereRaw('LOWER(name) = ?', ['tiffin time']);
            })
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get();
        $routines = ClassRoutine::with(['subject', 'teacher', 'classroom', 'timeSchedule'])
            ->where('academic_session_id', $academicSession->id)
            ->when($schoolClass, fn ($query) => $query->where('school_class_id', $schoolClass->id))
            ->when($section, fn ($query) => $query->where('section_id', $section->id))
            ->get();

        $viewType = in_array($request->string('view')->toString(), ['teacherwise'], true)
            ? 'teacherwise'
            : 'timetable';
        $teacherwiseRows = $viewType === 'teacherwise' ? $this->buildTeacherwiseRows($routines) : [];

        return compact('academicSession', 'schoolClass', 'section', 'days', 'periods', 'routines', 'viewType', 'teacherwiseRows');
    }

    public function create()
    {
        $academicSessions = AcademicSession::where('status', 1)->orderByDesc('id')->get();
        $classes = SchoolClass::where('status', 1)->orderBy('id')->get();
        $sections = Section::with('schoolClass')->orderBy('name_en')->get();
        $subjects = [];
        $teachers = Employee::active()
            ->where('employee_type', 'teacher')
            ->with('designation')
            ->orderBy('name')
            ->get();
        $classrooms = Classroom::orderBy('name_en')->get();
        $schedules = ClassSchedule::where('kind', 'teaching')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        $days = $this->workingDays();

        return view('pages.routines.create', compact(
            'classes',
            'sections',
            'academicSessions',
            'subjects',
            'teachers',
            'classrooms',
            'schedules',
            'days'
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validateRoutine($request);

        $this->ensureNoScheduleConflict($data);

        ClassRoutine::create($data);

        return redirect()->route('routines.index')->with('success', 'Routine created successfully.');
    }

    public function show(int $id)
    {
        $routine = ClassRoutine::with(['academicSession', 'schoolClass', 'section', 'subject', 'teacher', 'classroom', 'timeSchedule'])->findOrFail($id);

        return view('pages.routines.show', compact('routine'));
    }

    public function edit(int $id)
    {
        $routine = ClassRoutine::with(['academicSession', 'schoolClass', 'section', 'subject', 'teacher', 'classroom', 'timeSchedule'])->findOrFail($id);
        $academicSessions = AcademicSession::where('status', 1)->orderByDesc('id')->get();
        $classes = SchoolClass::where('status', 1)->orderBy('id')->get();
        $sections = Section::with('schoolClass')->orderBy('name_en')->get();
        $subjects = [];
        $teachers = Employee::active()
            ->where('employee_type', 'teacher')
            ->with('designation')
            ->orderBy('name')
            ->get();
        $classrooms = Classroom::orderBy('name_en')->get();
        $schedules = ClassSchedule::where('kind', 'teaching')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        $days = $this->workingDays();

        return view('pages.routines.edit', compact(
            'routine',
            'classes',
            'sections',
            'academicSessions',
            'subjects',
            'teachers',
            'classrooms',
            'schedules',
            'days'
        ));
    }

    public function update(Request $request, int $id)
    {
        $routine = ClassRoutine::findOrFail($id);
        $data = $this->validateRoutine($request, $routine);

        $this->ensureNoScheduleConflict($data, $routine->id);

        $routine->update($data);

        return redirect()->route('routines.index')->with('success', 'Routine updated successfully.');
    }

    public function destroy(int $id)
    {
        ClassRoutine::findOrFail($id)->delete();

        return redirect()->route('routines.index')->with('success', 'Routine deleted successfully.');
    }

    private function validateRoutine(Request $request, ?ClassRoutine $routine = null): array
    {
        $data = $request->validate([
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => [
                'required',
                Rule::exists('sections', 'id')->where(function ($query) use ($request) {
                    $query->where('school_class_id', $request->integer('school_class_id'));
                }),
            ],
            'subject_id' => [
                'required',
                Rule::exists('subject_class_assignments', 'subject_id')->where(function ($query) use ($request) {
                    $query->where('school_class_id', $request->integer('school_class_id'))
                        ->where('is_active', true);
                }),
            ],
            'teacher_id' => ['nullable', 'exists:employees,id'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'day' => ['required', 'string', Rule::in($this->workingDays())],
            'time_schedule_id' => [
                'required',
                Rule::exists('class_schedules', 'id')->where(fn ($query) => $query->where('kind', 'teaching')->where('is_active', true)),
            ],
        ]);

        $schedule = ClassSchedule::findOrFail($data['time_schedule_id']);
        $data['start_time'] = $schedule->start_time;
        $data['end_time'] = $schedule->end_time;

        return $data;
    }

    private function ensureNoScheduleConflict(array $data, int|array|null $ignoreRoutineId = null): void
    {
        $baseQuery = ClassRoutine::query()
            ->where('academic_session_id', $data['academic_session_id'])
            ->where('day', $data['day']);

        if (is_array($ignoreRoutineId) && $ignoreRoutineId) {
            $baseQuery->whereNotIn('id', $ignoreRoutineId);
        } elseif ($ignoreRoutineId) {
            $baseQuery->where('id', '<>', $ignoreRoutineId);
        }

        $hasOverlap = function ($query) use ($data) {
            $query->where('start_time', '<', $data['end_time'])
                ->where('end_time', '>', $data['start_time']);
        };

        $sectionConflict = (clone $baseQuery)
            ->where('school_class_id', $data['school_class_id'])
            ->where('section_id', $data['section_id'])
            ->where($hasOverlap)
            ->exists();

        if ($sectionConflict) {
            abort(422, 'This section already has a routine in the selected time slot.');
        }

        if (! empty($data['teacher_id'])) {
            $teacherConflict = (clone $baseQuery)
                ->where('teacher_id', $data['teacher_id'])
                ->where($hasOverlap)
                ->with(['subject', 'schoolClass', 'section', 'timeSchedule'])
                ->first();

            if ($teacherConflict) {
                $period = $teacherConflict->timeSchedule?->name ?? $data['start_time'];
                $location = trim(($teacherConflict->schoolClass?->name_en ?? 'Another class') . ' - ' . ($teacherConflict->section?->name_en ?? ''));
                abort(422, sprintf(
                    'This teacher already has %s in %s on %s during %s.',
                    $teacherConflict->subject?->name ?? 'a routine',
                    $location,
                    $data['day'],
                    $period
                ));
            }
        }

        if (! empty($data['classroom_id']) && (clone $baseQuery)->where('classroom_id', $data['classroom_id'])->where($hasOverlap)->exists()) {
            abort(422, 'This classroom is already booked in the selected time slot.');
        }
    }

    private function workingDays(): array
    {
        $dayNames = [
            6 => 'Saturday',
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
        ];

        $weekendDays = array_map('intval', WeekendSetting::current()->days());

        return array_values(array_filter(
            $dayNames,
            fn ($name, $index) => ! in_array($index, $weekendDays, true),
            ARRAY_FILTER_USE_BOTH
        ));
    }
}
