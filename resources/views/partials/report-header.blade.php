@php
    $school = \App\Models\SchoolSetting::current();
    $schoolName = $school->name ?: 'School Name';
    $address = $school->address;
    $contacts = array_filter([$school->contact_number_1, $school->contact_number_2]);
@endphp

<div class="card mb-3" style="border:1px solid #e2e8f0;">
    <div class="card-body py-3 px-4 d-flex align-items-center" style="gap:14px;">
        <div style="width:58px;height:58px;border:1px solid #cbd5e1;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#fff;overflow:hidden;flex-shrink:0;">
            @if(!empty($school->logo))
                <img src="{{ asset($school->logo) }}" alt="{{ $schoolName }} logo" style="max-width:100%;max-height:100%;object-fit:contain;">
            @else
                <span style="font-size:22px;font-weight:700;color:#334155;line-height:1;">{{ strtoupper(substr($schoolName, 0, 1)) }}</span>
            @endif
        </div>

        <div class="flex-grow-1">
            <h2 class="mb-1" style="font-size:20px;font-weight:700;color:#0f172a;line-height:1.2;">{{ $schoolName }}</h2>
            @if($address)
                <div style="font-size:13px;color:#475569;line-height:1.35;">{{ $address }}</div>
            @endif
            @if(count($contacts))
                <div style="font-size:13px;color:#334155;margin-top:2px;">{{ implode(' | ', $contacts) }}</div>
            @endif
        </div>
    </div>
</div>
