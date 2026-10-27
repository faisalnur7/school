<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use App\Models\SubjectClassConfig;
use App\Services\SubjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SubjectClassAssignment;
use App\Services\SubjectOrderingService;

class SubjectController extends Controller
{
    public function __construct(
        private SubjectService $subjectService,
        private SubjectOrderingService $subjectOrderingService
    ) {}

    /**
     * Display a listing of subjects.
     */
     public function index(Request $request)
     {
         if ($request->input('view') !== 'list') {
             return $this->indexClasswise($request);
         }

         $query = Subject::with(['classAssignments' => fn ($q) => $q->where('is_active', true)->with(['schoolClass', 'group'])])
             ->withCount('papers');

         if ($request->filled('search')) {
             $search = $request->search;
             $query->where(function ($q) use ($search) {
                 $q->where('name', 'like', "%{$search}%")
                     ->orWhere('code', 'like', "%{$search}%");
             });
         }

         if ($request->filled('type')) {
             $query->where('type', $request->type);
         }

         if ($request->filled('is_active')) {
             $query->where('is_active', $request->is_active);
         }

        if ($request->filled('school_class_id')) {
            $query->whereHas('classAssignments', function ($q) use ($request) {
                $q->where('school_class_id', $request->school_class_id)
                    ->where('is_active', true);
            });
        }
        
        if ($request->filled('group_id')) {
            $query->whereHas('classAssignments', function ($q) use ($request) {
                $q->where('group_id', $request->group_id)
                    ->where('is_active', true);
            });
        }

         $subjects = $query->orderBy('name')->paginate(100);
         $classes = $this->subjectService->getClassOptions();
         $groups = $this->subjectService->getGroupOptions();

         return view('pages.subjects.index', compact('subjects', 'classes', 'groups'));
     }

    public function indexClasswise(Request $request)
    {
        $data = $this->buildClasswiseSubjectData($request);

        return view('pages.subjects.index_classwise', $data);
    }

    public function print(Request $request)
    {
        return view('pages.subjects.print', [
            ...$this->buildClasswiseSubjectData($request),
            'autoPrint' => true,
        ]);
    }

