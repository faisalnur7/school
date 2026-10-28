<table class="tutorial-print-summary {{ $academicInfo?->group ? 'tutorial-print-summary--with-group' : '' }}">
    <tr>
        <td style="padding: {{ $tablePaddingMm }}mm {{ $tablePaddingMm * 2 }}mm;"><div class="tutorial-print-summary-label" style="font-size: {{ $fontSizeFor('student') }};">Student</div><div class="tutorial-print-summary-value" style="font-size: {{ $fontSizeFor('student') }};">{{ $student->full_name_en }}</div></td>
        <td style="padding: {{ $tablePaddingMm }}mm {{ $tablePaddingMm * 2 }}mm;"><div class="tutorial-print-summary-label" style="font-size: {{ $fontSizeFor('student') }};">Class</div><div class="tutorial-print-summary-value" style="font-size: {{ $fontSizeFor('student') }};">{{ $reportContext['class'] ?? '—' }}</div></td>
        @if($academicInfo?->group)
            <td class="tutorial-print-summary-group" style="padding: {{ $tablePaddingMm }}mm {{ $tablePaddingMm * 2 }}mm;"><div class="tutorial-print-summary-label" style="font-size: {{ $fontSizeFor('student') }};">Group</div><div class="tutorial-print-summary-value" style="font-size: {{ $fontSizeFor('student') }};">{{ $academicInfo->group->name_en ?? $academicInfo->group->name_bn }}</div></td>
        @endif
        <td style="padding: {{ $tablePaddingMm }}mm {{ $tablePaddingMm * 2 }}mm;"><div class="tutorial-print-summary-label" style="font-size: {{ $fontSizeFor('student') }};">ID</div><div class="tutorial-print-summary-value" style="font-size: {{ $fontSizeFor('student') }};">{{ $student->student_cid ?? $student->id }}</div></td>
    </tr>
</table>
