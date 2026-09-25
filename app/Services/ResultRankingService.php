<?php

namespace App\Services;

class ResultRankingService
{
    /**
     * Rank result rows using the school's pass-first merit rules.
     *
     * Passed students are ordered by total, GPA, attendance, then ID.
     * Failed students are ordered by failed subjects, total, attendance,
     * then ID. The returned rank is the displayed sequential position.
     */
    public function rank(array $rows, callable $metrics, callable $studentId): array
    {
        uasort($rows, function (array $a, array $b) use ($metrics, $studentId) {
            $aMetrics = $metrics($a);
            $bMetrics = $metrics($b);

            $aFailed = (int) ($aMetrics['failed_subjects'] ?? 0);
            $bFailed = (int) ($bMetrics['failed_subjects'] ?? 0);

            if (($aFailed === 0) !== ($bFailed === 0)) {
                return $aFailed === 0 ? -1 : 1;
            }

            if ($aFailed > 0 && $aFailed !== $bFailed) {
                return $aFailed <=> $bFailed;
            }

            $totalCompare = (float) ($bMetrics['total'] ?? 0) <=> (float) ($aMetrics['total'] ?? 0);
            if ($totalCompare !== 0) {
                return $totalCompare;
            }

            if ($aFailed === 0) {
                $gpaCompare = (float) ($bMetrics['gpa'] ?? 0) <=> (float) ($aMetrics['gpa'] ?? 0);
                if ($gpaCompare !== 0) {
                    return $gpaCompare;
                }
            }

            $attendanceCompare = (int) ($bMetrics['attendance'] ?? 0) <=> (int) ($aMetrics['attendance'] ?? 0);
            if ($attendanceCompare !== 0) {
                return $attendanceCompare;
            }

            return strnatcmp((string) $studentId($a), (string) $studentId($b));
        });

        $rank = 1;
        foreach ($rows as &$row) {
            $row['rank'] = $rank++;
        }
        unset($row);

        return $rows;
    }
}
