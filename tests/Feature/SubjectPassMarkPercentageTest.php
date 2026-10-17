<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Services\SubjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectPassMarkPercentageTest extends TestCase
{
    use RefreshDatabase;

    public function test_subject_pass_mark_is_calculated_from_percentage(): void
    {
        $subject = app(SubjectService::class)->createSubject([
            'name' => 'Percentage Subject',
            'type' => 'mandatory',
            'creative_marks' => 50,
            'mcq_marks' => 50,
            'pass_mark_percentage' => 33,
        ]);

        $this->assertSame(33.0, (float) $subject->fresh()->pass_mark);
    }

    public function test_class_configuration_pass_mark_is_calculated_from_percentage(): void
    {
        $class = SchoolClass::create(['name_en' => 'Class 1', 'name_bn' => 'Class 1', 'status' => 1]);

        $subject = app(SubjectService::class)->createSubject([
            'name' => 'Class Config Percentage Subject',
            'type' => 'mandatory',
            'class_configs' => [[
                'class_id' => $class->id,
                'creative_marks' => 50,
                'pass_mark_percentage' => 33,
            ]],
        ]);

        $this->assertSame(16.5, (float) $subject->classConfigs()->first()->pass_mark);
    }
}
