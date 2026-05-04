<?php

namespace App\Services;

class GradingService
{
    /**
     * Bangladesh SSC/HSC grading scale
     */
    private static array $grades = [
        ['min' => 80, 'max' => 100, 'letter' => 'A+', 'gpa' => 5.00],
        ['min' => 70, 'max' => 79,  'letter' => 'A',  'gpa' => 4.00],
        ['min' => 60, 'max' => 69,  'letter' => 'A-', 'gpa' => 3.50],
        ['min' => 50, 'max' => 59,  'letter' => 'B',  'gpa' => 3.00],
        ['min' => 40, 'max' => 49,  'letter' => 'C',  'gpa' => 2.00],
        ['min' => 33, 'max' => 39,  'letter' => 'D',  'gpa' => 1.00],
        ['min' => 0,  'max' => 32,  'letter' => 'F',  'gpa' => 0.00],
    ];

    public static function getGrade(float $marks, float $fullMarks = 100): array
    {
        if ($fullMarks <= 0) {
            return ['letter' => 'N/A', 'gpa' => 0.00];
        }

        $percentage = ($marks / $fullMarks) * 100;

        foreach (self::$grades as $grade) {
            if ($percentage >= $grade['min'] && $percentage <= $grade['max']) {
                return ['letter' => $grade['letter'], 'gpa' => $grade['gpa']];
            }
        }

        return ['letter' => 'F', 'gpa' => 0.00];
    }

    public static function getLetterGrade(float $marks, float $fullMarks = 100): string
    {
        return self::getGrade($marks, $fullMarks)['letter'];
    }

    public static function getGpa(float $marks, float $fullMarks = 100): float
    {
        return self::getGrade($marks, $fullMarks)['gpa'];
    }

    public static function calculateGpa(array $subjectGpas): float
    {
        if (empty($subjectGpas)) return 0.00;
        return round(array_sum($subjectGpas) / count($subjectGpas), 2);
    }

    public static function getGpaLabel(float $gpa): string
    {
        return match(true) {
            $gpa >= 5.00 => 'A+',
            $gpa >= 4.00 => 'A',
            $gpa >= 3.50 => 'A-',
            $gpa >= 3.00 => 'B',
            $gpa >= 2.00 => 'C',
            $gpa >= 1.00 => 'D',
            default      => 'F',
        };
    }

    public static function hasFailed(array $subjectMarks): bool
    {
        foreach ($subjectMarks as $mark) {
            if (($mark['letter_grade'] ?? 'F') === 'F') return true;
        }
        return false;
    }

    public static function allGrades(): array
    {
        return self::$grades;
    }
}
