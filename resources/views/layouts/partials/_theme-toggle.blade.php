@php
    $buttonClass = $buttonClass ?? '';
@endphp

<button type="button"
    class="theme-toggle {{ $buttonClass }}"
    data-theme-toggle
    aria-label="Switch to dark mode"
    aria-pressed="false">
    <span class="theme-toggle__icon theme-toggle__icon--moon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="currentColor" role="presentation" focusable="false">
            <path d="M21.64 13.04A9 9 0 1 1 10.96 2.36a1 1 0 0 1 1.22 1.26A7 7 0 0 0 20.4 13.6a1 1 0 0 1 1.24-1.22Z" />
        </svg>
    </span>
    <span class="theme-toggle__icon theme-toggle__icon--sun" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="currentColor" role="presentation" focusable="false">
            <path d="M12 17a5 5 0 1 1 5-5 5.006 5.006 0 0 1-5 5Zm0-8a3 3 0 1 0 3 3 3 3 0 0 0-3-3Zm0-3a1 1 0 0 1-1-1V3a1 1 0 0 1 2 0v2a1 1 0 0 1-1 1Zm0 16a1 1 0 0 1-1-1v-2a1 1 0 0 1 2 0v2a1 1 0 0 1-1 1ZM4.22 5.64a1 1 0 0 1 1.41-1.41l1.42 1.41a1 1 0 0 1-1.42 1.42Zm12.73 12.73a1 1 0 0 1 1.41-1.41l1.42 1.41a1 1 0 0 1-1.42 1.42ZM3 13H1a1 1 0 0 1 0-2h2a1 1 0 0 1 0 2Zm18 0h-2a1 1 0 0 1 0-2h2a1 1 0 0 1 0 2ZM5.64 19.78a1 1 0 0 1-1.41-1.41l1.41-1.42a1 1 0 0 1 1.42 1.42Zm12.73-12.73a1 1 0 0 1-1.41-1.41l1.41-1.42a1 1 0 0 1 1.42 1.42Z" />
        </svg>
    </span>
    <span class="theme-toggle__label" data-theme-toggle-label>Dark</span>
</button>
