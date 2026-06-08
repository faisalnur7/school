<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Group;
use Illuminate\Http\Request;

class StudentBirthdayController extends Controller
{
    public function index(Request $request)
    {
        $students = collect();
        $date = $request->input('date');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $sessionId = $request->input('session_id');
        $classId   = $request->input('class_id');
        $sectionId = $request->input('section_id');
        $groupId   = $request->input('group_id');

        if ($fromDate && $toDate) {
            $from = \Carbon\Carbon::parse($fromDate);
            $to   = \Carbon\Carbon::parse($toDate);
            $fromKey = $from->format('m-d');
            $toKey = $to->format('m-d');

            $students = Student::whereHas('academicInformations', function ($q) use ($sessionId, $classId, $sectionId, $groupId) {
                if ($sessionId) $q->where('academic_session_id', $sessionId);
                if ($classId)   $q->where('school_class_id', $classId);
                if ($sectionId) $q->where('section_id', $sectionId);
                if ($groupId)   $q->where('group_id', $groupId);
            })
            ->where(function ($query) use ($fromKey, $toKey) {
                if ($fromKey <= $toKey) {
                    $query->whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') >= ? AND DATE_FORMAT(date_of_birth, '%m-%d') <= ?", [$fromKey, $toKey]);
                } else {
                    $query->whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') >= ? OR DATE_FORMAT(date_of_birth, '%m-%d') <= ?", [$fromKey, $toKey]);
                }
            })
            ->with(['academicInformations' => fn($q) => $q->orderByDesc('id')->with(['schoolClass', 'section', 'academicSession', 'group'])])
            ->get();
        } elseif ($date) {
            $parsed = \Carbon\Carbon::parse($date);
            $students = Student::whereMonth('date_of_birth', $parsed->month)
                ->whereDay('date_of_birth', $parsed->day)
                ->whereHas('academicInformations', function ($q) use ($sessionId, $classId, $sectionId, $groupId) {
                    if ($sessionId) $q->where('academic_session_id', $sessionId);
                    if ($classId)   $q->where('school_class_id', $classId);
                    if ($sectionId) $q->where('section_id', $sectionId);
                    if ($groupId)   $q->where('group_id', $groupId);
                })
                ->with(['academicInformations' => fn($q) => $q->orderByDesc('id')->with(['schoolClass', 'section', 'academicSession', 'group'])])
                ->get();
        }

        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes  = SchoolClass::where('status', 1)->get();
        $sections = $classId ? Section::where('school_class_id', $classId)->get() : collect();
        $groups   = Group::where('status', 1)->get();

        return view('pages.students.birthdays', compact(
            'students',
            'date',
            'fromDate',
            'toDate',
            'sessions',
            'classes',
            'sections',
            'groups',
            'sessionId',
            'classId',
            'sectionId',
            'groupId'
        ));
    }
}
