<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectClassConfig;
use App\Http\Requests\StoreSubjectRequest;
use App\Services\SubjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectComponentPassModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_effective_class_config_defaults_to_combined_pass_mode(): void
    {
        $class = SchoolClass::create(['name_en' => 'Class 9', 'name_bn' => 'Class 9', 'status' => 1]);
        $subject = Subject::create([
            'name' => 'Default mode subject',
            'type' => 'mandatory',
            'creative_marks' => 70,
            'mcq_marks' => 30,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        SubjectClassConfig::create([
            'subject_id' => $subject->id,
            'school_class_id' => $class->id,
            'creative_marks' => 70,
            'mcq_marks' => 30,
            'pass_mark' => 33,
        ]);

        $this->assertSame('combined', $subject->getEffectiveMarksForClass($class->id)['component_pass_mode']);
    }

    public function test_class_config_persists_individual_pass_mode(): void
    {
        $class = SchoolClass::create(['name_en' => 'Class 9', 'name_bn' => 'Class 9', 'status' => 1]);
        $subject = app(SubjectService::class)->createSubject([
            'name' => 'Individual mode subject',
            'type' => 'mandatory',
            'class_configs' => [[
                'class_id' => $class->id,
                'creative_marks' => 70,
                'mcq_marks' => 30,
                'pass_mark_percentage' => 33,
                'component_pass_mode' => 'individual',
            ]],
        ]);

        $this->assertSame('individual', $subject->fresh()->getEffectiveMarksForClass($class->id)['component_pass_mode']);
    }

    public function test_subject_default_persists_individual_pass_mode_without_class_config(): void
    {
        $subject = app(SubjectService::class)->createSubject([
            'name' => 'Subject default mode',
            'type' => 'mandatory',
            'creative_marks' => 70,
            'mcq_marks' => 30,
            'pass_mark_percentage' => 33,
            'component_pass_mode' => 'individual',
        ]);

        $this->assertSame('individual', $subject->fresh()->getEffectiveMarksForClass()['component_pass_mode']);
    }

    public function test_class_config_rejects_an_unknown_pass_mode(): void
    {
        $rules = (new StoreSubjectRequest())->rules();

        $this->assertSame('nullable|in:combined,individual', $rules['class_configs.*.component_pass_mode']);
        $this->assertSame('nullable|in:combined,individual', $rules['component_pass_mode']);
    }

    public function test_parent_effective_marks_aggregate_paper_totals_and_pass_marks(): void
    {
        $parent = Subject::create(['name' => 'Bangla', 'type' => 'mandatory', 'is_parent' => true, 'is_active' => true]);
        $parent->papers()->createMany([
            ['name' => 'Bangla 1st', 'type' => 'mandatory', 'creative_marks' => 70, 'mcq_marks' => 30, 'pass_mark' => 33, 'is_paper' => true, 'is_active' => true],
            ['name' => 'Bangla 2nd', 'type' => 'mandatory', 'creative_marks' => 70, 'mcq_marks' => 30, 'pass_mark' => 33, 'is_paper' => true, 'is_active' => true],
        ]);

        $config = $parent->getEffectiveMarksForClass();

        $this->assertSame(200.0, $config['total_marks']);
        $this->assertSame(66.0, $config['pass_mark']);
    }

    public function test_multi_paper_mode_defaults_to_combined(): void
    {
        $parent = Subject::create(['name' => 'Combined Subject', 'type' => 'mandatory', 'is_parent' => true, 'is_active' => true]);
        $papers = $parent->papers()->createMany([
            ['name' => 'First Paper', 'type' => 'mandatory', 'creative_marks' => 100, 'pass_mark' => 33, 'is_paper' => true, 'is_active' => true],
            ['name' => 'Second Paper', 'type' => 'mandatory', 'creative_marks' => 100, 'pass_mark' => 33, 'is_paper' => true, 'is_active' => true],
        ]);
        $class = SchoolClass::create(['name_en' => 'Class 5', 'name_bn' => 'Class 5', 'status' => 1]);

        $this->assertSame('combined', $parent->getMultiPaperPassModeForClass($class->id, $papers));
    }

    public function test_mode_set_on_one_paper_applies_to_the_whole_paper_group_without_changing_marks(): void
    {
        $parent = Subject::create(['name' => 'Shared Mode Subject', 'type' => 'mandatory', 'is_parent' => true, 'is_active' => true]);
        $papers = $parent->papers()->createMany([
            ['name' => 'First Paper', 'type' => 'mandatory', 'creative_marks' => 70, 'mcq_marks' => 30, 'pass_mark' => 33, 'is_paper' => true, 'is_active' => true],
            ['name' => 'Second Paper', 'type' => 'mandatory', 'creative_marks' => 70, 'mcq_marks' => 30, 'pass_mark' => 33, 'is_paper' => true, 'is_active' => true],
        ]);
        $class = SchoolClass::create(['name_en' => 'Class 9', 'name_bn' => 'Class 9', 'status' => 1]);
        $firstConfig = SubjectClassConfig::create([
            'subject_id' => $papers[0]->id,
            'school_class_id' => $class->id,
            'creative_marks' => 70,
            'mcq_marks' => 30,
            'pass_mark' => 33,
            'component_pass_mode' => 'combined',
        ]);
        $secondConfig = SubjectClassConfig::create([
            'subject_id' => $papers[1]->id,
            'school_class_id' => $class->id,
            'creative_marks' => 70,
            'mcq_marks' => 30,
            'pass_mark' => 33,
            'component_pass_mode' => 'combined',
        ]);

        app(SubjectService::class)->syncMultiPaperPassMode($papers[1], $class->id, 'individual');

        $this->assertSame('individual', $parent->fresh()->getMultiPaperPassModeForClass($class->id));
        $this->assertSame(70.0, (float) $firstConfig->fresh()->creative_marks);
        $this->assertSame(30.0, (float) $secondConfig->fresh()->mcq_marks);
        $this->assertSame('individual', $firstConfig->fresh()->component_pass_mode);
        $this->assertSame('individual', $secondConfig->fresh()->component_pass_mode);
    }
}
