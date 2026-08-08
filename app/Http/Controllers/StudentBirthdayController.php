<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AcademicSession;
use App\Models\SchoolSetting;
use App\Models\StudentAcademicInformation;
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

        $birthdayFilter = null;
        if ($fromDate && $toDate) {
            $from = \Carbon\Carbon::parse($fromDate);
            $to   = \Carbon\Carbon::parse($toDate);
            $fromKey = $from->format('m-d');
            $toKey = $to->format('m-d');

            $birthdayFilter = function ($query) use ($fromKey, $toKey) {
                if ($fromKey <= $toKey) {
                    $query->whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') >= ? AND DATE_FORMAT(date_of_birth, '%m-%d') <= ?", [$fromKey, $toKey]);
                } else {
                    $query->whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') >= ? OR DATE_FORMAT(date_of_birth, '%m-%d') <= ?", [$fromKey, $toKey]);
                }
            };
        } elseif ($date) {
            $parsed = \Carbon\Carbon::parse($date);
            $fromKey = $parsed->format('m-d');
            $toKey = $parsed->format('m-d');

            $birthdayFilter = function ($query) use ($fromKey, $toKey) {
                $query->whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') = ?", [$fromKey]);
            };
        }

        $studentIdsQuery = StudentAcademicInformation::query()
            ->select('student_id')
            ->whereNotNull('student_id')
            ->when($sessionId, fn ($q) => $q->where('academic_session_id', $sessionId))
            ->when($classId, fn ($q) => $q->where('school_class_id', $classId))
            ->when($sectionId, fn ($q) => $q->where('section_id', $sectionId))
            ->when($groupId, fn ($q) => $q->where('group_id', $groupId))
            ->distinct();

        $query = Student::query()
            ->select(['id', 'student_cid', 'full_name_en', 'full_name_bn', 'image', 'date_of_birth', 'gender', 'guardian_phone', 'father_phone'])
            ->whereIn('id', $studentIdsQuery)
            ->with(['latestAcademicInformation' => fn($q) => $q->with(['schoolClass', 'section', 'academicSession', 'group'])]);

        $skipBirthdayFilter = $fromDate && $toDate && \Carbon\Carbon::parse($fromDate)->format('m-d') === '01-01' && \Carbon\Carbon::parse($toDate)->format('m-d') === '12-31';

        if ($birthdayFilter && !$skipBirthdayFilter) {
            $query->where($birthdayFilter);
        }

        $students = $query
            ->orderBy('full_name_en')
            ->simplePaginate(50)
            ->appends($request->query());

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

    public function birthdayCardPreview(Request $request, Student $student)
    {
        $student->loadMissing([
            'latestAcademicInformation' => fn ($q) => $q->with(['schoolClass', 'section', 'academicSession', 'group']),
        ]);

        $academicInfo = $student->latestAcademicInformation;
        $setting = SchoolSetting::current();

        $studentName = $student->full_name_en ?: $student->full_name_bn ?: 'Student';
        $issueDate = now()->format('F j, Y');
        $birthdayWish = trim((string) ($request->input('wish') ?: <<<TEXT
Wishing you a birthday filled with smiles, colorful surprises, happy moments, and beautiful memories.
May this year bring you growth, confidence, good health, and every success you deserve.
TEXT));

        return view('pages.students.birthday-card-preview', compact(
            'student',
            'academicInfo',
            'setting',
            'studentName',
            'issueDate',
            'birthdayWish'
        ));
    }
}
