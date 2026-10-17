<?php

namespace Tests\Feature;

use App\Http\Controllers\TutorialReportController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\TestCase;

class TutorialReportCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_tutorial_comments_use_percentage_ranges(): void
    {
        $comment = $this->commentMethod();

        $this->assertSame('Excellent', $comment->invoke(new TutorialReportController(), 90.0, 0));
        $this->assertSame('Good', $comment->invoke(new TutorialReportController(), 70.0, 0));
        $this->assertSame('Satisfactory', $comment->invoke(new TutorialReportController(), 59.8, 0));
        $this->assertSame('Satisfactory', $comment->invoke(new TutorialReportController(), 40.0, 0));
        $this->assertSame('Needs Improvement', $comment->invoke(new TutorialReportController(), 39.99, 0));
    }

    public function test_failed_or_absent_subject_overrides_percentage_comment(): void
    {
        $comment = $this->commentMethod();

        $this->assertSame('Needs Improvement', $comment->invoke(new TutorialReportController(), 95.0, 1));
    }

    private function commentMethod(): ReflectionMethod
    {
        $method = new ReflectionMethod(TutorialReportController::class, 'tutorialComment');
        $method->setAccessible(true);

        return $method;
    }
}
