@php
    $inkySearchEntries = $inkySearchEntries ?? [];
    $currentLocale = app()->getLocale();
    $currentFlag = $currentLocale === 'bn' ? '🇧🇩' : '🇺🇸';
    $timezone = config('app.timezone', 'Asia/Dhaka');
@endphp

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom admin-topnav-sticky">
    <ul class="navbar-nav align-items-center">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="{{ __('Toggle sidebar') }}">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <div class="admin-topnav-breadcrumbs d-none d-md-flex flex-grow-1 align-items-center mx-2" style="min-width: 0;">
        <nav aria-label="breadcrumb" class="w-100">
            <ol class="breadcrumb bg-transparent p-0 mb-0 small admin-breadcrumb-list">
                @foreach ($breadcrumbs ?? [] as $crumb)
                    @if ($loop->last)
                        <li class="breadcrumb-item active text-truncate text-muted" aria-current="page">
                            {{ $crumb['label'] }}
                        </li>
                    @else
                        <li class="breadcrumb-item text-truncate">
                            <a href="{{ $crumb['url'] }}" class="admin-breadcrumb-link">{{ $crumb['label'] }}</a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>

    <div class="admin-topnav-datetime d-none d-lg-flex align-items-center mr-2">
        <div class="d-flex align-items-center text-right admin-topnav-datetime__inner">
            <span class="admin-topnav-datetime__icon" aria-hidden="true">
                <i class="far fa-clock"></i>
            </span>
            <span id="topNavClock" class="font-weight-bold small admin-topnav-datetime__clock">
                {{ now($timezone)->format('h:i:s A') }}
            </span>
            <span class="admin-topnav-datetime__separator" aria-hidden="true"></span>
            <span class="admin-topnav-datetime__icon" aria-hidden="true">
                <i class="far fa-calendar-alt"></i>
            </span>
            <span id="topNavDate" class="admin-topnav-datetime__date">
                {{ now($timezone)->format('D, M d, Y') }}
            </span>
        </div>
    </div>

    <ul class="navbar-nav ml-auto align-items-center topnav-actions">
        <li class="nav-item d-flex align-items-center mr-1 mr-md-2">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill d-inline-flex align-items-center gap-2 inky-search-trigger border-gray-300"
                data-toggle="modal" data-target="#inkySearchModal" aria-label="{{ __('Ask in Seekly') }}">
                <i class="fas fa-search"></i>
                <span class="d-none d-sm-inline">{{ __('Ask in Seekly') }}</span>
                <kbd class="inky-shortcut">Ctrl+K</kbd>
            </button>
        </li>

        <li class="nav-item dropdown ml-2">
            <a class="nav-link dropdown-toggle topnav-lang-toggle d-inline-flex align-items-center" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" aria-label="{{ __('Language') }}">
                <span class="topnav-lang-toggle__flag" aria-hidden="true">{{ $currentFlag }}</span>
                <i class="fas fa-chevron-down topnav-lang-toggle__caret" aria-hidden="true"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right topnav-lang-menu shadow-lg border-0 p-2">
                <a class="dropdown-item d-flex align-items-center topnav-lang-item {{ $currentLocale === 'en' ? 'active' : '' }}" href="{{ route('locale.switch', ['locale' => 'en']) }}" aria-label="{{ __('Switch to English') }}">
                    <span class="topnav-lang-item__flag" aria-hidden="true">🇺🇸</span>
                    <span class="topnav-lang-item__label">{{ __('English') }}</span>
                </a>
                <a class="dropdown-item d-flex align-items-center topnav-lang-item {{ $currentLocale === 'bn' ? 'active' : '' }}" href="{{ route('locale.switch', ['locale' => 'bn']) }}" aria-label="{{ __('Switch to Bangla') }}">
                    <span class="topnav-lang-item__flag" aria-hidden="true">🇧🇩</span>
                    <span class="topnav-lang-item__label">{{ __('Bangla') }}</span>
                </a>
            </div>
        </li>

        <li class="nav-item d-flex align-items-center mr-3">
            @include('layouts.partials._theme-toggle', ['buttonClass' => 'topnav-theme-toggle'])
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-label="{{ __('User menu') }}">
                <img src="{{ auth()->user()->image_url }}" alt="{{ __('User') }}" class="img-circle" style="width:28px;height:28px;object-fit:cover;">
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right topnav-user-menu shadow-lg border-0 p-2">
                <a href="#" class="dropdown-item topnav-user-menu__profile">
                    <div class="media align-items-center">
                        <img src="{{ auth()->user()->image_url }}" alt="{{ __('User Avatar') }}" class="img-size-50 mr-3 img-circle topnav-user-menu__avatar" style="object-fit:cover;">
                        <div class="media-body">
                            <h3 class="dropdown-item-title topnav-user-menu__name">
                                {{ auth()->user()->name ?? '' }}
                            </h3>
                            <p class="text-sm topnav-user-menu__role">{{ __('Role') }}</p>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider topnav-user-menu__divider"></div>

                <a href="{{ route('account.profile.edit') }}" class="dropdown-item topnav-user-menu__action">
                    <i class="fas fa-cog mr-2"></i> {{ __('Profile Setting') }}
                </a>
                <div class="dropdown-divider topnav-user-menu__divider"></div>
                <a href="{{ route('account.password.edit') }}" class="dropdown-item topnav-user-menu__action">
                    <i class="fas fa-key mr-2"></i> {{ __('Change Password') }}
                </a>
                <div class="dropdown-divider topnav-user-menu__divider"></div>
                <a class="dropdown-item cursor-pointer topnav-user-menu__action topnav-user-menu__action--danger" id="customer-logout-btn">
                    <i class="fas fa-power-off mr-2"></i> {{ __('Logout') }}
                </a>

                <form id="customer_logout_form" style="display:none" action="{{ route('logout') }}" method="POST">
                    @csrf
                </form>
                <script>
                    $(document).on('click', '#customer-logout-btn', function() {
                        $("#customer_logout_form").submit();
                    })
                </script>
            </div>
        </li>
    </ul>
