@php
    $inkySearchEntries = $inkySearchEntries ?? [];
@endphp

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom admin-topnav-sticky">
    <ul class="navbar-nav align-items-center">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Toggle sidebar">
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

    <ul class="navbar-nav ml-auto align-items-center">
        <li class="nav-item d-flex align-items-center mr-1 mr-md-2">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill d-inline-flex align-items-center gap-2 inky-search-trigger border-gray-300"
                data-toggle="modal" data-target="#inkySearchModal" aria-label="Ask in Seekly">
                <i class="fas fa-search"></i>
                <span class="d-none d-sm-inline">Ask in Seekly</span>
                <kbd class="inky-shortcut">Ctrl+K</kbd>
            </button>
        </li>

        <li class="nav-item d-flex align-items-center mr-2">
            @include('layouts.partials._theme-toggle')
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-label="User menu">
                <img src="{{ auth()->user()->image_url }}" alt="User" class="img-circle" style="width:28px;height:28px;object-fit:cover;">
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <div class="media">
                        <img src="{{ auth()->user()->image_url }}" alt="User Avatar" class="img-size-50 mr-3 img-circle" style="object-fit:cover;">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                {{ auth()->user()->name ?? '' }}
                            </h3>
                            <p class="text-sm">Role</p>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider"></div>

                <a href="{{ route('account.profile.edit') }}" class="dropdown-item">
                    <i class="fas fa-cog mr-2"></i> Profile Setting
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('account.password.edit') }}" class="dropdown-item">
                    <i class="fas fa-key mr-2"></i> Change Password
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item cursor-pointer" id="customer-logout-btn">
                    <i class="fas fa-power-off mr-2"></i> Logout
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
                        <span class="inky-search-badge">Quick Search</span>
                        <h5 class="modal-title font-weight-bold mb-0" id="inkySearchModalLabel">Ask in Seekly</h5>
                        <small class="text-muted">Find modules, pages, and actions</small>
                    </div>
                    <button type="button" class="inky-search-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <div class="modal-body pt-3">
                <label class="sr-only" for="inkySearchInput">Search pages, settings, or entities</label>
                <div class="inky-search-input-wrap">
                    <span class="inky-search-input-icon" aria-hidden="true">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="search" id="inkySearchInput" class="form-control inky-search-input" placeholder="Search pages, settings, or entities..." autocomplete="off">
                </div>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2 mb-2">
                    <small class="text-muted">Type to filter results</small>
                    <small class="text-muted d-none d-sm-inline">Esc to close</small>
                </div>
                <div id="inkySearchResults" class="inky-search-results"></div>
                <div id="inkySearchEmpty" class="inky-search-empty">
                    <div class="inky-search-empty__icon" aria-hidden="true">
                        <i class="fas fa-compass"></i>
                    </div>
                    <div class="inky-search-empty__title">Search results will appear here.</div>
                    <div class="inky-search-empty__text">Try typing a module, page, or action like attendance, student, or reports.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const entries = @json($inkySearchEntries);
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

        function renderResults(query) {
            const normalizedQuery = normalize(query);
            const $results = $(resultsSelector);
            const $empty = $(emptySelector);

            if (!normalizedQuery) {
                $results.empty();
                renderEmptyState(
                    'Search results will appear here.',
                    'Try typing a module, page, or action like attendance, student, or reports.'
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
                    'No matching pages found.',
                    'Try a different module, page, or action.'
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
                'Search results will appear here.',
                'Try typing a module, page, or action like attendance, student, or reports.'
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
    })();
</script>
