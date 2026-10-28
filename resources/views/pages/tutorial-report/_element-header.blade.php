<div class="tutorial-print-header">
    <table class="tutorial-print-header-table">
        <tr>
            <td class="tutorial-print-header-logo-cell">
                @if($hasLogo)<img class="tutorial-print-header-logo" src="{{ $logoPath }}" alt="School logo">@endif
            </td>
            <td class="tutorial-print-header-text-cell">
                <div class="tutorial-print-school">{{ $schoolName }}</div>
                @if($school->address)<div class="tutorial-print-address">{{ $school->address }}</div>@endif
                <div class="tutorial-print-exam">{{ $exam->name }} · {{ $exam->year ?? ($exam->academicSession->name_en ?? '') }}</div>
                <div class="tutorial-print-title">Result Card</div>
            </td>
            <td class="tutorial-print-header-spacer-cell"></td>
        </tr>
    </table>
</div>
