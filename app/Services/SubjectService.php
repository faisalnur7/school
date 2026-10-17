<?php

namespace App\Services;

use App\Models\Group;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentSubject;
use App\Models\Subject;
use App\Models\SubjectClassAssignment;
use App\Models\SubjectClassConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectService
{
    /**
     * Create a new subject with optional papers and class configs
     */
    public function createSubject(array $data): Subject
    {
        return DB::transaction(function () use ($data) {
            // Extract papers if present
            $papersData = $data['papers'] ?? [];
            $classConfigsData = $data['class_configs'] ?? [];
            
            // Remove non-subject fields
            unset(
                $data['papers'],
                $data['class_configs'],
                $data['assign_to_class'],
                $data['school_class_ids'],
                $data['group_id'],
                $data['group_ids'],
                $data['all_groups'],
                $data['gender'],
                $data['religion'],
                $data['is_optional'],
                $data['exclusive_group_key'],
                $data['confirm_unassignment_with_marks']
            );
            
            $data = $this->normalizeSubjectPassMarkData($data);

            // Create the subject
            $subject = Subject::create($data);
            
            // Create papers if any
            if (!empty($papersData)) {
                foreach ($papersData as $paperData) {
                    $paperData['parent_id'] = $subject->id;
                    $paperData['is_paper'] = true;
                    $paperData['has_multiple_papers'] = false;
                    $paperData['type'] = $subject->type; // Papers inherit type
                    Subject::create($this->normalizeSubjectPassMarkData($paperData));
                }
            }
            
            // Create class configs if any
            if (!empty($classConfigsData)) {
                foreach ($classConfigsData as $configData) {
                    $subject->classConfigs()->create($this->normalizeClassConfigData($configData));
                }
            }
            
            return $subject;
        });
    }

    /**
     * Update an existing subject
     */
    public function updateSubject(Subject $subject, array $data): Subject
    {
        \Log::info('updateSubject called', [
            'subject_id' => $subject->id,
            'original_data' => $subject->toArray(),
            'update_data' => $data,
        ]);

        return DB::transaction(function () use ($subject, $data) {
            $hasPapersData = array_key_exists('papers', $data);
            $hasClassConfigsData = array_key_exists('class_configs', $data);

            // Extract papers if present
            $papersData = $data['papers'] ?? [];
            $classConfigsData = $data['class_configs'] ?? [];
            
            // Remove non-subject fields
            unset(
                $data['papers'],
                $data['class_configs'],
                $data['assign_to_class'],
                $data['school_class_ids'],
                $data['group_id'],
                $data['group_ids'],
                $data['all_groups'],
                $data['gender'],
                $data['religion'],
                $data['is_optional'],
                $data['exclusive_group_key'],
                $data['confirm_unassignment_with_marks']
            );
            
            $data = $this->normalizeSubjectPassMarkData($data, $subject);

            // Update subject
            $subject->update($data);
            
            // Synchronize papers in place so existing paper IDs and marks remain attached.
            if ($hasPapersData) {
                $existingPapers = $subject->papers()->get()->keyBy('id');
                $submittedPaperIds = collect($papersData)
                    ->pluck('id')
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->all();

                foreach ($existingPapers as $paper) {
                    if (! in_array($paper->id, $submittedPaperIds, true)) {
                        $paper->delete();
                    }
                }

                foreach ($papersData as $paperData) {
                    $paperId = isset($paperData['id']) && $paperData['id'] !== ''
                        ? (int) $paperData['id']
                        : null;

                    unset($paperData['id']);
                    $paperData = array_merge($paperData, [
                        'parent_id' => $subject->id,
                        'is_paper' => true,
                        'has_multiple_papers' => false,
                        'type' => $subject->type,
                    ]);

                    if ($paperId && $existingPapers->has($paperId)) {
                        $existingPapers->get($paperId)->update($this->normalizeSubjectPassMarkData($paperData, $existingPapers->get($paperId)));
                    } else {
                        Subject::create($this->normalizeSubjectPassMarkData($paperData));
                    }
                }
            }
            
            // Handle class configs - replace all
            if ($hasClassConfigsData) {
                $subject->classConfigs()->delete();
                if (!empty($classConfigsData)) {
                    foreach ($classConfigsData as $configData) {
                        $subject->classConfigs()->create($this->normalizeClassConfigData($configData));
                    }
                }
            }

            $fresh = $subject->fresh();
            \Log::info('Subject updated', [
                'subject_id' => $fresh->id,
                'new_data' => $fresh->toArray(),
            ]);

            return $fresh;
        });
    }

    /**
     * Return active assignments that would be removed and have historical marks.
     */
    public function getAssignmentsWithExamMarksToRemove(Subject $subject, array $selectedClassIds, ?array $selectedGroupIds = null): \Illuminate\Support\Collection
    {
        $selectedClassIds = collect($selectedClassIds)
            ->map(fn ($classId) => (int) $classId)
            ->unique()
            ->values();

        $selectedGroupIds = $selectedGroupIds === null
            ? null
            : collect($selectedGroupIds)
                ->map(fn ($groupId) => $groupId === null || $groupId === '' ? null : (int) $groupId)
                ->unique()
                ->values();

        return $subject->classAssignments()
            ->with('schoolClass')
            ->where('is_active', true)
            ->when($selectedClassIds->isNotEmpty() && $selectedGroupIds === null,
                fn ($query) => $query->whereNotIn('school_class_id', $selectedClassIds->all()))
            ->get()
            ->filter(function (SubjectClassAssignment $assignment) use ($selectedClassIds, $selectedGroupIds) {
                if ($selectedClassIds->isEmpty() || ! $selectedClassIds->contains((int) $assignment->school_class_id)) {
                    return true;
                }

                return $selectedGroupIds !== null && ! $selectedGroupIds->contains(
                    $assignment->group_id === null ? null : (int) $assignment->group_id
                );
            })
            ->filter(fn (SubjectClassAssignment $assignment) => $this->hasExamMarksForAssignment($assignment))
            ->values();
    }

    /**
     * Find active assignments where a class-wide assignment overlaps a
     * group-specific assignment for the same subject and class.
     */
    public function getDuplicateAssignments(Subject $subject): \Illuminate\Support\Collection
    {
        return $subject->classAssignments()
            ->with(['schoolClass', 'group'])
            ->where('is_active', true)
            ->get()
            ->groupBy('school_class_id')
            ->filter(fn ($assignments) => $assignments->count() > 1
                && $assignments->contains(fn (SubjectClassAssignment $assignment) => $assignment->group_id === null)
                && $assignments->contains(fn (SubjectClassAssignment $assignment) => $assignment->group_id !== null))
            ->map(function ($assignments) {
                $recommended = $assignments->first(fn (SubjectClassAssignment $assignment) => $assignment->group_id === null);

                return [
                    'class' => $recommended->schoolClass,
                    'assignments' => $assignments->values(),
                    'recommended' => $recommended,
                ];
            })
            ->values();
    }

    /**
     * Synchronize a subject's class assignments with the edit form selection.
     * Existing marks are preserved; assignments with marks are archived.
     */
    public function syncClassAssignments(Subject $subject, array $selectedClassIds, array $options = []): array
    {
        return DB::transaction(function () use ($subject, $selectedClassIds, $options) {
            $selectedClassIds = collect($selectedClassIds)
                ->map(fn ($classId) => (int) $classId)
                ->unique()
                ->values();

            $activeAssignments = $subject->classAssignments()
                ->where('is_active', true)
                ->get();
            $allAssignments = $subject->classAssignments()->get();

            $removed = [];
            $archived = [];

            foreach ($activeAssignments->whereNotIn('school_class_id', $selectedClassIds->all()) as $assignment) {
                $this->retireAssignment($assignment, $removed, $archived);
            }

            $groupScopes = array_key_exists('group_ids', $options)
                ? collect($options['group_ids'])
                    ->map(fn ($groupId) => $groupId === null || $groupId === '' ? null : (int) $groupId)
                    ->unique()
                    ->values()
                : collect([$options['group_id'] ?? null]);

            if ($groupScopes->isEmpty()) {
                $groupScopes = collect([null]);
            }

            foreach ($selectedClassIds as $classId) {
                $classAssignments = $activeAssignments
                    ->where('school_class_id', $classId)
                    ->values();
                $classAllAssignments = $allAssignments
                    ->where('school_class_id', $classId)
                    ->values();
                $usedAssignmentIds = [];

                foreach ($groupScopes as $groupId) {
                    $assignmentData = [
                        'subject_id' => $subject->id,
                        'group_id' => $groupId,
                        'gender' => $options['gender'] ?? 'all',
                        'religion' => $options['religion'] ?? 'all',
                        'is_optional' => (bool) ($options['is_optional'] ?? false),
                        'is_compulsory' => (bool) ($options['is_compulsory'] ?? true),
                        'exclusive_group_key' => $options['exclusive_group_key'] ?? null,
                        'is_active' => true,
                    ];

                    $targetAssignment = $classAssignments->first(
                        fn (SubjectClassAssignment $assignment) => ! in_array($assignment->id, $usedAssignmentIds, true)
                            && $assignment->group_id === $groupId
                    );

                    // Reuse an archived assignment for the same scope when it
                    // exists. The assignment table has a unique key that also
                    // includes inactive rows, so converting a common row into
                    // that group would otherwise fail with a duplicate-key
                    // error.
                    if (! $targetAssignment) {
                        $targetAssignment = $classAllAssignments->first(
                            fn (SubjectClassAssignment $assignment) => ! $assignment->is_active
                                && ! in_array($assignment->id, $usedAssignmentIds, true)
                                && $assignment->group_id === $groupId
                        );
                    }

                    // Preserve the existing assignment and marks when converting
                    // a class-wide assignment to one or more selected groups.
                    if (! $targetAssignment && $groupId !== null) {
                        $targetAssignment = $classAssignments->first(
                            fn (SubjectClassAssignment $assignment) => ! in_array($assignment->id, $usedAssignmentIds, true)
                                && $assignment->group_id === null
                        );
                    }

                    if ($targetAssignment) {
                        $wasInactive = ! $targetAssignment->is_active;
                        $targetAssignment->update(array_merge($assignmentData, [
                            'school_class_id' => $classId,
                            'is_active' => true,
                        ]));
                        $usedAssignmentIds[] = $targetAssignment->id;

                        if ($wasInactive && ($assignmentData['is_compulsory'] ?? true)) {
                            $this->assignStudentsToSubject($targetAssignment->fresh());
                        }
                    } else {
                        $this->assignToClass(array_merge($assignmentData, [
                            'school_class_id' => $classId,
                        ]));
                    }
                }

                foreach ($classAssignments->reject(fn ($assignment) => in_array($assignment->id, $usedAssignmentIds, true)) as $staleAssignment) {
                    $this->retireAssignment($staleAssignment, $removed, $archived);
                }
            }

            Log::info('Subject class assignments synchronized', [
                'subject_id' => $subject->id,
                'selected_class_ids' => $selectedClassIds->all(),
                'selected_group_ids' => $groupScopes->all(),
                'removed_assignment_ids' => $removed,
                'archived_assignment_ids' => $archived,
            ]);

            return compact('removed', 'archived');
        });
    }

    private function retireAssignment(SubjectClassAssignment $assignment, array &$removed, array &$archived): void
    {
        $this->removeCurrentSessionStudentAssignments($assignment);

        if ($this->hasExamMarksForAssignment($assignment)) {
            $assignment->update(['is_active' => false]);
            $archived[] = $assignment->id;
        } else {
            $assignment->delete();
            $removed[] = $assignment->id;
        }
    }

    private function hasExamMarksForAssignment(SubjectClassAssignment $assignment): bool
    {
        $subjectIds = Subject::query()
            ->where('id', $assignment->subject_id)
            ->orWhere('parent_id', $assignment->subject_id)
            ->pluck('id');

        return ExamMark::query()
            ->join('exams', 'exams.id', '=', 'exam_marks.exam_id')
            ->join('student_academic_information as sai', function ($join) {
                $join->on('sai.student_id', '=', 'exam_marks.student_id')
                    ->on('sai.academic_session_id', '=', 'exams.academic_session_id');
            })
            ->whereIn('exam_marks.subject_id', $subjectIds)
            ->where('sai.school_class_id', $assignment->school_class_id)
            ->exists();
    }

    private function removeCurrentSessionStudentAssignments(SubjectClassAssignment $assignment): void
    {
        $sessionId = \App\Models\AcademicSession::where('status', 1)->value('id');

        if (! $sessionId) {
            return;
        }

        StudentSubject::where('subject_id', $assignment->subject_id)
            ->where('school_class_id', $assignment->school_class_id)
            ->where('academic_session_id', $sessionId)
            ->delete();
    }

    /**
     * Delete a subject (soft delete)
     */
    public function deleteSubject(Subject $subject): void
    {
        $subject->delete();
    }

    /**
     * Toggle subject status
     */
    public function toggleStatus(Subject $subject): Subject
    {
        $subject->is_active = ! $subject->is_active;
        $subject->save();

        return $subject;
    }

    /**
     * Assign subject to a class
     */
    public function assignToClass(array $data, bool $autoAssignStudents = true): SubjectClassAssignment
    {
        return DB::transaction(function () use ($data, $autoAssignStudents) {
            \Log::info('assignToClass CALLED', $data);

            $groupId = $data['group_id'] ?? null;

            $assignmentQuery = SubjectClassAssignment::where('subject_id', $data['subject_id'])
                ->where('school_class_id', $data['school_class_id']);

            if ($groupId === null || $groupId === '') {
                $assignmentQuery->whereNull('group_id');
            } else {
                $assignmentQuery->where('group_id', $groupId);
            }

            $assignments = $assignmentQuery->orderBy('id')->get();
            $existingAssignment = $assignments->first();

            \Log::info('Existing assignment lookup complete', [
                'existing_assignment_id' => $existingAssignment?->id,
                'duplicates_found' => $assignments->count(),
            ]);

            if ($existingAssignment) {
                \Log::info('Updating existing assignment', [
                    'assignment_id' => $existingAssignment->id,
                ]);

                $existingAssignment->update($data);

                if ($assignments->count() > 1) {
                    $duplicateIds = $assignments->slice(1)->pluck('id')->all();

                    SubjectClassAssignment::whereIn('id', $duplicateIds)->delete();

                    \Log::info('Removed duplicate assignments after update', [
                        'assignment_ids' => $duplicateIds,
                    ]);
                }

                $assignment = $existingAssignment->fresh();
            } else {
                // Validate exclusive group rule for Science
                if (! empty($data['exclusive_group_key'])) {
                    $this->validateExclusiveGroupRule($data);
                }

                $data['sort_order'] ??= (int) SubjectClassAssignment::query()
                    ->where('school_class_id', $data['school_class_id'])
                    ->when($groupId === null || $groupId === '', fn ($query) => $query->whereNull('group_id'), fn ($query) => $query->where('group_id', $groupId))
                    ->max('sort_order') + 1;

                $assignment = SubjectClassAssignment::create($data);

                \Log::info('New assignment created', [
                    'assignment_id' => $assignment->id,
                ]);
            }

            // Auto-assign to students if it's compulsory
            if ($autoAssignStudents && ($data['is_compulsory'] ?? true)) {
                \Log::info('Auto-assigning students (compulsory)', [
                    'assignment_id' => $assignment->id,
                ]);
                $this->assignStudentsToSubject($assignment);
            }

            return $assignment;
        });
    }

    /**
     * Assign all students in a class to a compulsory subject
     */
    public function assignStudentsToSubject(SubjectClassAssignment $assignment): int
    {
        \Log::info('assignStudentsToSubject started', [
            'assignment_id' => $assignment->id,
            'subject_id' => $assignment->subject_id,
            'class_id' => $assignment->school_class_id,
        ]);

        $subject = $assignment->subject;
        $classId = $assignment->school_class_id;

        // Get current academic session
        $session = \App\Models\AcademicSession::where('status', 1)->first();
        if (! $session) {
            throw new \InvalidArgumentException('No active academic session found.');
        }

        \Log::info('Academic session found', ['session_id' => $session->id]);

        $studentQuery = Student::where('status', 1)
            ->whereHas('academicInformations', function ($q) use ($classId) {
                $q->where('school_class_id', $classId);
            });

        if ($assignment->group_id) {
            $studentQuery->whereHas('academicInformations', function ($q) use ($assignment) {
                $q->where('group_id', $assignment->group_id);
            });
        }

        $students = $studentQuery->get()->filter(
            fn (Student $student) => $assignment->appliesToStudent(
                $student->gender === Student::MALE ? 'male' : 'female',
                Student::religionTokenFromId($student->religion)
            )
        )->values();

        \Log::info('Students found for assignment', [
            'count' => $students->count(),
            'filters' => [
                'class_id' => $classId,
                'group_id' => $assignment->group_id,
                'gender' => $assignment->gender,
                'religion' => $assignment->religion,
            ],
        ]);

        $assignedCount = 0;
        foreach ($students as $student) {
            try {
                StudentSubject::updateOrCreate([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'academic_session_id' => $session->id,
                ], [
                    'school_class_id' => $classId,
                    'is_optional' => false,
                    'is_mandatory' => true,
                ]);
                $assignedCount++;
            } catch (\Exception $e) {
                \Log::error('Failed to assign student', [
                    'student_id' => $student->id,
                    'error' => $e->getMessage(),
                ]);

                continue;
            }
        }

        \Log::info('assignStudentsToSubject completed', [
            'assignment_id' => $assignment->id,
            'total_assigned' => $assignedCount,
        ]);

        return $assignedCount;
    }

    /**
     * Get religion ID by name
     */
    private function getReligionIdByName(string $name): ?int
    {
        return Student::religionIdFromToken($name);
    }

    /**
     * Validate exclusive group rule (e.g., Biology vs Higher Math in Science)
     */
    private function validateExclusiveGroupRule(array $data): void
    {
        $exclusiveKey = $data['exclusive_group_key'];

        // Check if there's already a mandatory subject with this exclusive key in the same class
        $existingMandatory = SubjectClassAssignment::where('school_class_id', $data['school_class_id'])
            ->where('exclusive_group_key', $exclusiveKey)
            ->where('is_compulsory', true)
            ->exists();

        if ($existingMandatory && ($data['is_compulsory'] ?? false)) {
            throw new \InvalidArgumentException(
                'Cannot assign both subjects as mandatory in the same exclusive group. '.
                'Only one subject can be mandatory (e.g., Biology OR Higher Math).'
            );
        }
    }

    /**
     * Get available subjects for a student
     */
    public function getAvailableSubjectsForStudent(
        int $classId,
        ?int $groupId,
        string $gender,
        string $religion
    ): \Illuminate\Database\Eloquent\Collection {
        return SubjectClassAssignment::where('school_class_id', $classId)
            ->where(function ($query) use ($groupId) {
                $query->whereNull('group_id')
                    ->orWhere('group_id', $groupId);
            })
            ->where('is_active', true)
            ->with(['subject', 'subject.classConfigs'])
            ->get()
            ->filter(fn (SubjectClassAssignment $assignment) => $assignment->appliesToStudent($gender, $religion))
            ->pluck('subject')
            ->filter()
            ->values();
    }

    /**
     * Get mandatory subjects for a student
     */
    public function getMandatorySubjectsForStudent(
        int $classId,
        ?int $groupId,
        string $gender,
        string $religion
    ): \Illuminate\Database\Eloquent\Collection {
        return SubjectClassAssignment::where('school_class_id', $classId)
            ->where(function ($query) use ($groupId) {
                $query->whereNull('group_id')
                    ->orWhere('group_id', $groupId);
            })
            ->where('is_compulsory', true)
            ->where('is_active', true)
            ->with(['subject', 'subject.classConfigs'])
            ->get()
            ->filter(fn (SubjectClassAssignment $assignment) => $assignment->appliesToStudent($gender, $religion))
            ->pluck('subject')
            ->filter()
            ->values();
    }

    /**
     * Get optional subjects for a student
     */
    public function getOptionalSubjectsForStudent(
        int $classId,
        ?int $groupId,
        string $gender,
        string $religion
    ): \Illuminate\Database\Eloquent\Collection {
        return SubjectClassAssignment::where('school_class_id', $classId)
            ->where(function ($query) use ($groupId) {
                $query->whereNull('group_id')
                    ->orWhere('group_id', $groupId);
            })
            ->where('is_optional', true)
            ->where('is_active', true)
            ->with(['subject', 'subject.classConfigs'])
            ->get()
            ->filter(fn (SubjectClassAssignment $assignment) => $assignment->appliesToStudent($gender, $religion))
            ->pluck('subject')
            ->filter()
            ->values();
    }

    /**
     * Assign subject to a student
     */
    public function assignToStudent(array $data): StudentSubject
    {
        $student = Student::findOrFail($data['student_id']);
        $gender = $student->gender === Student::MALE ? 'male' : 'female';
        $religion = Student::religionTokenFromId($student->religion);

        $assignment = SubjectClassAssignment::where('subject_id', $data['subject_id'])
            ->where('school_class_id', $data['school_class_id'])
            ->where('is_active', true)
            ->get()
            ->first(fn (SubjectClassAssignment $candidate) => $candidate->appliesToStudent($gender, $religion));

        if (! $assignment) {
            throw new \InvalidArgumentException('This subject is not available for assignment.');
        }

        // Check exclusive group rule for Science
        if ($assignment->exclusive_group_key) {
            $this->validateStudentExclusiveGroupSelection($data);
        }

        return StudentSubject::create($data);
    }

    /**
     * Validate student selection for exclusive group (e.g., Biology OR Higher Math)
     */
    private function validateStudentExclusiveGroupSelection(array $data): void
    {
        $assignment = SubjectClassAssignment::where('subject_id', $data['subject_id'])
            ->where('school_class_id', $data['school_class_id'])
            ->first();

        if (! $assignment || ! $assignment->exclusive_group_key) {
            return;
        }

        // Check if student already has the other subject in this exclusive group
        $otherSubjectInGroup = SubjectClassAssignment::where('school_class_id', $data['school_class_id'])
            ->where('exclusive_group_key', $assignment->exclusive_group_key)
            ->where('subject_id', '!=', $data['subject_id'])
            ->first();

        if ($otherSubjectInGroup) {
            $hasOther = StudentSubject::where('student_id', $data['student_id'])
                ->where('subject_id', $otherSubjectInGroup->subject_id)
                ->where('academic_session_id', $data['academic_session_id'])
                ->exists();

            if ($hasOther) {
                throw new \InvalidArgumentException(
                    'Cannot select both subjects. Please deselect the other subject first.'
                );
            }
        }
    }

    /**
     * Get all subjects with their class assignments
     */
    public function getAllSubjectsWithAssignments(): \Illuminate\Database\Eloquent\Collection
    {
        return Subject::with(['classAssignments.schoolClass', 'classAssignments.group', 'papers'])
            ->latest()
            ->get();
    }

    /**
     * Get subjects filtered by class
     */
    public function getSubjectsByClass(int $classId): \Illuminate\Database\Eloquent\Collection
    {
        return SubjectClassAssignment::where('school_class_id', $classId)
            ->whereHas('subject', fn ($q) => $q->active())
            ->with(['subject' => fn ($q) => $q->active(), 'group'])
            ->active()
            ->get();
    }

    /**
     * Get subject options for dropdown
     */
    public function getSubjectOptions(): array
    {
        return Subject::active()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Get class options for dropdown
     */
    public function getClassOptions(): array
    {
        return SchoolClass::where('status', 1)
            ->orderBy('id')
            ->pluck('name_en', 'id')
            ->toArray();
    }

    /**
     * Get group options for dropdown
     */
    public function getGroupOptions(): array
    {
        return Group::where('status', 1)
            ->pluck('name_en', 'id')
            ->toArray();
    }

    /**
     * Remove subject from class and clean up student assignments
     */
    public function removeFromClass(int $assignmentId): array
    {
        return DB::transaction(function () use ($assignmentId) {
            $assignment = SubjectClassAssignment::findOrFail($assignmentId);

            $this->removeCurrentSessionStudentAssignments($assignment);

            if ($this->hasExamMarksForAssignment($assignment)) {
                $assignment->update(['is_active' => false]);
                $archived = true;
            } else {
                $assignment->delete();
                $archived = false;
            }

            return compact('archived');
        });
    }

    /**
     * Manage subject papers (sync papers list)
     */
    public function managePapers(Subject $subject, array $paperIds): array
    {
        $result = [
            'added' => 0,
            'removed' => 0,
        ];

        // Get current papers
        $currentPaperIds = $subject->papers()->pluck('id')->toArray();
        
        // Papers to add
        $toAdd = array_diff($paperIds, $currentPaperIds);
        foreach ($toAdd as $paperId) {
            $paper = Subject::find($paperId);
            if ($paper) {
                $paper->parent_id = $subject->id;
                $paper->is_paper = true;
                $paper->save();
                $result['added']++;
            }
        }
        
        // Papers to remove
        $toRemove = array_diff($currentPaperIds, $paperIds);
        foreach ($toRemove as $paperId) {
            $paper = Subject::find($paperId);
            if ($paper) {
                $paper->parent_id = null;
                $paper->is_paper = false;
                $paper->save();
                $result['removed']++;
            }
        }
        
        // Update parent flags
        $subject->is_parent = !empty($toAdd) || count($currentPaperIds) > 0;
        $subject->has_multiple_papers = $subject->papers()->count() > 0;
        $subject->save();

        return $result;
    }

    /**
     * Get papers for a subject
     */
    public function getPapers(Subject $subject): \Illuminate\Database\Eloquent\Collection
    {
        return $subject->papers()->orderBy('name')->get();
    }

    /**
     * Get parent subjects (combined subjects only)
     */
    public function getParentSubjects(): \Illuminate\Database\Eloquent\Collection
    {
        return Subject::where('is_parent', true)->orWhere('has_multiple_papers', true)->get();
    }

    /**
     * Validate combined subject structure
     */
    public function validateCombinedSubject(Subject $subject): array
    {
        $errors = [];
        
        if ($subject->is_parent || $subject->has_multiple_papers) {
            // Check that parent has papers
            $papersCount = $subject->papers()->count();
            if ($papersCount < 2) {
                $errors[] = 'A combined subject must have at least 2 papers.';
            }
            
            // Check that parent has no marks (if required by spec)
            // The spec says "cannot have marks directly (optional design)"
            // We'll allow parent marks but warn
        }
        
        // Validate each paper
        foreach ($subject->papers as $paper) {
            if (!$paper->hasValidMarks()) {
                $errors[] = "Paper '{$paper->name}' must have at least one mark field > 0.";
            }
            if (!$paper->hasValidPassMark()) {
                $errors[] = "Paper '{$paper->name}' has invalid pass mark.";
            }
        }
        
        // If combine_papers_for_result is true, validate combined total
        if ($subject->combine_papers_for_result && $subject->papers()->count() > 0) {
            $combinedTotal = $subject->papers()->sum(function ($paper) {
                return $paper->total_marks;
            });
            
            if ($subject->pass_mark > $combinedTotal) {
                $errors[] = 'Pass mark cannot exceed combined total marks of all papers.';
            }
        }
        
        return $errors;
    }

    /**
     * Get effective marks for subject in a class (with fallback)
     */
    public function getEffectiveMarks(Subject $subject, int $classId): array
    {
        return $subject->getEffectiveMarksForClass($classId);
    }

    /**
     * Create class config for subject
     */
    public function createClassConfig(array $data): SubjectClassConfig
    {
        return SubjectClassConfig::create($this->normalizeClassConfigData($data));
    }

    /**
     * Update class config
     */
    public function updateClassConfig(SubjectClassConfig $config, array $data): SubjectClassConfig
    {
        $config->update($this->normalizeClassConfigData($data));
        return $config;
    }

    /**
     * Delete class config
     */
    public function deleteClassConfig(SubjectClassConfig $config): bool
    {
        return $config->delete();
    }

    /**
     * Normalize class config payload from form field names to DB column names.
     */
    private function normalizeClassConfigData(array $data): array
    {
        if (array_key_exists('class_id', $data) && ! array_key_exists('school_class_id', $data)) {
            $data['school_class_id'] = $data['class_id'];
        }

        unset($data['class_id']);

        if (array_key_exists('pass_mark_percentage', $data) && $data['pass_mark_percentage'] !== '' && $data['pass_mark_percentage'] !== null) {
            $total = collect(['creative_marks', 'mcq_marks', 'practical_marks', 'viva_marks'])
                ->sum(fn ($field) => (float) ($data[$field] ?? 0));
            $data['pass_mark'] = round($total * ((float) $data['pass_mark_percentage'] / 100), 2);
        }

        unset($data['pass_mark_percentage']);

        return $data;
    }

    private function normalizeSubjectPassMarkData(array $data, ?Subject $subject = null): array
    {
        if (array_key_exists('pass_mark_percentage', $data) && $data['pass_mark_percentage'] !== '' && $data['pass_mark_percentage'] !== null) {
            $marks = collect(['creative_marks', 'mcq_marks', 'practical_marks', 'viva_marks']);
            $total = $marks->sum(function ($field) use ($data, $subject) {
                return (float) ($data[$field] ?? $subject?->{$field} ?? 0);
            });
            $data['pass_mark'] = round($total * ((float) $data['pass_mark_percentage'] / 100), 2);
        }

        unset($data['pass_mark_percentage']);

        return $data;
    }
}
