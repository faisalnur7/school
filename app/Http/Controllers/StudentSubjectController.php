<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\StudentSubject;
use App\Models\SubjectClassAssignment;
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

        $students = collect();
        $selectedClass   = null;
        $selectedSection = null;
        $sections        = collect();

        if ($request->filled('class_id')) {
            $selectedClass = SchoolClass::find($request->class_id);
            $sections      = Section::where('school_class_id', $request->class_id)->get();

            $query = Student::where('status', 1)
                ->whereHas('academicInformations', function ($q) use ($request) {
                    $q->where('school_class_id', $request->class_id);
                    if ($request->filled('section_id')) {
                        $q->where('section_id', $request->section_id);
                    }
                    if ($request->filled('session_id')) {
                        $q->where('academic_session_id', $request->session_id);
                    }
                })
                ->with(['academicInformations' => fn($q) => $q->where('school_class_id', $request->class_id)
                    ->with(['section', 'group'])]);

            $students = $query->orderBy('full_name_en')->paginate(30)->withQueryString();
        }

        return view('pages.student-subjects.index', compact(
            'classes', 'sessions', 'sections', 'students', 'selectedClass'
        ));
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
        $religion = $this->getReligionName($student->religion);

        // Get all assignments for this class
        $assignments = SubjectClassAssignment::where('school_class_id', $classId)
            ->where('is_active', true)
            ->where(function ($q) use ($groupId) {
                $q->whereNull('group_id')->orWhere('group_id', $groupId);
            })
            ->with('subject')
            ->get();

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

        DB::transaction(function () use ($student, $sessionId, $classId, $subjectIds) {
            // Remove existing optional assignments (keep compulsory ones)
            StudentSubject::where('student_id', $student->id)
                ->where('academic_session_id', $sessionId)
                ->where('is_mandatory', false)
                ->delete();

            // Add new optional assignments
            foreach ($subjectIds as $subjectId) {
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
                foreach ($compulsoryAssignments as $assignment) {
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
        return match($religion) {
            1 => 'islam',
            2 => 'hinduism',
            3 => 'christianity',
            4 => 'buddhism',
            default => 'other',
        };
    }
}
