<?php

namespace App\Services;

use App\Models\Group;
use App\Models\SchoolClass;
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
            unset($data['papers'], $data['class_configs']);
            
            // Create the subject
            $subject = Subject::create($data);
            
            // Create papers if any
            if (!empty($papersData)) {
                foreach ($papersData as $paperData) {
                    $paperData['parent_id'] = $subject->id;
                    $paperData['is_paper'] = true;
                    $paperData['has_multiple_papers'] = false;
                    $paperData['type'] = $subject->type; // Papers inherit type
                    Subject::create($paperData);
                }
            }
            
            // Create class configs if any
            if (!empty($classConfigsData)) {
                foreach ($classConfigsData as $configData) {
                    $subject->classConfigs()->create($configData);
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
            unset($data['papers'], $data['class_configs']);
            
            // Update subject
            $subject->update($data);
            
            // Handle papers - delete old ones and recreate (simple strategy)
            if ($hasPapersData) {
                $subject->papers()->delete();
                if (!empty($papersData)) {
                    foreach ($papersData as $paperData) {
                        $paperData['parent_id'] = $subject->id;
                        $paperData['is_paper'] = true;
                        $paperData['has_multiple_papers'] = false;
                        $paperData['type'] = $subject->type;
                        Subject::create($paperData);
                    }
                }
            }
            
            // Handle class configs - replace all
            if ($hasClassConfigsData) {
                $subject->classConfigs()->delete();
                if (!empty($classConfigsData)) {
                    foreach ($classConfigsData as $configData) {
                        $subject->classConfigs()->create($configData);
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
        \Log::info('assignToClass CALLED', $data);

        // Check for duplicate assignment
        $existingAssignment = SubjectClassAssignment::where('subject_id', $data['subject_id'])
            ->where('school_class_id', $data['school_class_id'])
            ->where(function ($query) use ($data) {
                $query->whereNull('group_id')
                    ->orWhere('group_id', $data['group_id'] ?? null);
            })
            ->where('gender', $data['gender'] ?? 'all')
            ->where('religion', $data['religion'] ?? 'all')
            ->first();

        \Log::info('Duplicate check complete', [
            'existing_assignment_id' => $existingAssignment?->id,
        ]);

        if ($existingAssignment) {
            \Log::info('Updating existing assignment', [
                'assignment_id' => $existingAssignment->id,
            ]);

            // Update existing assignment
            $existingAssignment->update($data);

            // Re-assign students if compulsory
            if ($autoAssignStudents && ($data['is_compulsory'] ?? true)) {
                \Log::info('Re-assigning students (compulsory)', [
                    'assignment_id' => $existingAssignment->id,
                ]);
                $this->assignStudentsToSubject($existingAssignment);
            }

            return $existingAssignment;
        }

        // Validate exclusive group rule for Science
        if (! empty($data['exclusive_group_key'])) {
            $this->validateExclusiveGroupRule($data);
        }

        $assignment = SubjectClassAssignment::create($data);

        \Log::info('New assignment created', [
            'assignment_id' => $assignment->id,
        ]);

        // Auto-assign to students if it's compulsory
        if ($autoAssignStudents && ($data['is_compulsory'] ?? true)) {
            \Log::info('Auto-assigning students (compulsory)', [
                'assignment_id' => $assignment->id,
            ]);
            $this->assignStudentsToSubject($assignment);
        }

        return $assignment;
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
        $groupId = $assignment->group_id;
        $gender = $assignment->gender;
        $religion = $assignment->religion;

        // Get current academic session
        $session = \App\Models\AcademicSession::where('status', 1)->first();
        if (! $session) {
            throw new \InvalidArgumentException('No active academic session found.');
        }

        \Log::info('Academic session found', ['session_id' => $session->id]);

        // Build student query based on filters
        $studentQuery = \App\Models\Student::where('status', 1)
            ->whereHas('academicInformations', function ($q) use ($classId) {
                $q->where('school_class_id', $classId);
            });

        // Filter by group if specified
        if ($groupId) {
            $studentQuery->whereHas('academicInformations', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            });
        }

        // Filter by gender if not 'all'
        if ($gender && $gender !== 'all') {
            $studentQuery->where('gender', $gender === 'male' ? 1 : 0);
        }

        // Filter by religion if not 'all'
        if ($religion && $religion !== 'all') {
            $religionId = $this->getReligionIdByName($religion);
            if ($religionId) {
                $studentQuery->where('religion', $religionId);
            }
        }

        $students = $studentQuery->get();

        \Log::info('Students found for assignment', [
            'count' => $students->count(),
            'filters' => ['class_id' => $classId, 'group_id' => $groupId, 'gender' => $gender, 'religion' => $religion],
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
        $religionMap = [
            'islam' => 1,
            'hinduism' => 2,
            'christianity' => 3,
            'buddhism' => 4,
            'other' => 5,
        ];

        return $religionMap[strtolower($name)] ?? null;
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
            ->where(function ($query) use ($gender) {
                $query->where('gender', 'all')
                    ->orWhere('gender', $gender);
            })
            ->where(function ($query) use ($religion) {
                $query->where('religion', 'all')
                    ->orWhere('religion', $religion);
            })
            ->where('is_active', true)
            ->with(['subject', 'subject.classConfigs'])
            ->get()
            ->pluck('subject');
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
            ->where(function ($query) use ($gender) {
                $query->where('gender', 'all')
                    ->orWhere('gender', $gender);
            })
            ->where(function ($query) use ($religion) {
                $query->where('religion', 'all')
                    ->orWhere('religion', $religion);
            })
            ->where('is_compulsory', true)
            ->where('is_active', true)
            ->with(['subject', 'subject.classConfigs'])
            ->get()
            ->pluck('subject');
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
            ->where(function ($query) use ($gender) {
                $query->where('gender', 'all')
                    ->orWhere('gender', $gender);
            })
            ->where(function ($query) use ($religion) {
                $query->where('religion', 'all')
                    ->orWhere('religion', $religion);
            })
            ->where('is_optional', true)
            ->where('is_active', true)
            ->with(['subject', 'subject.classConfigs'])
            ->get()
            ->pluck('subject');
    }

    /**
     * Assign subject to a student
     */
    public function assignToStudent(array $data): StudentSubject
    {
        // Check if subject is available for this student
        $assignment = SubjectClassAssignment::where('subject_id', $data['subject_id'])
            ->where('school_class_id', $data['school_class_id'])
            ->where('is_active', true)
            ->first();

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
            ->with(['subject', 'group'])
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
    public function removeFromClass(int $assignmentId): bool
    {
        $assignment = SubjectClassAssignment::findOrFail($assignmentId);

        $session = \App\Models\AcademicSession::where('status', 1)->first();

        // Remove student assignments for this subject-class combination
        StudentSubject::where('subject_id', $assignment->subject_id)
            ->where('school_class_id', $assignment->school_class_id)
            ->where('academic_session_id', $session?->id)
            ->delete();

        $assignment->delete();

        return true;
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
        return SubjectClassConfig::create($data);
    }

    /**
     * Update class config
     */
    public function updateClassConfig(SubjectClassConfig $config, array $data): SubjectClassConfig
    {
        $config->update($data);
        return $config;
    }

    /**
     * Delete class config
     */
    public function deleteClassConfig(SubjectClassConfig $config): bool
    {
        return $config->delete();
    }
}
