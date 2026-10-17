<?php

namespace Tests\Unit;

use App\Services\GradingService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GradingServiceTest extends TestCase
{
    #[DataProvider('percentageGradeProvider')]
    public function test_decimal_percentages_use_their_lower_grade_threshold(
        float $marks,
        float $fullMarks,
        string $expectedLetter,
        float $expectedGpa,
    ): void {
        $this->assertSame([
            'letter' => $expectedLetter,
            'gpa' => $expectedGpa,
        ], GradingService::getGrade($marks, $fullMarks));
    }

    public static function percentageGradeProvider(): array
    {
        return [
            '79.8 percent is A' => [79.8, 100, 'A', 4.00],
            '69.8 percent is A minus' => [69.8, 100, 'A-', 3.50],
            '59.8 percent is B' => [59.8, 100, 'B', 3.00],
            '49.8 percent is C' => [49.8, 100, 'C', 2.00],
            '39.8 percent is D' => [39.8, 100, 'D', 1.00],
            '32.8 percent is F' => [32.8, 100, 'F', 0.00],
        ];
    }
}
