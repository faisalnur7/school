<?php

namespace Tests\Unit;

use App\Services\ResultRankingService;
use Tests\TestCase;

class ResultRankingServiceTest extends TestCase
{
    public function test_passed_students_are_ranked_before_failed_students(): void
    {
        $rows = [
            ['id' => '04', 'metrics' => ['failed_subjects' => 1, 'total' => 99, 'gpa' => 5, 'attendance' => 5]],
            ['id' => '02', 'metrics' => ['failed_subjects' => 0, 'total' => 80, 'gpa' => 4, 'attendance' => 5]],
            ['id' => '01', 'metrics' => ['failed_subjects' => 0, 'total' => 90, 'gpa' => 3, 'attendance' => 5]],
        ];

        $ranked = app(ResultRankingService::class)->rank(
            $rows,
            fn (array $row) => $row['metrics'],
            fn (array $row) => $row['id'],
        );

        $this->assertSame(['01', '02', '04'], array_column($ranked, 'id'));
        $this->assertSame([1, 2, 3], array_column($ranked, 'rank'));
    }

    public function test_failed_students_use_failures_then_total_then_attendance_then_id(): void
    {
        $rows = [
            ['id' => '03', 'metrics' => ['failed_subjects' => 2, 'total' => 80, 'gpa' => 2, 'attendance' => 10]],
            ['id' => '02', 'metrics' => ['failed_subjects' => 1, 'total' => 50, 'gpa' => 1, 'attendance' => 1]],
            ['id' => '01', 'metrics' => ['failed_subjects' => 1, 'total' => 60, 'gpa' => 1, 'attendance' => 1]],
            ['id' => '04', 'metrics' => ['failed_subjects' => 1, 'total' => 60, 'gpa' => 1, 'attendance' => 2]],
        ];

        $ranked = app(ResultRankingService::class)->rank(
            $rows,
            fn (array $row) => $row['metrics'],
            fn (array $row) => $row['id'],
        );

        $this->assertSame(['04', '01', '02', '03'], array_column($ranked, 'id'));
    }
}
