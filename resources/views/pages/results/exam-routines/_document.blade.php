@php
    $renderForPdf = $renderForPdf ?? false;
    $cardSettings = $cardSettings ?? null;
    $schoolName = $setting?->name ?: config('app.name', 'School Name');
    $address = $setting?->address;
    $contacts = array_filter([$setting?->contact_number_1, $setting?->contact_number_2]);
    $logoSource = $cardSettings?->card_logo && file_exists(public_path($cardSettings->card_logo))
        ? $cardSettings->card_logo
        : $setting?->logo;
    $logoPath = $logoSource && file_exists(public_path($logoSource))
        ? ($renderForPdf ? public_path($logoSource) : asset($logoSource))
        : null;
    $examTypeLabel = ($exam->exam_category ?? null) === 'tutorial' ? 'Tutorial Exam' : 'Terminal Exam';
    $groupLabel = $group?->name_en ?: 'All groups';
    $formatTime = fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('g:i A') : '—';
    $slotTime = fn ($slot) => $slot === 'noon'
        ? $formatTime($routine->noon_start_time) . ' – ' . $formatTime($routine->noon_end_time)
        : $formatTime($routine->morning_start_time) . ' – ' . $formatTime($routine->morning_end_time);
    $orderedItems = $items->sortBy(fn ($item) => [
        $item->exam_date?->toDateString() ?? '9999-12-31',
        $item->sort_order,
    ])->values();
    $routineRows = $orderedItems->groupBy(fn ($item) => implode('|', [
        $item->exam_date?->toDateString() ?? '',
        $item->slot,
    ]))->values();
@endphp

<div class="routine-document">
    <div class="school-header-wrap">
        <table class="school-header-table">
            <tr>
                <td class="school-header-logo-cell">
                    <div class="school-logo-box">
                        @if($logoPath)
                            <img src="{{ $logoPath }}" alt="{{ $schoolName }} logo" class="school-logo-img">
                        @else
                            <span class="school-logo-fallback">{{ strtoupper(substr($schoolName, 0, 1)) }}</span>
                        @endif
                    </div>
                </td>
                <td class="school-header-info-cell">
                    <div class="school-title">{{ $schoolName }}</div>
                    @if($address)<div class="school-line">{{ $address }}</div>@endif
                    @if(count($contacts))<div class="school-line">{{ implode(' | ', $contacts) }}</div>@endif
                    @if($setting?->slogan)<div class="school-line">{{ $setting->slogan }}</div>@endif
                </td>
            </tr>
        </table>
    </div>

    <h1 class="document-title">Exam Routine</h1>
    <p class="document-subtitle">{{ $exam->name }}</p>

    <table class="routine-summary">
        <tr>
            <td><div class="summary-label">Academic Session</div><div class="summary-value">{{ $academicSession->name_en }}</div></td>
            <td><div class="summary-label">Exam Type</div><div class="summary-value">{{ $examTypeLabel }}</div></td>
            <td><div class="summary-label">Class</div><div class="summary-value">{{ $schoolClass->name_en }}</div></td>
            <td><div class="summary-label">Group</div><div class="summary-value">{{ $groupLabel }}</div></td>
        </tr>
    </table>

    <table class="routine-table">
        <thead>
            <tr>
                <th class="subject">Subject</th>
                <th class="date">Date</th>
                <th class="day">Day</th>
                <th class="time">Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach($routineRows as $rowItems)
                @php
                    $rowItem = $rowItems->first();
                    $subjectNames = $rowItems->map(fn ($item) => $item->subject?->name ?: '—')->join(' / ');
                @endphp
                <tr>
                    <td><div class="subject-name">{{ $subjectNames }}</div></td>
                    <td>{{ $rowItem->exam_date?->format('d/m/Y') ?: '—' }}</td>
                    <td>{{ $rowItem->day_name }}</td>
                    <td>{{ $slotTime($rowItem->slot) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="routine-footer">Generated on {{ now()->format('d M Y, h:i A') }}</div>
</div>
