<?php

namespace App\Http\Controllers;

use App\Models\ClassRoutine;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\WeekendSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoutineController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassRoutine::with(['schoolClass', 'section', 'subject', 'teacher', 'classroom']);
        $days = $this->workingDays();

        if ($request->filled('search')) {
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

        if ($request->filled('school_class_id')) {
            $query->where('school_class_id', $request->integer('school_class_id'));
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->integer('section_id'));
        }

        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }

        $routines = $query
            ->orderBy('school_class_id')
            ->orderBy('section_id')
            ->orderByRaw("FIELD(day, '" . implode("','", $days) . "')")
            ->orderBy('start_time')
            ->paginate(20)
            ->withQueryString();

        $classes = SchoolClass::where('status', 1)->orderBy('name_en')->get();
        $sections = Section::with('schoolClass')->where('status', 1)->orderBy('name_en')->get();

        return view('pages.routines.index', compact('routines', 'classes', 'sections', 'days'));
    }

    public function create()
    {
        $classes = SchoolClass::where('status', 1)->orderBy('name_en')->get();
        $sections = Section::with('schoolClass')->where('status', 1)->orderBy('name_en')->get();
        $subjects = [];
        $teachers = Employee::active()
            ->where('employee_type', 'teacher')
            ->with('designation')
            ->orderBy('name')
            ->get();
        $classrooms = Classroom::orderBy('name_en')->get();
        $days = $this->workingDays();

        return view('pages.routines.create', compact(
            'classes',
            'sections',
            'subjects',
            'teachers',
            'classrooms',
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
        $routine = ClassRoutine::with(['schoolClass', 'section', 'subject', 'teacher', 'classroom'])->findOrFail($id);

        return view('pages.routines.show', compact('routine'));
    }

    public function edit(int $id)
    {
        $routine = ClassRoutine::with(['schoolClass', 'section', 'subject', 'teacher', 'classroom'])->findOrFail($id);
        $classes = SchoolClass::where('status', 1)->orderBy('name_en')->get();
        $sections = Section::with('schoolClass')->where('status', 1)->orderBy('name_en')->get();
        $subjects = [];
        $teachers = Employee::active()
            ->where('employee_type', 'teacher')
            ->with('designation')
            ->orderBy('name')
            ->get();
        $classrooms = Classroom::orderBy('name_en')->get();
        $days = $this->workingDays();

        return view('pages.routines.edit', compact(
            'routine',
            'classes',
            'sections',
            'subjects',
            'teachers',
            'classrooms',
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
        return $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => [
                'required',
                Rule::exists('sections', 'id')->where(function ($query) use ($request) {
                    $query->where('school_class_id', $request->integer('school_class_id'));
                }),
            ],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['nullable', 'exists:employees,id'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'day' => ['required', 'string', Rule::in($this->workingDays())],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);
    }

    private function ensureNoScheduleConflict(array $data, ?int $ignoreRoutineId = null): void
    {
        $baseQuery = ClassRoutine::query()->where('day', $data['day']);

        if ($ignoreRoutineId) {
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

        if (! empty($data['teacher_id']) && (clone $baseQuery)->where('teacher_id', $data['teacher_id'])->where($hasOverlap)->exists()) {
            abort(422, 'This teacher already has a routine in the selected time slot.');
        }

        if (! empty($data['classroom_id']) && (clone $baseQuery)->where('classroom_id', $data['classroom_id'])->where($hasOverlap)->exists()) {
            abort(422, 'This classroom is already booked in the selected time slot.');
        }
    }

    private function workingDays(): array
    {
        $dayNames = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        $weekendDays = WeekendSetting::current()->days();

        return array_values(array_filter(
            $dayNames,
            fn ($name, $index) => ! in_array($index, $weekendDays, true),
            ARRAY_FILTER_USE_BOTH
        ));
    }
}
