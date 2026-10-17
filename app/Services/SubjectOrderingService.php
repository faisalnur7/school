<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\SubjectClassAssignment;
use Illuminate\Support\Collection;

class SubjectOrderingService
{
    public function orderMap(int $classId, ?int $groupId = null): array
    {
        return SubjectClassAssignment::query()
            ->where('school_class_id', $classId)
            ->when($groupId !== null, fn ($query) => $query->where(function ($q) use ($groupId) {
                $q->whereNull('group_id')->orWhere('group_id', $groupId);
            }))
            ->get(['subject_id', 'group_id', 'sort_order'])
            ->groupBy('subject_id')
            ->map(function (Collection $assignments): array {
                $common = $assignments->whereNull('group_id');
                $specific = $assignments->whereNotNull('group_id');

                if ($common->isNotEmpty()) {
                    return [0, $common->pluck('sort_order')->filter(fn ($order) => $order !== null)->min() ?? PHP_INT_MAX];
                }

                return [1, $specific->pluck('sort_order')->filter(fn ($order) => $order !== null)->min() ?? PHP_INT_MAX];
            })
            ->all();
    }

    public function sortSubjects(Collection $subjects, int $classId, ?int $groupId = null): Collection
    {
        $orders = $this->orderMap($classId, $groupId);

        return $subjects->sortBy(fn (Subject $subject) => [
            $subject->is_paper ? 0 : 1,
            ...$this->subjectOrder($subject, $orders),
            mb_strtolower($subject->name),
            (int) $subject->id,
        ])->values();
    }

    public function sortRows(Collection $rows, int $classId, ?int $groupId = null, bool $papersFirst = false): Collection
    {
        $orders = $this->orderMap($classId, $groupId);

        return $rows->sortBy(fn (array $row) => [
            $papersFirst ? (empty($row['papers']) ? 1 : 0) : 0,
            ...($orders[$row['subject_id']] ?? [1, PHP_INT_MAX]),
            mb_strtolower($row['subject_name'] ?? ''),
            (int) ($row['subject_id'] ?? 0),
        ])->values();
    }

    private function subjectOrder(Subject $subject, array $orders): array
    {
        $subjectIds = collect([$subject->id, $subject->parent_id])->filter();

        $order = $subjectIds
            ->map(fn ($id) => $orders[$id] ?? null)
            ->filter()
            ->sortBy(fn ($value) => [$value[0], $value[1]])
            ->first();

        return $order ?? [1, PHP_INT_MAX];
    }
}
