<?php

namespace Tests\Unit;

use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Services\StudentAudienceService;
use PHPUnit\Framework\TestCase;

class StudentAudienceServiceTest extends TestCase
{
    public function test_only_current_active_students_are_in_the_audience(): void
    {
        $student = new Student(['status' => 1]);
        $student->setRelation('latestAcademicInformation', new StudentAcademicInformation([
            'is_current' => true,
            'academic_status' => 'active',
        ]));

        $this->assertTrue((new StudentAudienceService())->isActive($student));
    }

    public function test_graduated_students_are_excluded(): void
    {
        $student = new Student(['status' => 1]);
        $student->setRelation('latestAcademicInformation', new StudentAcademicInformation([
            'is_current' => true,
            'academic_status' => 'graduated',
        ]));

        $this->assertFalse((new StudentAudienceService())->isActive($student));
    }
}
