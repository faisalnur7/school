<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class StudentAudienceService
{
    public function activeStudents(): Builder
    {
        return Student::query()
            ->where('status', 1)
            ->whereHas('latestAcademicInformation', fn (Builder $query) => $query
                ->where('is_current', true)
                ->where('academic_status', 'active'));
    }

    public function studentForUser(User $user): ?Student
    {
        $student = $user->student()->with('latestAcademicInformation')->first();
        return $this->isActive($student) ? $student : null;
    }

    public function isActive(?Student $student): bool
    {
        $academic = $student?->latestAcademicInformation;
        return (bool) ($student
            && (int) $student->status === 1
            && $academic
            && $academic->is_current
            && $academic->academic_status === 'active');
    }
}
