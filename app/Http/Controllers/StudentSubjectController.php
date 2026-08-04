<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\StudentSubject;
use App\Models\SubjectClassAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentSubjectController extends Controller
{
    /**
     * List students with subject assignment status
     */
    public function index(Request $request)
    {
        $classes  = SchoolClass::where('status', 1)->orderBy('id')->get();
        $sessions = AcademicSession::orderByDesc('id')->get();
        $studentCid = trim((string) $request->input('student_cid', ''));
        $selectedSessionId = $request->input('session_id');
        $selectedClass = $request->filled('class_id') ? SchoolClass::find($request->class_id) : null;
        $selectedSectionId = $request->input('section_id');
        $selectedExam = $request->filled('exam_id') ? Exam::with('examSubjects')->find($request->exam_id) : null;

        if (!$selectedSessionId && $selectedExam) {
            $selectedSessionId = $selectedExam->academic_session_id;
        }

        if ($selectedSessionId && $selectedExam && (int) $selectedExam->academic_session_id !== (int) $selectedSessionId) {
            $selectedExam = null;
        }

        $exams = $selectedSessionId
            ? Exam::where('academic_session_id', $selectedSessionId)->orderByDesc('id')->get()
            : collect();

        $students = collect();
        $sections        = collect();

        if ($request->filled('class_id')) {
            $sections      = Section::where('school_class_id', $request->class_id)->get();
        }

        $query = Student::where('status', 1)
            ->when($studentCid !== '', fn ($q) => $q->where('student_cid', 'like', "%{$studentCid}%"))
            ->whereHas('academicInformations', function ($q) use ($request, $selectedSessionId) {
                $q->when($request->filled('class_id'), fn ($query) => $query->where('school_class_id', $request->class_id))
                    ->when($request->filled('section_id'), fn ($query) => $query->where('section_id', $request->section_id))
                    ->when($selectedSessionId, fn ($query) => $query->where('academic_session_id', $selectedSessionId));
            })
            ->with(['academicInformations' => function ($q) use ($request, $selectedSessionId) {
                $q->when($request->filled('class_id'), fn ($query) => $query->where('school_class_id', $request->class_id))
                    ->when($request->filled('section_id'), fn ($query) => $query->where('section_id', $request->section_id))
                    ->when($selectedSessionId, fn ($query) => $query->where('academic_session_id', $selectedSessionId))
                    ->with(['section', 'group']);
            }]);

        if ($selectedExam) {
            $examSubjectIds = $selectedExam->examSubjects->pluck('subject_id')->all();

            if (!empty($examSubjectIds)) {
                $query->whereHas('studentSubjects', function ($subjectQuery) use ($selectedSessionId, $examSubjectIds) {
                    $subjectQuery->when($selectedSessionId, fn ($q) => $q->where('academic_session_id', $selectedSessionId))
                        ->whereIn('subject_id', $examSubjectIds);
                });
            }
        }

        if ($request->filled('class_id') || $studentCid !== '') {
            $students = $query->orderBy('full_name_en')->paginate(30)->withQueryString();
        }

        return view('pages.student-subjects.index', compact(
            'classes', 'sessions', 'sections', 'students', 'selectedClass', 'selectedSectionId', 'selectedSessionId', 'selectedExam', 'exams', 'studentCid'
        ));
    }

    public function getExamsBySession(Request $request): JsonResponse
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return response()->json(['exams' => []]);
        }

        $exams = Exam::query()
            ->where('academic_session_id', $sessionId)
            ->orderByDesc('id')
            ->get()
            ->map(fn (Exam $exam) => [
                'id' => $exam->id,
                'name' => $exam->name,
                'type' => $exam->type,
                'type_label' => $exam->type_label,
            ])
            ->values();

        return response()->json(['exams' => $exams]);
    }

    /**
     * Show subject assignment form for a student
     */
    public function assign(Request $request, Student $student)
    {
        $sessionId = $request->session_id ?? AcademicSession::where('status', 1)->value('id');
        $session   = AcademicSession::find($sessionId);

        $academicInfo = $student->academicInformations()
            ->when($sessionId, fn($q) => $q->where('academic_session_id', $sessionId))
            ->with(['schoolClass', 'section', 'group'])
            ->latest()
            ->first();

        if (! $academicInfo) {
            return back()->with('error', 'Student has no academic information for this session.');
        }

        $classId  = $academicInfo->school_class_id;
        $groupId  = $academicInfo->group_id;
        $gender   = $student->gender == 1 ? 'male' : 'female';
        $religion = Student::religionTokenFromId($student->religion);

        // Get all assignments for this class
        $assignments = SubjectClassAssignment::where('school_class_id', $classId)
            ->where('is_active', true)
            ->where(function ($q) use ($groupId) {
                $q->whereNull('group_id')->orWhere('group_id', $groupId);
            })
            ->with('subject')
            ->get()
            ->filter(fn (SubjectClassAssignment $assignment) => $assignment->appliesToStudent($gender, $religion))
            ->values();

        // Current student subjects
        $currentSubjectIds = StudentSubject::where('student_id', $student->id)
            ->where('academic_session_id', $sessionId)
            ->pluck('subject_id')
            ->toArray();

        // Group assignments
        $compulsory = $assignments->where('is_compulsory', true)->where('is_optional', false);
        $optional   = $assignments->where('is_optional', true);

        // Group exclusive subjects
        $exclusiveGroups = $optional->whereNotNull('exclusive_group_key')
            ->groupBy('exclusive_group_key');
        $freeOptional = $optional->whereNull('exclusive_group_key');

        $sessions = AcademicSession::orderByDesc('id')->get();

        return view('pages.student-subjects.assign', compact(
            'student', 'academicInfo', 'compulsory', 'optional',
            'exclusiveGroups', 'freeOptional', 'currentSubjectIds',
            'sessionId', 'sessions', 'classId'
        ));
    }

    /**
     * Save subject assignments for a student
     */
    public function saveAssignment(Request $request, Student $student)
    {
        $request->validate([
            'session_id'    => 'required|exists:academic_sessions,id',
            'class_id'      => 'required|exists:school_classes,id',
            'subject_ids'   => 'array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $sessionId = $request->session_id;
        $classId   = $request->class_id;
        $subjectIds = array_filter((array) $request->subject_ids);
        $gender = $student->gender == 1 ? 'male' : 'female';
        $religion = Student::religionTokenFromId($student->religion);

        DB::transaction(function () use ($student, $sessionId, $classId, $subjectIds, $gender, $religion) {
            // Remove existing optional assignments (keep compulsory ones)
            StudentSubject::where('student_id', $student->id)
                ->where('academic_session_id', $sessionId)
                ->where('is_mandatory', false)
                ->delete();

            $applicableSubjectIds = SubjectClassAssignment::where('school_class_id', $classId)
                ->whereIn('subject_id', $subjectIds)
                ->where('is_active', true)
                ->with('subject')
                ->get()
                ->filter(fn (SubjectClassAssignment $assignment) => $assignment->appliesToStudent($gender, $religion))
                ->pluck('subject_id')
                ->all();

            // Add new optional assignments
            foreach ($applicableSubjectIds as $subjectId) {
                // Check if already exists (compulsory)
                $exists = StudentSubject::where('student_id', $student->id)
                    ->where('subject_id', $subjectId)
                    ->where('academic_session_id', $sessionId)
                    ->exists();

                if (! $exists) {
                    StudentSubject::create([
                        'student_id'          => $student->id,
                        'subject_id'          => $subjectId,
                        'school_class_id'     => $classId,
                        'academic_session_id' => $sessionId,
                        'is_optional'         => true,
                        'is_mandatory'        => false,
                    ]);
                }
            }
        });

        return redirect()->route('student-subjects.index', ['class_id' => $classId])
            ->with('success', "Subjects assigned to {$student->full_name_en} successfully.");
    }

    /**
     * Bulk assign compulsory subjects to all students in a class
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:school_classes,id',
            'session_id' => 'required|exists:academic_sessions,id',
        ]);

        $classId   = $request->class_id;
        $sessionId = $request->session_id;

        $compulsoryAssignments = SubjectClassAssignment::where('school_class_id', $classId)
            ->where('is_compulsory', true)
            ->where('is_active', true)
            ->get();

        $students = Student::where('status', 1)
            ->whereHas('academicInformations', fn($q) => $q
                ->where('school_class_id', $classId)
                ->where('academic_session_id', $sessionId))
            ->get();

        $count = 0;
        DB::transaction(function () use ($students, $compulsoryAssignments, $classId, $sessionId, &$count) {
            foreach ($students as $student) {
                $gender = $student->gender == 1 ? 'male' : 'female';
                $religion = Student::religionTokenFromId($student->religion);
                $applicableAssignments = $compulsoryAssignments->filter(
                    fn (SubjectClassAssignment $assignment) => $assignment->appliesToStudent($gender, $religion)
                );

                foreach ($applicableAssignments as $assignment) {
                    StudentSubject::updateOrCreate(
                        ['student_id' => $student->id, 'subject_id' => $assignment->subject_id, 'academic_session_id' => $sessionId],
                        ['school_class_id' => $classId, 'is_optional' => false, 'is_mandatory' => true]
                    );
                    $count++;
                }
            }
        });

        return back()->with('success', "Bulk assigned {$count} subject-student records.");
    }

    private function getReligionName(?int $religion): string
    {
        return Student::religionTokenFromId($religion);
    }
}
