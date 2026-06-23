@php
    $mobileBrandSetting = \App\Models\SchoolSetting::current();
    $mobileBrandLogo = !empty($mobileBrandSetting->logo)
        ? asset($mobileBrandSetting->logo)
        : asset('assets/dist/img/AdminLTELogo.png');

    $mobileBrandName = $mobileBrandSetting->name
        ?: $mobileBrandSetting->short_name
        ?: config('app.name', 'Institute');
@endphp

<div class="mobile-brand-bar d-lg-none">
    <a href="{{ route('dashboard') }}" class="mobile-brand-bar__link" aria-label="{{ $mobileBrandName }}">
        <span class="mobile-brand-bar__logo" aria-hidden="true">
            <img src="{{ $mobileBrandLogo }}" alt="{{ $mobileBrandName }} logo">
        </span>
        <span class="mobile-brand-bar__copy">
            <span class="mobile-brand-bar__name">{{ $mobileBrandName }}</span>
        </span>
    </a>
</div>
