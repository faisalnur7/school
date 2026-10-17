@php
    $format = static fn ($value) => number_format((float) $value, 2);
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subject List</title>
    <style>
        @page { size: landscape; margin: 12mm 10mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #172033; font-family: Arial, Helvetica, sans-serif; font-size: 11px; }
        .report-header { display: table; width: 100%; table-layout: fixed; margin-bottom: 14px; }
        .report-header > div { display: table-cell; width: 50%; vertical-align: top; }
        .report-header > div:last-child { text-align: right; }
        h1 { margin: 0 0 4px; font-size: 22px; }
        .meta { color: #64748b; font-size: 11px; }
        .class-section { margin-top: 16px; }
        .class-section.is-not-last { page-break-after: always; }
        .class-heading { margin: 0 0 6px; padding: 7px 9px; border: 1px solid #cbd6e3; background: #e8f0f8; color: #1e3a5f; font-size: 14px; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d7dee8; padding: 5px 4px; vertical-align: middle; }
        th { background: #eff4fa; color: #24344d; font-size: 10px; text-align: left; text-transform: uppercase; }
        td { font-size: 9px; }
        .text-center { text-align: center; }
        .class-cell { font-weight: 700; }
        .group-cell { color: #475569; font-weight: 600; }
        .badge { display: inline-block; border-radius: 999px; padding: 3px 7px; font-size: 9px; font-weight: 700; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1d4ed8; }
        .badge-muted { background: #f1f5f9; color: #64748b; }
        .empty { padding: 20px; color: #64748b; text-align: center; }
        @media print {
            @page { size: landscape; margin: 12mm 10mm; }
            thead { display: table-header-group; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="report-header">
        <div>
            <h1>Subject List</h1>
            <div class="meta">Classwise and groupwise subject assignments</div>
        </div>
        <div class="meta">Generated {{ now()->format('d M Y, h:i A') }}</div>
    </div>

    @php
        $hasRows = false;
    @endphp
    @forelse($classwiseSubjects as $classId => $classData)
        @php
            $classSubjectCount = collect($classData['groups'])->sum(fn ($group) => $group['subjects']->count());
            $hasRows = $hasRows || $classSubjectCount > 0;
        @endphp
        @if($classSubjectCount > 0)
            <section class="class-section {{ $loop->last ? '' : 'is-not-last' }}">
                <h2 class="class-heading">Class: {{ $classData['class']->name_en ?? 'Unnamed Class' }} <span class="meta">({{ $classSubjectCount }} subjects)</span></h2>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 10%;">Class</th>
                            <th style="width: 9%;">Group</th>
                            <th style="width: 5%;">Order</th>
                            <th style="width: 18%;">Subject</th>
                            <th style="width: 8%;">Code</th>
                            <th style="width: 7%;">Type</th>
                            <th style="width: 8%;">Full Marks</th>
                            <th style="width: 8%;">Pass Mark</th>
                            <th style="width: 8%;">Pass Mark %</th>
                            <th style="width: 7%;">Status</th>
                            <th style="width: 7%;">Tutorial</th>
                            <th style="width: 5%;">Papers</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classData['groups'] as $groupData)
                            @foreach($groupData['subjects'] as $subject)
                                @php
                                    $effectiveMarks = $subject->getEffectiveMarksForClass((int) $classId);
                                    $fullMarks = (float) $effectiveMarks['total_marks'];
                                    $passMark = (float) $effectiveMarks['pass_mark'];
                                    $passMarkPercentage = $fullMarks > 0 ? ($passMark / $fullMarks) * 100 : 0;
                                    $assignment = $subject->classAssignments->first(fn ($item) =>
                                        (int) $item->school_class_id === (int) $classId
                                        && (($groupData['group']?->id === null && $item->group_id === null)
                                            || (int) $item->group_id === (int) $groupData['group']?->id)
                                    );
                                @endphp
                                <tr>
                                    <td class="class-cell">{{ $classData['class']->name_en ?? 'Unnamed Class' }}</td>
                                    <td class="group-cell">{{ $groupData['group']->name_en ?? 'Common' }}</td>
                                    <td class="text-center">{{ $assignment?->sort_order ?? '-' }}</td>
                                    <td>{{ $subject->name }}</td>
                                    <td>{{ $subject->code ?: '—' }}</td>
                                    <td>{{ ucfirst($subject->type) }}</td>
                                    <td class="text-center">{{ $format($fullMarks) }}</td>
                                    <td class="text-center">{{ $format($passMark) }}</td>
                                    <td class="text-center">{{ $format($passMarkPercentage) }}%</td>
                                    <td class="text-center"><span class="badge {{ $subject->is_active ? 'badge-success' : 'badge-danger' }}">{{ $subject->is_active ? 'Active' : 'Inactive' }}</span></td>
                                    <td class="text-center"><span class="badge {{ $subject->include_in_tutorial_exam ? 'badge-info' : 'badge-muted' }}">{{ $subject->include_in_tutorial_exam ? 'Included' : 'Excluded' }}</span></td>
                                    <td class="text-center">{{ $subject->papers_count }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </section>
        @endif
    @empty
    @endforelse
    @unless($hasRows)
        <div class="empty">No subjects found.</div>
    @endunless

    @if($autoPrint ?? false)
        <script>window.addEventListener('load', function () { window.print(); });</script>
    @endif
</body>
</html>
