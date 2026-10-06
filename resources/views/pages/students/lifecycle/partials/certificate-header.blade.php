@php($headerLayout = $headerLayout ?? [])
<header class="certificate-header" aria-label="School certificate header">
    @if($setting->slogan)
        <div class="certificate-header-slogan">{{ $setting->slogan }}</div>
    @endif
    <div class="certificate-header-main">
        @if($setting->logo)
            <img class="certificate-header-logo" src="{{ asset($setting->logo) }}" alt="{{ $setting->name ?: 'School logo' }}" style="width: {{ data_get($headerLayout, 'header.logo.width', 48) }}px; height: {{ data_get($headerLayout, 'header.logo.height', 48) }}px;">
        @endif
        <div class="certificate-header-names">
            @if($setting->name_bn)
                <div class="certificate-header-bangla" style="font-family: {{ data_get($headerLayout, 'typography.font_family', 'Arial, Helvetica, sans-serif') }}; font-size: {{ data_get($headerLayout, 'header.bangla.font_size', 29) }}px; font-weight: {{ data_get($headerLayout, 'header.bangla.font_weight', 800) }}; color: {{ data_get($headerLayout, 'header.bangla.color', '#111827') }}; text-align: {{ data_get($headerLayout, 'header.bangla.text_align', 'left') }};">{{ $setting->name_bn }}</div>
            @endif
            @if($setting->name)
                <div class="certificate-header-english" style="font-family: {{ data_get($headerLayout, 'typography.font_family', 'Arial, Helvetica, sans-serif') }}; font-size: {{ data_get($headerLayout, 'header.english.font_size', 17) }}px; font-weight: {{ data_get($headerLayout, 'header.english.font_weight', 800) }}; color: {{ data_get($headerLayout, 'header.english.color', '#111827') }}; text-align: {{ data_get($headerLayout, 'header.english.text_align', 'left') }};">{{ $setting->name }}</div>
            @endif
        </div>
        <div class="certificate-header-meta">
            @if($setting->address_bn)<div>{{ $setting->address_bn }}</div>@endif
            @if($setting->address)<div>{{ $setting->address }}</div>@endif
            @if($setting->school_code)<div><strong>School Code:</strong> {{ $setting->school_code }}</div>@endif
            @if($setting->ipemis_code)<div><strong>IPEMIS Code:</strong> {{ $setting->ipemis_code }}</div>@endif
        </div>
    </div>
</header>
