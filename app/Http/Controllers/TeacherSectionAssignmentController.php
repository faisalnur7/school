<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Employee;
use App\Models\SchoolClass;
use App\Models\TeacherSectionAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherSectionAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes = SchoolClass::where('status', 1)->orderBy('id')->get();
        $teachers = $this->teacherUsers();

        $assignments = TeacherSectionAssignment::query()
            ->with([
                'user:id,name,email',
                'session:id,name_en,name_bn',
                'schoolClass:id,name_en,name_bn',
                'section:id,name_en,name_bn',
            ])
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('pages.teacher-section-assignments.index', compact(
            'sessions',
            'classes',
            'teachers',
            'assignments'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
        ]);

        TeacherSectionAssignment::query()->updateOrCreate(
            [
                'user_id' => (int) $data['user_id'],
                'session_id' => (int) $data['session_id'],
                'class_id' => (int) $data['class_id'],
                'section_id' => (int) $data['section_id'],
            ],
            []
        );

        return back()->with('success', 'Teacher assigned to section successfully.');
    }

    public function destroy(TeacherSectionAssignment $teacherSectionAssignment)
    {
        $teacherSectionAssignment->delete();
        return back()->with('success', 'Assignment removed.');
    }

    private function teacherUsers()
    {
        $teacherUserIds = Employee::query()
            ->where('employee_type', 'teacher')
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique()
            ->values();

        $query = User::query()->select(['id', 'name', 'email'])->orderBy('name');

        if ($teacherUserIds->isNotEmpty()) {
            $query->whereIn('id', $teacherUserIds);
        }

        return $query->get();
    }
}
