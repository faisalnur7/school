@php
    $school = \App\Models\SchoolSetting::current();
    $schoolName = $school->name ?: 'School Name';
    $address = $school->address;
    $contacts = array_filter([$school->contact_number_1, $school->contact_number_2]);
    $reportTitle = $reportTitle ?? null;
@endphp

<div class="card mb-3 report-header-card" style="border:1px solid #e2e8f0;">
    <div class="card-body py-3 px-4 report-header-body" style="width:100%;">
        <div class="report-header-logo" style="width:58px;height:58px;border:1px solid #cbd5e1;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#fff;overflow:hidden;flex-shrink:0;">
            @if(!empty($school->logo))
                <img src="{{ asset($school->logo) }}" alt="{{ $schoolName }} logo" style="max-width:100%;max-height:100%;object-fit:contain;">
            @else
                <span style="font-size:22px;font-weight:700;color:#334155;line-height:1;">{{ strtoupper(substr($schoolName, 0, 1)) }}</span>
            @endif
        </div>

        <div class="report-header-copy">
            <h2 class="mb-1 report-header-title" style="font-size:20px;font-weight:700;color:#0f172a;line-height:1.2;">{{ $schoolName }}</h2>
            @if($address)
                <div class="report-header-address" style="font-size:13px;color:#475569;line-height:1.35;">{{ $address }}</div>
            @endif
            @if(count($contacts))
                <div class="report-header-contacts" style="font-size:13px;color:#334155;margin-top:2px;">{{ implode(' | ', $contacts) }}</div>
            @endif
        </div>
    </div>

    @if(!empty($reportTitle))
        <div class="report-header-print-title">{{ $reportTitle }}</div>
    @endif
</div>

<style>
    .report-header-card .report-header-body {
        display: flex;
        align-items: center;
        gap: 14px;
        width: 100%;
    }

    .report-header-card .report-header-copy {
        min-width: 0;
    }

    .report-header-print-title {
        display: none;
    }

    @media print {
        .report-header-card {
            width: 100% !important;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .report-header-card .report-header-body {
            display: table !important;
            width: 100% !important;
            table-layout: fixed !important;
        }

        .report-header-card .report-header-logo,
        .report-header-card .report-header-copy {
            display: table-cell !important;
            vertical-align: middle !important;
        }

        .report-header-card .report-header-logo {
            width: 58px !important;
            padding-right: 12px !important;
        }

        .report-header-card .report-header-copy {
            min-width: 0 !important;
        }

        .report-header-print-title {
            display: block !important;
            margin: 0.45rem 0 0;
            padding-bottom: 0.35rem;
            width: 100% !important;
            clear: both;
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
            text-align: center;
        }
    }
</style>
