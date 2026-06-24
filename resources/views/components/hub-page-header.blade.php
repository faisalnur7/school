@props([
    'title',
    'subtitle' => null,
])

@once
    <style>
        .hub-page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            width: 100%;
            padding: .35rem 0 .85rem;
        }

        .hub-page-header__copy {
            min-width: 0;
            flex: 1 1 auto;
        }

        .hub-page-header__title {
            margin: 0;
            color: #0f172a;
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.03em;
        }

        .hub-page-header__subtitle {
            margin: .35rem 0 0;
            color: #64748b;
            font-size: .98rem;
            line-height: 1.5;
            max-width: 56rem;
        }

        .hub-page-header__actions {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: .5rem;
            flex-wrap: wrap;
            margin-top: .1rem;
        }

        html[data-theme='dark'] .hub-page-header__title {
            color: #f8fafc;
        }

        html[data-theme='dark'] .hub-page-header__subtitle {
            color: #cbd5e1;
        }

        @media (max-width: 767.98px) {
            .hub-page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .hub-page-header__title {
                font-size: 1.45rem;
            }

            .hub-page-header__subtitle {
                font-size: .92rem;
                max-width: none;
            }

            .hub-page-header__actions {
                justify-content: flex-start;
            }
        }
    </style>
@endonce

<div class="hub-page-header">
    <div class="hub-page-header__copy">
        <h1 class="hub-page-header__title">{{ $title }}</h1>
        @if(!empty($subtitle))
            <p class="hub-page-header__subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="hub-page-header__actions">
            {{ $actions }}
        </div>
    @endisset
</div>