    public function pdf(Request $request)
    {
        $html = view('pages.subjects.print', [
            ...$this->buildClasswiseSubjectData($request),
            'autoPrint' => false,
        ])->render();

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4-L',
            'margin_top' => 12,
            'margin_bottom' => 12,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))->header('Content-Type', 'application/pdf');
    }

    private function buildClasswiseSubjectData(Request $request): array
    {
        $query = Subject::with(['classAssignments' => fn ($q) => $q->where('is_active', true)->with(['schoolClass', 'group'])])
            ->withCount('papers');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('school_class_id')) {
            $query->whereHas('classAssignments', function ($q) use ($request) {
                $q->where('school_class_id', $request->school_class_id)
                    ->where('is_active', true);
            });
        }
        
        if ($request->filled('group_id')) {
            $query->whereHas('classAssignments', function ($q) use ($request) {
                $q->where('group_id', $request->group_id)
                    ->where('is_active', true);
            });
        }

        $subjects = $query->orderBy('name')->get();

        // Use plain array (not Collection) to avoid indirect modification error
        $classwiseSubjects = [];

        foreach ($subjects as $subject) {
            foreach ($subject->classAssignments as $assignment) {
                if (! $assignment->schoolClass) {
                    continue;
                }

                $classKey = $assignment->schoolClass->id;
                $groupKey = $assignment->group?->id ?? 'no_group';

                if (! isset($classwiseSubjects[$classKey])) {
                    $classwiseSubjects[$classKey] = [
                        'class' => $assignment->schoolClass,
                        'groups' => [],
                    ];
                }

                if (! isset($classwiseSubjects[$classKey]['groups'][$groupKey])) {
                    $classwiseSubjects[$classKey]['groups'][$groupKey] = [
                        'group' => $assignment->group,
                    'subjects' => collect(),
                    ];
                }

                if (! $classwiseSubjects[$classKey]['groups'][$groupKey]['subjects']->contains('id', $subject->id)) {
                    $classwiseSubjects[$classKey]['groups'][$groupKey]['subjects']->push($subject);
                }
            }
        }

        foreach ($classwiseSubjects as $classId => &$classData) {
            foreach ($classData['groups'] as $groupKey => &$groupData) {
                $groupData['subjects'] = $this->subjectOrderingService->sortSubjects(
                    $groupData['subjects'],
                    (int) $classId,
                    $groupKey === 'no_group' ? null : (int) $groupKey,
                );
            }
        }
        unset($classData, $groupData);

        $classes = $this->subjectService->getClassOptions();
        $groups = $this->subjectService->getGroupOptions();

        return compact('classwiseSubjects', 'classes', 'groups');
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create()
    {
        $classes = $this->subjectService->getClassOptions();
        $groups = $this->subjectService->getGroupOptions();

        return view('pages.subjects.edit', [
            'subject' => new Subject(),
            'classes' => $classes,
            'groups' => $groups,
            'isCreate' => true,
        ]);
    }

    /**
     * Store a newly created subject.
     */
    public function store(StoreSubjectRequest $request)
    {
        try {
            $subject = $this->subjectService->createSubject($request->validated());

            // Handle class assignments - multiple classes
            if ($request->filled('assign_to_class') && $request->has('school_class_ids')) {
                $classIds = $request->input('school_class_ids', []);

                foreach ($classIds as $classId) {
                    foreach ($this->groupScopesFromRequest($request) as $groupId) {
                        $assignmentData = [
                            'subject_id' => $subject->id,
                            'school_class_id' => $classId,
                            'group_id' => $groupId,
                            'gender' => $request->gender ?? 'all',
                            'religion' => $request->religion ?? 'all',
                            'is_optional' => $request->boolean('is_optional'),
                            'is_compulsory' => ! $request->boolean('is_optional', false),
                            'exclusive_group_key' => $request->exclusive_group_key,
                            'is_active' => true,
                        ];

                        try {
                            $this->subjectService->assignToClass($assignmentData);
                        } catch (\Exception $e) {
                            \Log::error('Subject assignment failed for class '.$classId.': '.$e->getMessage());
                        }
                    }
                }
            }

            return redirect()->route('subjects.index')->with('success', 'Subject created successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject)
    {
        $subject->load([
            'classAssignments.schoolClass', 
            'classAssignments.group', 
            'studentSubjects.student',
            'papers',
            'classConfigs.schoolClass'
        ])->loadCount(['papers', 'classAssignments', 'classConfigs']);

        return view('pages.subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject)
    {
        $classes = $this->subjectService->getClassOptions();
        $groups = $this->subjectService->getGroupOptions();
        $subject->load([
            'classAssignments.schoolClass', 
            'classAssignments.group',
            'papers',
            'classConfigs.schoolClass'
        ]);
        $duplicateAssignments = $this->subjectService->getDuplicateAssignments($subject);

        return view('pages.subjects.edit', compact('subject', 'classes', 'groups', 'duplicateAssignments'));
    }

    /**
     * Update the specified subject.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        try {
            $assignToClass = $request->boolean('assign_to_class');
            $classIds = $assignToClass
                ? collect($request->input('school_class_ids', []))
                    ->filter(fn ($classId) => $classId !== null && $classId !== '')
                    ->map(fn ($classId) => (int) $classId)
                    ->unique()
                    ->values()
                    ->all()
                : [];

            $groupScopes = $this->groupScopesFromRequest($request);
            $assignmentOptions = [
                'group_ids' => $groupScopes,
                'gender' => $request->input('gender', 'all'),
                'religion' => $request->input('religion', 'all'),
                'is_optional' => $request->boolean('is_optional'),
                'is_compulsory' => ! $request->boolean('is_optional'),
                'exclusive_group_key' => $request->input('exclusive_group_key'),
            ];

            $assignmentsWithMarks = $this->subjectService->getAssignmentsWithExamMarksToRemove($subject, $classIds, $groupScopes);

            if ($assignmentsWithMarks->isNotEmpty() && ! $request->boolean('confirm_unassignment_with_marks')) {
                return back()
                    ->withInput()
                    ->with('unassignment_warning', $assignmentsWithMarks->map(fn ($assignment) => [
                        'class_name' => $assignment->schoolClass?->name_en ?? 'Selected class',
                        'assignment_id' => $assignment->id,
                    ])->values()->all());
            }

            Log::info('Subject update STARTED', [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'request_all' => $request->all(),
                'has_school_class_ids' => $request->has('school_class_ids'),
                'filled_assign_to_class' => $request->filled('assign_to_class'),
                'school_class_ids_input' => $request->input('school_class_ids', []),
            ]);

            $syncResult = DB::transaction(function () use ($subject, $request, $classIds, $assignmentOptions) {
                $this->subjectService->updateSubject($subject, $request->validated());
                return $this->subjectService->syncClassAssignments($subject, $classIds, $assignmentOptions);
            });

            Log::info('Subject basic info UPDATED', [
                'subject_id' => $subject->id,
                'fresh_subject' => $subject->fresh()->toArray(),
            ]);

            Log::info('Subject update COMPLETED', ['subject_id' => $subject->id]);

            $message = 'Subject updated successfully';
            if (! empty($syncResult['archived'])) {
                $message .= ' Existing exam marks were preserved.';
            }

            return redirect()->route('subjects.index')->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Subject update FAILED', [
                'subject_id' => $subject->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Remove a class assignment from a subject.
     */
    public function removeAssignment(int $assignmentId)
    {
        try {
            Log::info('removeAssignment called', ['assignment_id' => $assignmentId]);
            $result = $this->subjectService->removeFromClass($assignmentId);
            Log::info('Assignment removed successfully', ['assignment_id' => $assignmentId]);

            $message = ! empty($result['archived'])
                ? 'Class assignment archived; existing exam marks were preserved.'
                : 'Class assignment removed successfully';

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Assignment removal failed', [
                'assignment_id' => $assignmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove a class-wise marks configuration from a subject.
     */
    public function removeClassConfig(Subject $subject, SubjectClassConfig $config)
    {
        abort_unless((int) $config->subject_id === (int) $subject->id, 404);

        try {
            $this->subjectService->deleteClassConfig($config);

            return redirect()->route('subjects.edit', $subject)
                ->with('success', 'Class-wise marks configuration removed successfully');
        } catch (\Exception $e) {
            Log::error('Class-wise marks configuration removal failed', [
                'subject_id' => $subject->id,
                'config_id' => $config->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified subject from storage (soft delete).
     */
    public function destroy(Subject $subject)
    {
        try {
            $this->subjectService->deleteSubject($subject);

            return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle subject status.
     */
    public function toggleStatus(Subject $subject)
    {
        $this->subjectService->toggleStatus($subject);

        $status = $subject->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Subject {$status} successfully");
    }

    /**
     * Toggle whether a subject participates in tutorial exams.
     */
    public function toggleTutorialExam(Subject $subject)
    {
        $subject->update([
            'include_in_tutorial_exam' => ! $subject->include_in_tutorial_exam,
        ]);

        $status = $subject->include_in_tutorial_exam ? 'included in' : 'excluded from';

        return back()->with('success', "Subject {$status} tutorial exams successfully");
    }

    /**
     * Assign subject to class.
     */
    public function assignToClass(Request $request)
    {
        try {
            $this->subjectService->assignToClass($request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'school_class_id' => 'required|exists:school_classes,id',
                'group_id' => 'nullable|exists:groups,id',
                'gender' => 'in:all,male,female',
                'religion' => 'string',
                'is_optional' => 'boolean',
                'is_compulsory' => 'boolean',
                'exclusive_group_key' => 'nullable|string',
            ]));

            return back()->with('success', 'Subject assigned to class successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function groupScopesFromRequest(Request $request): array
    {
        $groupIds = collect($request->input('group_ids', []))
            ->filter(fn ($groupId) => $groupId !== null && $groupId !== '')
            ->map(fn ($groupId) => (int) $groupId)
            ->unique()
            ->values()
            ->all();

        // Explicit group selections are authoritative if a stale client submits
        // both the All Groups flag and one or more group IDs.
        if (! empty($groupIds)) {
            return $groupIds;
        }

        if ($request->boolean('all_groups')) {
            return [null];
        }

        if (empty($groupIds) && $request->filled('group_id')) {
            $groupIds = [(int) $request->input('group_id')];
        }

        return empty($groupIds) ? [null] : $groupIds;
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'group_id' => ['nullable', 'exists:groups,id'],
            'assignment_ids' => ['required', 'array', 'min:1'],
            'assignment_ids.*' => ['integer', 'distinct', 'exists:subject_class_assignments,id'],
        ]);

        $assignments = SubjectClassAssignment::query()
            ->where('school_class_id', $data['school_class_id'])
            ->where('is_active', true)
            ->when(array_key_exists('group_id', $data) && $data['group_id'] !== null,
                fn ($query) => $query->where('group_id', $data['group_id']),
                fn ($query) => $query->whereNull('group_id'))
            ->whereIn('id', $data['assignment_ids'])
            ->get()
            ->keyBy('id');

        abort_unless($assignments->count() === count($data['assignment_ids']), 422, 'Invalid subject assignment order.');

        DB::transaction(function () use ($data, $assignments) {
            foreach ($data['assignment_ids'] as $index => $assignmentId) {
                $assignments[$assignmentId]->update(['sort_order' => $index + 1]);
            }
        });

        return response()->json(['ok' => true]);
    }

    /**
     * Get subjects by class (AJAX).
     */
    public function getSubjectsByClass(Request $request)
    {
        if (! $request->filled('class_id')) {
            return response()->json([]);
        }

        $subjects = $this->subjectService->getSubjectsByClass($request->class_id);

        return response()->json($subjects);
    }
}