</nav>

<div class="modal fade inky-search-modal-root" id="inkySearchModal" tabindex="-1" role="dialog" aria-labelledby="inkySearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg inky-search-dialog" role="document">
            <div class="modal-content border-0 shadow-lg inky-search-modal">
                <div class="modal-header border-0 pb-0">
                    <div class="inky-search-heading">
                        <span class="inky-search-badge">{{ __('Quick Search') }}</span>
                        <h5 class="modal-title font-weight-bold mb-0" id="inkySearchModalLabel">{{ __('Ask in Seekly') }}</h5>
                        <small class="text-muted">{{ __('Find modules, pages, and actions') }}</small>
                    </div>
                    <button type="button" class="inky-search-close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <div class="modal-body pt-3">
                <label class="sr-only" for="inkySearchInput">{{ __('Search pages, settings, or entities') }}</label>
                <div class="inky-search-input-wrap">
                    <span class="inky-search-input-icon" aria-hidden="true">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="search" id="inkySearchInput" class="form-control inky-search-input" placeholder="{{ __('Search pages, settings, or entities...') }}" autocomplete="off">
                </div>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2 mb-2">
                    <small class="text-muted">{{ __('Type to filter results') }}</small>
                    <small class="text-muted d-none d-sm-inline">{{ __('Esc to close') }}</small>
                </div>
                <div id="inkySearchResults" class="inky-search-results"></div>
                <div id="inkySearchEmpty" class="inky-search-empty">
                    <div class="inky-search-empty__icon" aria-hidden="true">
                        <i class="fas fa-compass"></i>
                    </div>
                    <div class="inky-search-empty__title">{{ __('Search results will appear here.') }}</div>
                    <div class="inky-search-empty__text">{{ __('Try typing a module, page, or action like attendance, student, or reports.') }}</div>
                </div>
            </div>
        </div>
