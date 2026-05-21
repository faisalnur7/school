@php
    $school = \App\Models\SchoolSetting::current();
    $schoolName = $school->name ?: 'School Name';
    $address = $school->address;
    $contacts = array_filter([$school->contact_number_1, $school->contact_number_2]);
    $logoPath = !empty($school->logo) ? public_path($school->logo) : null;
    $hasLogo = $logoPath && file_exists($logoPath);
@endphp

<div class="school-header-wrap">
    <table class="school-header-table">
        <tr>
            <td class="school-header-logo-cell">
                <div class="school-logo-box">
                    @if($hasLogo)
                        <img src="{{ $logoPath }}" alt="{{ $schoolName }} logo" class="school-logo-img">
                    @else
                        <span class="school-logo-fallback">{{ strtoupper(substr($schoolName, 0, 1)) }}</span>
                    @endif
                </div>
            </td>
            <td class="school-header-info-cell">
                <div class="school-title">{{ $schoolName }}</div>
                @if($address)
                    <div class="school-line">{{ $address }}</div>
                @endif
                @if(count($contacts))
                    <div class="school-line">{{ implode(' | ', $contacts) }}</div>
                @endif
            </td>
        </tr>
    </table>
</div>
