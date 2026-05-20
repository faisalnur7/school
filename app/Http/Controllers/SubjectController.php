<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use App\Services\SubjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    public function __construct(
        private SubjectService $subjectService
    ) {}

    /**
     * Display a listing of subjects.
     */
     public function index(Request $request)
     {
         $query = Subject::with(['classAssignments.schoolClass', 'classAssignments.group'])
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
                $q->where('school_class_id', $request->school_class_id);
            });
        }
        
        if ($request->filled('group_id')) {
            $query->whereHas('classAssignments', function ($q) use ($request) {
                $q->where('group_id', $request->group_id);
            });
        }

         $subjects = $query->orderBy('name')->paginate(100);
         $classes = $this->subjectService->getClassOptions();
         $groups = $this->subjectService->getGroupOptions();

         return view('pages.subjects.index', compact('subjects', 'classes', 'groups'));
     }

    public function indexClasswise(Request $request)
    {
        $query = Subject::with(['classAssignments.schoolClass', 'classAssignments.group'])
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
                $q->where('school_class_id', $request->school_class_id);
            });
        }
        
        if ($request->filled('group_id')) {
            $query->whereHas('classAssignments', function ($q) use ($request) {
                $q->where('group_id', $request->group_id);
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

        $classes = $this->subjectService->getClassOptions();
        $groups = $this->subjectService->getGroupOptions();

        return view('pages.subjects.index_classwise', compact('classwiseSubjects', 'classes', 'groups'));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create()
    {
        $classes = $this->subjectService->getClassOptions();
        $groups = $this->subjectService->getGroupOptions();

        return view('pages.subjects.create', compact('classes', 'groups'));
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
                    $assignmentData = [
                        'subject_id' => $subject->id,
                        'school_class_id' => $classId,
                        'group_id' => $request->group_id,
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
        ]);

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

        return view('pages.subjects.edit', compact('subject', 'classes', 'groups'));
    }

    /**
     * Update the specified subject.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        try {
            Log::info('Subject update STARTED', [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'request_all' => $request->all(),
                'has_school_class_ids' => $request->has('school_class_ids'),
                'filled_assign_to_class' => $request->filled('assign_to_class'),
                'school_class_ids_input' => $request->input('school_class_ids', []),
            ]);

            $this->subjectService->updateSubject($subject, $request->validated());

            Log::info('Subject basic info UPDATED', [
                'subject_id' => $subject->id,
                'fresh_subject' => $subject->fresh()->toArray(),
            ]);

            // Handle class assignments - multiple classes
            if ($request->filled('assign_to_class') && $request->has('school_class_ids')) {
                $classIds = $request->input('school_class_ids', []);

                Log::info('Processing class assignments', [
                    'subject_id' => $subject->id,
                    'class_ids' => $classIds,
                    'is_optional' => $request->boolean('is_optional'),
                    'is_compulsory' => ! $request->boolean('is_optional', false),
                ]);

                foreach ($classIds as $classId) {
                    $assignmentData = [
                        'subject_id' => $subject->id,
                        'school_class_id' => $classId,
                        'group_id' => $request->group_id,
                        'gender' => $request->gender ?? 'all',
                        'religion' => $request->religion ?? 'all',
                        'is_optional' => $request->boolean('is_optional'),
                        'is_compulsory' => ! $request->boolean('is_optional', false),
                        'exclusive_group_key' => $request->exclusive_group_key,
                        'is_active' => true,
                    ];

                    Log::info('Attempting assignment', [
                        'subject_id' => $subject->id,
                        'class_id' => $classId,
                        'data' => $assignmentData,
                    ]);

                    try {
                        $result = $this->subjectService->assignToClass($assignmentData);
                        Log::info('Class assignment SUCCESS', [
                            'subject_id' => $subject->id,
                            'class_id' => $classId,
                            'assignment_id' => $result->id ?? null,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Class assignment FAILED', [
                            'subject_id' => $subject->id,
                            'class_id' => $classId,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                }
            } else {
                Log::info('No class assignments processed - checkbox or array missing', [
                    'assign_to_class' => $request->filled('assign_to_class'),
                    'has_school_class_ids' => $request->has('school_class_ids'),
                ]);
            }

            Log::info('Subject update COMPLETED', ['subject_id' => $subject->id]);

            return redirect()->route('subjects.index')->with('success', 'Subject updated successfully');
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
            $this->subjectService->removeFromClass($assignmentId);
            Log::info('Assignment removed successfully', ['assignment_id' => $assignmentId]);

            return back()->with('success', 'Class assignment removed successfully');
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