</div>
</div>

<style>
    .admin-topnav-datetime {
        min-width: 260px;
        min-height: 38px;
        padding: 0.45rem 0.85rem;
        border-radius: 999px;
        border: 1px solid rgba(191, 219, 254, 0.9);
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(239, 246, 255, 0.9) 100%);
        box-shadow: 0 12px 26px rgba(15, 23, 42, 0.08);
        color: #0f172a;
        backdrop-filter: blur(14px);
    }

    .admin-topnav-datetime__inner {
        gap: 0.5rem;
        white-space: nowrap;
    }

    .admin-topnav-datetime__icon {
        color: #2563eb;
        font-size: 0.88rem;
        line-height: 1;
    }

    .admin-topnav-datetime__clock,
    .admin-topnav-datetime__date {
        color: #0f172a;
        line-height: 1;
    }

    .admin-topnav-datetime__date {
        font-size: 0.8rem;
        font-weight: 600;
    }

    .admin-topnav-datetime__separator {
        width: 1px;
        height: 18px;
        background: rgba(37, 99, 235, 0.18);
    }

    .topnav-actions {
        gap: 0.95rem;
    }

    .topnav-actions > .nav-item {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    .topnav-flag {
        font-size: 1.05rem;
        line-height: 1;
    }

    .topnav-lang-toggle {
        min-width: 3.1rem;
        min-height: 38px;
        padding: 0.4rem 0.72rem !important;
        border-radius: 999px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(239, 246, 255, 0.92) 100%);
        border: 1px solid rgba(191, 219, 254, 0.9);
        box-shadow: 0 12px 26px rgba(15, 23, 42, 0.08);
        gap: 0.3rem;
        justify-content: center;
        backdrop-filter: blur(14px);
    }

    .topnav-lang-toggle:hover,
    .topnav-lang-toggle:focus {
        background: linear-gradient(135deg, rgba(239, 246, 255, 0.98) 0%, rgba(219, 234, 254, 0.96) 100%);
        border-color: #bfdbfe;
        box-shadow: 0 14px 28px rgba(37, 99, 235, 0.12);
    }

    .topnav-lang-toggle::after {
        display: none;
    }

    .topnav-lang-toggle__flag {
        font-size: 1.05rem;
        line-height: 1;
    }

    .topnav-lang-toggle__caret {
        font-size: 0.62rem;
        color: #64748b;
    }

    .topnav-lang-menu {
        min-width: 7rem;
        padding: 0.45rem;
        border-radius: 18px;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(16px);
    }

    .topnav-lang-item {
        min-height: 2.4rem;
        justify-content: flex-start;
        border-radius: 12px;
        gap: 0.6rem;
        padding: 0.6rem 0.85rem;
        transition: background-color .15s ease, transform .15s ease, box-shadow .15s ease;
    }

    .topnav-lang-item + .topnav-lang-item {
        margin-top: 0.2rem;
    }

    .topnav-lang-item:hover,
    .topnav-lang-item:focus,
    .topnav-lang-item.active {
        background: rgba(59, 130, 246, 0.12);
        box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.12);
        transform: translateY(-1px);
    }

    .topnav-lang-item__flag {
        font-size: 1rem;
        line-height: 1;
    }

    .topnav-lang-item__label {
        color: #0f172a;
        font-size: 0.86rem;
        font-weight: 600;
        line-height: 1;
        letter-spacing: 0.01em;
    }

    html[data-theme='dark'] .admin-topnav-datetime {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.98) 0%, rgba(30, 41, 59, 0.96) 100%);
        border-color: rgba(96, 165, 250, 0.22);
        box-shadow: 0 14px 30px rgba(2, 6, 23, 0.5);
    }

    html[data-theme='dark'] .admin-topnav-datetime__icon {
        color: #93c5fd;
    }

    html[data-theme='dark'] .admin-topnav-datetime__clock,
    html[data-theme='dark'] .admin-topnav-datetime__date {
        color: #f8fafc;
    }

    html[data-theme='dark'] .admin-topnav-datetime__separator {
        background: rgba(96, 165, 250, 0.42);
    }

    html[data-theme='dark'] .topnav-lang-toggle {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.98) 0%, rgba(30, 41, 59, 0.96) 100%);
        border-color: rgba(96, 165, 250, 0.22);
        box-shadow: 0 14px 30px rgba(2, 6, 23, 0.45);
    }

    html[data-theme='dark'] .topnav-lang-toggle:hover,
    html[data-theme='dark'] .topnav-lang-toggle:focus {
        background: linear-gradient(135deg, rgba(30, 41, 59, 1) 0%, rgba(15, 23, 42, 0.98) 100%);
        border-color: rgba(96, 165, 250, 0.5);
        box-shadow: 0 16px 32px rgba(2, 6, 23, 0.5);
    }

    html[data-theme='dark'] .topnav-lang-toggle__caret {
        color: #cbd5e1;
    }

    html[data-theme='dark'] .topnav-lang-menu {
        background: rgba(15, 23, 42, 0.99);
        border-color: rgba(96, 165, 250, 0.18);
        box-shadow: 0 18px 36px rgba(2, 6, 23, 0.55);
    }

    html[data-theme='dark'] .topnav-lang-item:hover,
    html[data-theme='dark'] .topnav-lang-item:focus,
    html[data-theme='dark'] .topnav-lang-item.active {
        background: rgba(59, 130, 246, 0.24);
        box-shadow: inset 0 0 0 1px rgba(96, 165, 250, 0.24);
    }

    html[data-theme='dark'] .topnav-lang-item__label {
        color: #f8fafc;
    }

    .topnav-user-menu {
        min-width: 16rem;
        padding: 0.55rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(18px);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.12);
        overflow: hidden;
    }

    .topnav-user-menu__profile {
        border-radius: 0.9rem;
        padding: 0.8rem 0.85rem;
        margin-bottom: 0.3rem;
        background: linear-gradient(135deg, rgba(239, 246, 255, 0.8) 0%, rgba(255, 255, 255, 0.96) 100%);
    }

    .topnav-user-menu__profile:hover,
    .topnav-user-menu__profile:focus {
        background: linear-gradient(135deg, rgba(224, 242, 254, 0.95) 0%, rgba(255, 255, 255, 1) 100%);
    }

    .topnav-user-menu__avatar {
        width: 3rem;
        height: 3rem;
        border: 2px solid rgba(191, 219, 254, 0.8);
        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.08);
    }

    .topnav-user-menu__name {
        margin-bottom: 0.25rem;
        color: #0f172a;
        font-size: 0.95rem;
        font-weight: 700;
        line-height: 1.15;
    }

    .topnav-user-menu__role {
        margin-bottom: 0;
        color: #64748b;
        font-size: 0.78rem;
        line-height: 1.1;
    }

    .topnav-user-menu__divider {
        margin: 0.35rem 0;
        border-top-color: rgba(226, 232, 240, 0.9);
    }

    .topnav-user-menu__action {
        border-radius: 0.8rem;
        padding: 0.75rem 0.85rem;
        color: #0f172a;
        font-weight: 600;
        transition: background-color 0.15s ease, transform 0.15s ease, color 0.15s ease;
    }

    .topnav-user-menu__action:hover,
    .topnav-user-menu__action:focus {
        background: rgba(59, 130, 246, 0.08);
        color: #0f172a;
        transform: translateY(-1px);
    }

    .topnav-user-menu__action--danger:hover,
    .topnav-user-menu__action--danger:focus {
        background: rgba(239, 68, 68, 0.1);
        color: #b91c1c;
    }

    html[data-theme='dark'] .topnav-user-menu {
        background: rgba(15, 23, 42, 0.98);
        box-shadow: 0 20px 40px rgba(2, 6, 23, 0.55);
        border: 1px solid rgba(51, 65, 85, 0.8);
    }

    html[data-theme='dark'] .topnav-user-menu__profile {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.96) 100%);
    }

    html[data-theme='dark'] .topnav-user-menu__profile:hover,
    html[data-theme='dark'] .topnav-user-menu__profile:focus {
        background: linear-gradient(135deg, rgba(51, 65, 85, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%);
    }

    html[data-theme='dark'] .topnav-user-menu__avatar {
        border-color: rgba(96, 165, 250, 0.35);
    }

    html[data-theme='dark'] .topnav-user-menu__name {
        color: #f8fafc;
    }

    html[data-theme='dark'] .topnav-user-menu__role {
        color: #cbd5e1;
    }

    html[data-theme='dark'] .topnav-user-menu__divider {
        border-top-color: rgba(51, 65, 85, 0.85);
    }

    html[data-theme='dark'] .topnav-user-menu__action {
        color: #e2e8f0;
    }

    html[data-theme='dark'] .topnav-user-menu__action:hover,
    html[data-theme='dark'] .topnav-user-menu__action:focus {
        background: rgba(59, 130, 246, 0.14);
        color: #f8fafc;
    }

    html[data-theme='dark'] .topnav-user-menu__action--danger:hover,
    html[data-theme='dark'] .topnav-user-menu__action--danger:focus {
        background: rgba(239, 68, 68, 0.18);
        color: #fecaca;
    }

    @media (max-width: 991.98px) {
        .topnav-actions {
            gap: 0.7rem;
        }
    }

    @media (max-width: 576px) {
        .topnav-actions {
            gap: 0.5rem;
        }
    }
</style>

<script>
    (function () {
        const entries = @json($inkySearchEntries);
        const timezone = @json($timezone);
        const modalSelector = '#inkySearchModal';
        const inputSelector = '#inkySearchInput';
        const resultsSelector = '#inkySearchResults';
        const emptySelector = '#inkySearchEmpty';
        const triggerSelector = '.inky-search-trigger';

        function escapeHtml(value) {
            return String(value || '').replace(/[&<>"']/g, function (char) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    '\'': '&#39;',
                })[char];
            });
        }

        function renderEmptyState(title, description) {
            const markup = [
                '<div class="inky-search-empty__icon" aria-hidden="true"><i class="fas fa-compass"></i></div>',
                '<div class="inky-search-empty__title">' + escapeHtml(title) + '</div>',
            ];

            if (description) {
                markup.push('<div class="inky-search-empty__text">' + escapeHtml(description) + '</div>');
            }

            $(emptySelector).removeClass('d-none').html(markup.join(''));
        }

        function normalize(value) {
            return (value || '')
                .toString()
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();
        }

        function scoreEntry(entry, query) {
            const haystack = normalize([
                entry.label,
                entry.module,
                entry.route,
                entry.uri,
                entry.keywords,
            ].join(' '));

            if (!haystack.includes(query)) {
                return 0;
            }

            let score = 0;
            const label = normalize(entry.label);
            const module = normalize(entry.module);
            const route = normalize(entry.route);
            const uri = normalize(entry.uri);

            if (label === query) score += 120;
            if (label.startsWith(query)) score += 80;
            if (label.includes(query)) score += 60;
            if (module.includes(query)) score += 28;
            if (route.includes(query)) score += 24;
            if (uri.includes(query)) score += 18;

            query.split(' ').forEach(function (term) {
                if (!term) {
                    return;
                }

                if (label.includes(term)) score += 18;
                if (module.includes(term)) score += 10;
                if (route.includes(term)) score += 8;
                if (uri.includes(term)) score += 6;
            });

            return score;
        }

        function updateTopNavClock() {
            const now = new Date();
            const clockEl = document.getElementById('topNavClock');
            const dateEl = document.getElementById('topNavDate');

            const timeFormatter = new Intl.DateTimeFormat('en-US', {
                timeZone: timezone,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true,
            });

            const dateFormatter = new Intl.DateTimeFormat('en-US', {
                timeZone: timezone,
                weekday: 'short',
                month: 'short',
                day: '2-digit',
                year: 'numeric',
            });

            if (clockEl) {
                clockEl.textContent = timeFormatter.format(now);
            }

            if (dateEl) {
                dateEl.textContent = dateFormatter.format(now);
            }
        }

        function renderResults(query) {
            const normalizedQuery = normalize(query);
            const $results = $(resultsSelector);
            const $empty = $(emptySelector);

            if (!normalizedQuery) {
                $results.empty();
                renderEmptyState(
                    @js(__('Search results will appear here.')),
                    @js(__('Try typing a module, page, or action like attendance, student, or reports.'))
                );
                return;
            }

            const matches = entries
                .map(function (entry) {
                    return Object.assign({}, entry, { score: scoreEntry(entry, normalizedQuery) });
                })
                .filter(function (entry) {
                    return entry.score > 0;
                })
                .sort(function (a, b) {
                    return b.score - a.score || a.label.localeCompare(b.label);
                })
                .slice(0, 12);

            $results.empty();

            if (!matches.length) {
                renderEmptyState(
                    @js(__('No matching pages found.')),
                    @js(__('Try a different module, page, or action.'))
                );
                return;
            }

            $empty.addClass('d-none');

            matches.forEach(function (entry) {
                const $item = $('<button>', {
                    type: 'button',
                    class: 'inky-search-item',
                    'data-url': entry.url,
                });

                const $icon = $('<span>', { class: 'inky-search-item__icon', 'aria-hidden': 'true' })
                    .append($('<i>', { class: 'fas fa-arrow-right' }));
                const $text = $('<div>', { class: 'inky-search-item__text' });
                $text.append($('<div>', { class: 'inky-search-item__title text-truncate', text: entry.label }));
                $text.append($('<small>', { class: 'inky-search-item__meta text-truncate', text: `${entry.module} · ${entry.route}` }));

                const $badge = $('<span>', { class: 'inky-search-item__badge', text: entry.module });

                $item.append($icon).append($text).append($badge);
                $results.append($item);
            });
        }

        $(document).on('click', triggerSelector, function () {
            $(modalSelector).modal('show');
        });

        $(document).on('shown.bs.modal', modalSelector, function () {
            const $input = $(inputSelector);
            $input.trigger('focus');
            $input.val('').trigger('input');
        });

        $(document).on('hidden.bs.modal', modalSelector, function () {
            $(inputSelector).val('');
            $(resultsSelector).empty();
            renderEmptyState(
                @js(__('Search results will appear here.')),
                @js(__('Try typing a module, page, or action like attendance, student, or reports.'))
            );
        });

        $(document).on('input', inputSelector, function () {
            renderResults($(this).val());
        });

        $(document).on('click', '.inky-search-item', function () {
            const url = $(this).data('url');
            if (url) {
                window.location.href = url;
            }
        });

        $(document).on('keydown', function (event) {
            const isK = event.key && event.key.toLowerCase() === 'k';
            if ((event.ctrlKey || event.metaKey) && isK) {
                event.preventDefault();
                $(modalSelector).modal('show');
            }
        });

        $(document).on('keydown', inputSelector, function (event) {
            if (event.key === 'Escape') {
                $(modalSelector).modal('hide');
            }
        });

        updateTopNavClock();
        if (!window.__schoolTopNavClockTimer) {
            window.__schoolTopNavClockTimer = window.setInterval(updateTopNavClock, 1000);
        }
    })();
</script>
