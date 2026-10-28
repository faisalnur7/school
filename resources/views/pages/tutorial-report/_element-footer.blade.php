<table class="tutorial-print-footer">
    <tr><td class="tutorial-print-comment-cell"><div class="tutorial-print-comment"><span class="tutorial-print-comment-label">Comments -</span><span>{{ $data['comment'] ?? '—' }}</span></div></td></tr>
    <tr class="tutorial-print-signature-row"><td colspan="3">
        @if(data_get($signatureSettings, 'visible', true))<table class="tutorial-print-signature-table" align="right">
            <tr><td><div class="tutorial-print-signature-space"></div></td></tr>
            <tr><td>
                @if(data_get($signatureSettings, 'visible', true))
                    <div class="tutorial-print-signature" aria-label="Principal signature">@if($hasPrincipalSignature)<img class="tutorial-print-signature-image" src="{{ $principalSignature }}" alt="Principal signature">@else Principal Signature @endif @if(data_get($signatureSettings, 'show_name', true))<div class="tutorial-print-signature__name">{{ $school->principal_name ?? 'Principal' }}</div>@endif @if(data_get($signatureSettings, 'show_designation', true) && ($school->principal_designation ?? 'Principal') !== ($school->principal_name ?? 'Principal'))<div class="tutorial-print-signature__details">{{ $school->principal_designation ?? 'Principal' }}</div>@endif @if(data_get($signatureSettings, 'show_school_name', true))<div class="tutorial-print-signature__school">{{ str_replace(' ', "\u{00A0}", (string) ($school->principal_school_name ?? $schoolName)) }}</div>@endif</div>
                @endif
            </td></tr>
        </table>@endif
    </td></tr>
</table>
