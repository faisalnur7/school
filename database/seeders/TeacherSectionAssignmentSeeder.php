<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\Employee;
use App\Models\Section;
use App\Models\StudentAcademicInformation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSectionAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $teacherUserIds = Employee::query()
            ->where('employee_type', 'teacher')
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique()
            ->values();

        $seedAllUsers = false;
        if ($teacherUserIds->isEmpty()) {
            // Fallback: treat all users as teachers (projects that don't use employees for auth).
            $teacherUserIds = User::query()->pluck('id')->values();
            $seedAllUsers = true;
        }

        if ($teacherUserIds->isEmpty()) return;

        $sessionIds = AcademicSession::query()
            ->where('status', 1)
            ->pluck('id')
            ->values();

        if ($sessionIds->isEmpty()) {
            $latestSessionId = AcademicSession::query()->max('id');
            if (! $latestSessionId) {
                return;
            }
            $sessionIds = collect([(int) $latestSessionId]);
        }

        $combos = StudentAcademicInformation::query()
            ->whereIn('academic_session_id', $sessionIds)
            ->whereNotNull('school_class_id')
            ->whereNotNull('section_id')
            ->selectRaw('academic_session_id as session_id, school_class_id as class_id, section_id')
            ->distinct()
            ->orderBy('session_id')
            ->orderBy('class_id')
            ->orderBy('section_id')
            ->get();

        if ($combos->isEmpty()) {
            $sessionId = (int) $sessionIds->first();
            $combos = Section::query()
                ->whereNotNull('school_class_id')
                ->selectRaw('? as session_id, school_class_id as class_id, id as section_id', [$sessionId])
                ->orderBy('class_id')
                ->orderBy('section_id')
                ->get();
        }

        $now = now();
        $rows = [];

        if ($seedAllUsers) {
            foreach ($teacherUserIds as $userId) {
                foreach ($combos as $combo) {
                    $rows[] = [
                        'user_id' => (int) $userId,
                        'session_id' => (int) $combo->session_id,
                        'class_id' => (int) $combo->class_id,
                        'section_id' => (int) $combo->section_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        } else {
            $i = 0;
            foreach ($combos as $combo) {
                $rows[] = [
                    'user_id' => (int) $teacherUserIds[$i % $teacherUserIds->count()],
                    'session_id' => (int) $combo->session_id,
                    'class_id' => (int) $combo->class_id,
                    'section_id' => (int) $combo->section_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $i++;
            }
        }

        DB::table('teacher_section_assignments')->upsert(
            $rows,
            ['user_id', 'session_id', 'class_id', 'section_id'],
            ['updated_at']
        );
    }
}
