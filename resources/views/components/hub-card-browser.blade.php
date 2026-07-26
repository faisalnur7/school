@props([
    'cards' => [],
    'sections' => [],
    'defaultView' => 'medium',
])

@php
    $cards = collect($cards);
    $sections = collect($sections);
    $viewModes = [
        'small' => ['label' => 'Small Grid', 'icon' => 'fa-th'],
        'medium' => ['label' => 'Medium Grid', 'icon' => 'fa-th-large'],
        'large' => ['label' => 'Large Grid', 'icon' => 'fa-border-all'],
        'list' => ['label' => 'List', 'icon' => 'fa-list'],
    ];
    $defaultView = array_key_exists($defaultView, $viewModes) ? $defaultView : 'medium';

    $resolveHref = function (array $card): string {
        if (!empty($card['href'])) {
            return $card['href'];
        }

        if (!empty($card['url'])) {
            return $card['url'];
        }

        $routeName = $card['route'] ?? null;

        if (!$routeName) {
            return '#';
        }

        if (!\Illuminate\Support\Facades\Route::has($routeName)) {
            return '#';
        }

        return route($routeName, $card['params'] ?? []);
    };
@endphp

@once
    <style>
        .hub-browser {
            --hub-card-size: 240px;
            --hub-media-height: 128px;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            width: 100%;
        }

        .hub-browser[data-view="small"] {
            --hub-card-size: 112px;
            --hub-media-height: 64px;
        }

        .hub-browser[data-view="medium"] {
            --hub-card-size: 240px;
            --hub-media-height: 128px;
        }

        .hub-browser[data-view="large"] {
            --hub-card-size: 320px;
            --hub-media-height: 168px;
        }

        .hub-browser[data-view="list"] {
            --hub-card-size: 100%;
            --hub-media-height: auto;
        }

        .hub-browser__toolbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
            width: 100%;
        }

        .hub-browser__switcher {
            display: inline-flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: .35rem;
            padding: .45rem;
            border-radius: 1rem;
            background: rgba(15, 23, 42, .04);
            border: 1px solid rgba(148, 163, 184, .2);
            box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }

        .hub-browser__switcher::-webkit-scrollbar {
            display: none;
        }

        .hub-browser__switcher-label {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .45rem .7rem;
            border-radius: .8rem;
            background: rgba(255, 255, 255, .72);
            color: #475569;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .hub-browser__button {
            appearance: none;
            border: 0;
            border-radius: .8rem;
            background: transparent;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .55rem .8rem;
            font-size: .85rem;
            font-weight: 600;
            line-height: 1;
            transition: background-color .2s ease, color .2s ease, transform .2s ease, box-shadow .2s ease;
            white-space: nowrap;
            cursor: pointer;
            flex: 0 0 auto;
        }

        .hub-browser__button:hover {
            background: rgba(37, 99, 235, .08);
            color: #1d4ed8;
            transform: translateY(-1px);
        }

        .hub-browser__button.is-active {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: #fff;
            box-shadow: 0 10px 20px rgba(37, 99, 235, .25);
        }

        .hub-browser__button i {
            font-size: .9rem;
        }

        .hub-browser__content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .hub-browser__section {
            display: flex;
            flex-direction: column;
            gap: .9rem;
        }

        .hub-browser__section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .hub-browser__section-title {
            margin: 0;
            color: #0f172a;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .hub-browser__section-count {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: .3rem .65rem;
            background: rgba(148, 163, 184, .14);
            color: #475569;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .02em;
            white-space: nowrap;
        }

        .hub-browser__grid {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, var(--hub-card-size)), var(--hub-card-size)));
            gap: 1rem;
            align-items: stretch;
            justify-content: start;
        }

        .hub-browser__card-link {
            display: flex;
            min-width: 0;
            width: 100%;
            text-decoration: none;
            color: inherit;
        }

        .hub-browser__card {
            position: relative;
            width: 100%;
            min-width: 0;
            max-width: var(--hub-card-size);
            overflow: hidden;
            border-radius: 1.25rem;
            background: #fff;
            border: 1px solid rgba(148, 163, 184, .16);
            box-shadow: 0 10px 26px rgba(15, 23, 42, .08);
            display: flex;
            flex-direction: column;
            min-height: 100%;
            aspect-ratio: 1 / 1;
            transition: transform .24s ease, box-shadow .24s ease, border-color .24s ease;
        }

        .hub-browser__card-link:hover .hub-browser__card {
            transform: translateY(-3px);
            box-shadow: 0 18px 38px rgba(15, 23, 42, .14);
            border-color: rgba(59, 130, 246, .25);
        }

        .hub-browser__media {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            height: var(--hub-media-height);
            min-height: var(--hub-media-height);
            flex: 0 0 var(--hub-media-height);
            background: linear-gradient(135deg, var(--hub-card-from, #4f46e5), var(--hub-card-to, #7c3aed));
            color: #fff;
        }

        .hub-browser__badge {
            position: absolute;
            top: .75rem;
            right: .75rem;
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: .32rem .55rem;
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .18);
            color: #fff;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .02em;
            text-transform: uppercase;
            backdrop-filter: blur(10px);
        }

        .hub-browser__body {
            position: relative;
            z-index: 1;
            display: flex;
            flex: 1 1 auto;
            min-height: 0;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .3rem;
            padding: 1rem;
            text-align: center;
        }

        .hub-browser__eyebrow {
            color: #64748b;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .hub-browser__title {
            margin: 0;
            color: #0f172a;
            font-size: .98rem;
            font-weight: 800;
            line-height: 1.35;
        }

        .hub-browser__subtitle {
            margin: 0;
            color: #64748b;
            font-size: .8rem;
            line-height: 1.45;
        }

        .hub-browser[data-view="medium"] .hub-browser__card {
            border-radius: 1.15rem;
        }

        .hub-browser[data-view="medium"] .hub-browser__media {
            min-height: var(--hub-media-height);
            height: var(--hub-media-height);
            padding: 1rem .95rem;
        }

        .hub-browser[data-view="medium"] .hub-browser__media i {
            font-size: 1.75rem !important;
        }

        .hub-browser[data-view="medium"] .hub-browser__body {
            padding: .8rem .9rem .95rem;
            gap: .15rem;
        }

        .hub-browser[data-view="medium"] .hub-browser__title {
            font-size: .9rem;
        }

        .hub-browser[data-view="medium"] .hub-browser__subtitle {
            font-size: .76rem;
        }

        .hub-browser[data-view="large"] .hub-browser__card {
            border-radius: 1.35rem;
        }

        .hub-browser[data-view="large"] .hub-browser__grid {
            grid-template-columns: repeat(auto-fit, minmax(min(100%, var(--hub-card-size)), var(--hub-card-size)));
            gap: 1.1rem;
        }

        .hub-browser[data-view="large"] .hub-browser__media {
            min-height: var(--hub-media-height);
            height: var(--hub-media-height);
            padding: 1.4rem 1.1rem;
        }

        .hub-browser[data-view="large"] .hub-browser__media i {
            font-size: 2.2rem !important;
        }

        .hub-browser[data-view="large"] .hub-browser__body {
            padding: 1.05rem 1.1rem 1.2rem;
            gap: .28rem;
        }

        .hub-browser[data-view="large"] .hub-browser__title {
            font-size: 1rem;
        }

        .hub-browser[data-view="large"] .hub-browser__subtitle {
            font-size: .84rem;
        }

        .hub-browser__arrow {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%) translateX(.1rem);
            opacity: 0;
            color: #94a3b8;
            transition: opacity .2s ease, transform .2s ease;
        }

        .hub-browser__card-link:hover .hub-browser__arrow {
            opacity: 1;
            transform: translateY(-50%) translateX(.35rem);
        }

        .hub-browser[data-view="list"] .hub-browser__grid {
            grid-template-columns: 1fr;
        }

        .hub-browser[data-view="list"] .hub-browser__card {
            flex-direction: row;
            align-items: stretch;
            min-height: 7.2rem;
            max-width: none;
            aspect-ratio: auto;
        }

        .hub-browser[data-view="list"] .hub-browser__media {
            flex: 0 0 6.25rem;
            min-height: auto;
            padding: 1rem;
        }

        .hub-browser[data-view="list"] .hub-browser__body {
            align-items: flex-start;
            justify-content: center;
            padding: 1rem 4rem 1rem 1rem;
            text-align: left;
        }

        .hub-browser[data-view="list"] .hub-browser__arrow {
            opacity: 1;
        }

        .hub-browser[data-view="list"] .hub-browser__title {
            font-size: 1rem;
        }

        .hub-browser[data-view="list"] .hub-browser__subtitle {
            font-size: .86rem;
        }

        .hub-browser[data-view="small"] .hub-browser__card {
            border-radius: 1rem;
            aspect-ratio: 1 / 1;
        }

        .hub-browser[data-view="small"] .hub-browser__grid {
            grid-template-columns: repeat(auto-fit, minmax(min(100%, var(--hub-card-size)), var(--hub-card-size)));
            justify-content: start;
            gap: .75rem;
        }

        .hub-browser[data-view="small"] .hub-browser__media {
            flex: 0 0 var(--hub-media-height);
            height: var(--hub-media-height);
            min-height: var(--hub-media-height);
            padding: .5rem;
        }

        .hub-browser[data-view="small"] .hub-browser__media i {
            font-size: 1.45rem !important;
        }

        .hub-browser[data-view="small"] .hub-browser__body {
            flex: 1 1 auto;
            min-height: 0;
            padding: .45rem .45rem .55rem;
            gap: 0;
        }

        .hub-browser[data-view="small"] .hub-browser__subtitle,
        .hub-browser[data-view="small"] .hub-browser__eyebrow,
        .hub-browser[data-view="small"] .hub-browser__arrow,
        .hub-browser[data-view="small"] .hub-browser__badge {
            display: none;
        }

        .hub-browser[data-view="small"] .hub-browser__title {
            font-size: .78rem;
            line-height: 1.2;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        html[data-theme='dark'] .hub-browser__switcher {
            background: rgba(15, 23, 42, .75);
            border-color: rgba(148, 163, 184, .18);
            box-shadow: 0 12px 30px rgba(2, 6, 23, .28);
        }

        html[data-theme='dark'] .hub-browser__switcher-label {
            background: rgba(15, 23, 42, .78);
            color: #cbd5e1;
        }

        html[data-theme='dark'] .hub-browser__button {
            color: #cbd5e1;
        }

        html[data-theme='dark'] .hub-browser__button:hover {
            background: rgba(37, 99, 235, .16);
            color: #eff6ff;
        }

        html[data-theme='dark'] .hub-browser__card {
            background: #0f172a;
            border-color: rgba(148, 163, 184, .14);
            box-shadow: 0 14px 30px rgba(2, 6, 23, .32);
        }

        html[data-theme='dark'] .hub-browser__title {
            color: #f8fafc;
        }

        html[data-theme='dark'] .hub-browser__subtitle,
        html[data-theme='dark'] .hub-browser__eyebrow {
            color: #cbd5e1;
        }

        html[data-theme='dark'] .hub-browser__section-title {
            color: #f8fafc;
        }

        html[data-theme='dark'] .hub-browser__section-count {
            background: rgba(148, 163, 184, .16);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .hub-browser__arrow {
            color: #94a3b8;
        }

        @media (max-width: 575.98px) {
            .hub-browser {
                --hub-media-height: auto;
            }

            .hub-browser__toolbar {
                justify-content: stretch;
            }

            .hub-browser__switcher {
                width: 100%;
                padding: .3rem;
                gap: .25rem;
            }

            .hub-browser__switcher-label {
                display: none;
            }

            .hub-browser__button {
                flex: 1 1 0;
                justify-content: center;
                padding: .45rem .35rem;
                font-size: .72rem;
                min-width: 0;
            }

            .hub-browser__button span {
                display: none;
            }

            .hub-browser__grid {
                gap: .65rem;
            }

            .hub-browser__card {
                max-width: none;
                min-height: 10rem;
                aspect-ratio: auto;
            }

            .hub-browser[data-view="small"] .hub-browser__grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .hub-browser[data-view="medium"] .hub-browser__grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .hub-browser[data-view="large"] .hub-browser__grid,
            .hub-browser[data-view="list"] .hub-browser__grid {
                grid-template-columns: 1fr;
            }

            .hub-browser[data-view="small"] .hub-browser__media,
            .hub-browser[data-view="medium"] .hub-browser__media,
            .hub-browser[data-view="large"] .hub-browser__media {
                flex: 1 1 50%;
                min-height: 0;
                height: auto;
                padding: .55rem;
            }

            .hub-browser[data-view="small"] .hub-browser__body,
            .hub-browser[data-view="medium"] .hub-browser__body,
            .hub-browser[data-view="large"] .hub-browser__body {
                flex: 1 1 50%;
                justify-content: center;
                padding: .45rem .45rem .55rem;
            }

            .hub-browser[data-view="small"] .hub-browser__title {
                font-size: .72rem;
            }

            .hub-browser[data-view="medium"] .hub-browser__title,
            .hub-browser[data-view="large"] .hub-browser__title {
                font-size: .8rem;
            }

            .hub-browser__button {
                padding: .4rem .6rem;
                font-size: .76rem;
            }
        }
    </style>
@endonce

<div
    class="hub-browser"
    data-hub-browser
    data-hub-default-view="{{ $defaultView }}"
    data-view="{{ $defaultView }}"
>
    <div class="hub-browser__toolbar">
        <div class="hub-browser__switcher" role="group" aria-label="Card view options">
            <span class="hub-browser__switcher-label">
                <i class="fas fa-eye" aria-hidden="true"></i>
                View
            </span>
            @foreach($viewModes as $mode => $config)
                <button
                    type="button"
                    class="hub-browser__button"
                    data-hub-view-option="{{ $mode }}"
                    aria-pressed="false"
                >
                    <i class="fas {{ $config['icon'] }}" aria-hidden="true"></i>
                    <span>{{ $config['label'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <div class="hub-browser__content">
        @if($sections->isNotEmpty())
            @foreach($sections as $sectionName => $sectionCards)
                @php
                    $sectionCards = collect($sectionCards);
                @endphp

                <section class="hub-browser__section">
                    <div class="hub-browser__section-header">
                        <h4 class="hub-browser__section-title">{{ $sectionName }}</h4>
                        <span class="hub-browser__section-count">{{ $sectionCards->count() }} cards</span>
                    </div>

                    <div class="hub-browser__grid">
                        @foreach($sectionCards as $card)
                            @php
                                $href = $resolveHref($card);
                            @endphp
                            <a href="{{ $href }}" class="hub-browser__card-link group" aria-label="{{ $card['title'] ?? 'Open card' }}">
                                <article
                                    class="hub-browser__card"
                                    style="--hub-card-from: {{ $card['from'] ?? '#4f46e5' }}; --hub-card-to: {{ $card['to'] ?? '#7c3aed' }};"
                                >
                                    <div class="hub-browser__media">
                                        @if(!empty($card['badge']))
                                            <span class="hub-browser__badge">{{ $card['badge'] }}</span>
                                        @endif
                                        <i class="fas {{ $card['icon'] ?? 'fa-circle' }} text-3xl transition-transform duration-300 group-hover:scale-110" aria-hidden="true"></i>
                                    </div>

                                    <div class="hub-browser__body">
                                        @if(!empty($card['eyebrow']))
                                            <div class="hub-browser__eyebrow">{{ $card['eyebrow'] }}</div>
                                        @endif
                                        <p class="hub-browser__title">{{ $card['title'] ?? '' }}</p>
                                        @if(!empty($card['subtitle']))
                                            <p class="hub-browser__subtitle">{{ $card['subtitle'] }}</p>
                                        @endif
                                    </div>

                                    <div class="hub-browser__arrow" aria-hidden="true">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                </article>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endforeach
        @else
            <div class="hub-browser__grid">
                @foreach($cards as $card)
                    @php
                        $href = $resolveHref($card);
                    @endphp
                    <a href="{{ $href }}" class="hub-browser__card-link group" aria-label="{{ $card['title'] ?? 'Open card' }}">
                        <article
                            class="hub-browser__card"
                            style="--hub-card-from: {{ $card['from'] ?? '#4f46e5' }}; --hub-card-to: {{ $card['to'] ?? '#7c3aed' }};"
                        >
                            <div class="hub-browser__media">
                                @if(!empty($card['badge']))
                                    <span class="hub-browser__badge">{{ $card['badge'] }}</span>
                                @endif
                                <i class="fas {{ $card['icon'] ?? 'fa-circle' }} text-3xl transition-transform duration-300 group-hover:scale-110" aria-hidden="true"></i>
                            </div>

                            <div class="hub-browser__body">
                                @if(!empty($card['eyebrow']))
                                    <div class="hub-browser__eyebrow">{{ $card['eyebrow'] }}</div>
                                @endif
                                <p class="hub-browser__title">{{ $card['title'] ?? '' }}</p>
                                @if(!empty($card['subtitle']))
                                    <p class="hub-browser__subtitle">{{ $card['subtitle'] }}</p>
                                @endif
                            </div>

                            <div class="hub-browser__arrow" aria-hidden="true">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </article>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var containers = document.querySelectorAll('[data-hub-browser]');
            if (!containers.length) {
                return;
            }

            var modes = ['small', 'medium', 'large', 'list'];
            var mobileQuery = window.matchMedia ? window.matchMedia('(max-width: 575.98px)') : null;

            function isMobile() {
                return !!(mobileQuery && mobileQuery.matches);
            }

            function getStorageKey(container) {
                var baseKey = container.dataset.hubStorageKey || 'hub';
                return 'hub-view-mode:' + baseKey + ':' + (isMobile() ? 'mobile' : 'desktop');
            }

            containers.forEach(function (container) {
                var defaultView = modes.indexOf(container.dataset.hubDefaultView) >= 0
                    ? container.dataset.hubDefaultView
                    : 'medium';
                var buttons = container.querySelectorAll('[data-hub-view-option]');
                var storedView = null;
                var currentStorageKey = getStorageKey(container);

                try {
                    storedView = window.localStorage.getItem(currentStorageKey);
                } catch (error) {
                    storedView = null;
                }

                var initialView = modes.indexOf(storedView) >= 0
                    ? storedView
                    : (isMobile() ? 'small' : defaultView);

                function setView(view) {
                    if (modes.indexOf(view) === -1) {
                        view = defaultView;
                    }

                    container.dataset.view = view;

                    buttons.forEach(function (button) {
                        var active = button.dataset.hubViewOption === view;
                        button.classList.toggle('is-active', active);
                        button.setAttribute('aria-pressed', active ? 'true' : 'false');
                    });

                    try {
                        window.localStorage.setItem(currentStorageKey, view);
                    } catch (error) {
                        // Ignore storage failures in private browsing modes.
                    }
                }

                function refreshForViewportChange() {
                    var nextStorageKey = getStorageKey(container);
                    if (nextStorageKey === currentStorageKey) {
                        return;
                    }

                    currentStorageKey = nextStorageKey;

                    try {
                        storedView = window.localStorage.getItem(currentStorageKey);
                    } catch (error) {
                        storedView = null;
                    }

                    setView(modes.indexOf(storedView) >= 0 ? storedView : (isMobile() ? 'small' : defaultView));
                }

                buttons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        setView(button.dataset.hubViewOption);
                    });
                });

                setView(initialView);

                if (mobileQuery && typeof mobileQuery.addEventListener === 'function') {
                    mobileQuery.addEventListener('change', function () {
                        refreshForViewportChange();
                    });
                } else if (mobileQuery && typeof mobileQuery.addListener === 'function') {
                    mobileQuery.addListener(function () {
                        refreshForViewportChange();
                    });
                }
            });

            window.addEventListener('storage', function (event) {
                document.querySelectorAll('[data-hub-browser]').forEach(function (container) {
                    var currentStorageKey = getStorageKey(container);
                    if (event.key !== currentStorageKey || modes.indexOf(event.newValue) === -1) {
                        return;
                    }

                    var buttons = container.querySelectorAll('[data-hub-view-option]');
                    container.dataset.view = event.newValue;

                    buttons.forEach(function (button) {
                        var active = button.dataset.hubViewOption === event.newValue;
                        button.classList.toggle('is-active', active);
                        button.setAttribute('aria-pressed', active ? 'true' : 'false');
                    });
                });
            });
        });
    </script>
@endonce
