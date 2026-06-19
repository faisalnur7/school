@php
    $routeName = \Illuminate\Support\Facades\Route::currentRouteName() ?? '';
    $pageTitle = trim((string) $__env->yieldContent('page_title'));
    $hubRoute = null;
    $hubLabel = null;

    $hubMap = [
        'website.cms.' => ['route' => 'website.cms.hub', 'label' => 'Website Management'],
        'website.' => ['route' => 'website.cms.hub', 'label' => 'Website Management'],
        'inventory.sales.' => ['route' => 'inventory.sales.hub', 'label' => 'Inventory Sales Hub'],
        'inventory.' => ['route' => 'inventory.hub', 'label' => 'Inventory'],
        'yearly-final-report.' => ['route' => 'results.hub', 'label' => 'Results'],
        'result.yearly-final-report.' => ['route' => 'results.hub', 'label' => 'Results'],
        'result.progress-report.' => ['route' => 'results.hub', 'label' => 'Results'],
        'result.tutorial-report.' => ['route' => 'results.hub', 'label' => 'Results'],
        'results.' => ['route' => 'results.hub', 'label' => 'Results'],
        'exams.' => ['route' => 'results.hub', 'label' => 'Results'],
        'student-subjects.' => ['route' => 'results.hub', 'label' => 'Results'],
        'admit-seat-cards.' => ['route' => 'results.hub', 'label' => 'Results'],
        'onlineexams.' => ['route' => 'results.hub', 'label' => 'Results'],
        'progress-report.' => ['route' => 'results.hub', 'label' => 'Results'],
        'tutorial-report.' => ['route' => 'results.hub', 'label' => 'Results'],
        'fee-due-report.' => ['route' => 'fees.hub', 'label' => 'Fees'],
        'student-due-report.' => ['route' => 'fees.hub', 'label' => 'Fees'],
        'student-receive-report.' => ['route' => 'fees.hub', 'label' => 'Fees'],
        'student-receivable-report.' => ['route' => 'fees.hub', 'label' => 'Fees'],
        'student-payment-report.' => ['route' => 'fees.hub', 'label' => 'Fees'],
        'student-ledger-report.' => ['route' => 'fees.hub', 'label' => 'Fees'],
        'students.' => ['route' => 'students.hub', 'label' => 'Students'],
        'teacher-section-assignments.' => ['route' => 'academics.hub', 'label' => 'Academics'],
        'teachers.' => ['route' => 'academics.hub', 'label' => 'Academics'],
        'activities.' => ['route' => 'academics.hub', 'label' => 'Academics'],
        'clubs.' => ['route' => 'academics.hub', 'label' => 'Academics'],
        'health.' => ['route' => 'academics.hub', 'label' => 'Academics'],
        'sports.' => ['route' => 'academics.hub', 'label' => 'Academics'],
        'homework.' => ['route' => 'academics.hub', 'label' => 'Academics'],
        'lessons.' => ['route' => 'academics.hub', 'label' => 'Academics'],
        'classrooms.' => ['route' => 'academics.hub', 'label' => 'Academics'],
        'routines.' => ['route' => 'academics.hub', 'label' => 'Academics'],
        'subjects.' => ['route' => 'academics.hub', 'label' => 'Academics'],
        'sessions.' => ['route' => 'academics.hub', 'label' => 'Academics'],
        'groups.' => ['route' => 'academics.hub', 'label' => 'Academics'],
        'sections.' => ['route' => 'academics.hub', 'label' => 'Academics'],
        'classes.' => ['route' => 'academics.hub', 'label' => 'Academics'],
        'attendance.' => ['route' => 'attendance.hub', 'label' => 'Attendance'],
        'fees.' => ['route' => 'fees.hub', 'label' => 'Fees'],
        'payments.' => ['route' => 'fees.hub', 'label' => 'Fees'],
        'transports.' => ['route' => 'fees.hub', 'label' => 'Fees'],
        'free-studentships.' => ['route' => 'fees.hub', 'label' => 'Fees'],
        'scholarships.' => ['route' => 'fees.hub', 'label' => 'Fees'],
        'fee-sets.' => ['route' => 'fees.hub', 'label' => 'Fees'],
        'fee_categories.' => ['route' => 'fees.hub', 'label' => 'Fees'],
        'fee-categories.' => ['route' => 'fees.hub', 'label' => 'Fees'],
        'financials.' => ['route' => 'financials.hub', 'label' => 'Financials'],
        'incomes.' => ['route' => 'financials.hub', 'label' => 'Financials'],
        'expenses.' => ['route' => 'financials.hub', 'label' => 'Financials'],
        'transactions.' => ['route' => 'financials.hub', 'label' => 'Financials'],
        'income-categories.' => ['route' => 'financials.hub', 'label' => 'Financials'],
        'expense-categories.' => ['route' => 'financials.hub', 'label' => 'Financials'],
        'shareholder-transactions.' => ['route' => 'shareholders.hub', 'label' => 'Shareholders'],
        'shareholders.' => ['route' => 'shareholders.hub', 'label' => 'Shareholders'],
        'account-groups.' => ['route' => 'accounts.hub', 'label' => 'Accounts'],
        'accounts-list.' => ['route' => 'accounts.hub', 'label' => 'Accounts'],
        'ledger.' => ['route' => 'accounts.hub', 'label' => 'Accounts'],
        'accounting-periods.' => ['route' => 'accounts.hub', 'label' => 'Accounts'],
        'journal-entries.' => ['route' => 'accounts.hub', 'label' => 'Accounts'],
        'bank-accounts.' => ['route' => 'accounts.hub', 'label' => 'Accounts'],
        'mobile-banking-accounts.' => ['route' => 'accounts.hub', 'label' => 'Accounts'],
        'hand-cash.' => ['route' => 'accounts.hub', 'label' => 'Accounts'],
        'reports.' => ['route' => 'accounts.hub', 'label' => 'Accounts'],
        'assets.' => ['route' => 'assets.hub', 'label' => 'Assets'],
        'asset-categories.' => ['route' => 'assets.hub', 'label' => 'Assets'],
        'asset-purchases.' => ['route' => 'assets.hub', 'label' => 'Assets'],
        'asset-issues.' => ['route' => 'assets.hub', 'label' => 'Assets'],
        'budget.' => ['route' => 'budget.hub', 'label' => 'Budget'],
        'budget-allocations.' => ['route' => 'budget.hub', 'label' => 'Budget'],
        'users.' => ['route' => 'users.hub', 'label' => 'Users'],
        'audit-trails.' => ['route' => 'users.hub', 'label' => 'Users'],
        'roles.' => ['route' => 'users.hub', 'label' => 'Users'],
        'permissions.' => ['route' => 'users.hub', 'label' => 'Users'],
        'permission-categories.' => ['route' => 'users.hub', 'label' => 'Users'],
        'notices.' => ['route' => 'website.cms.hub', 'label' => 'Website Management'],
        'events.' => ['route' => 'website.cms.hub', 'label' => 'Website Management'],
        'generate-id-cards.' => ['route' => 'institute.hub', 'label' => 'Institute'],
        'settings.rooms.' => ['route' => 'institute.hub', 'label' => 'Institute'],
        'settings.buildings.' => ['route' => 'institute.hub', 'label' => 'Institute'],
        'facilities.' => ['route' => 'institute.hub', 'label' => 'Institute'],
        'institute.' => ['route' => 'institute.hub', 'label' => 'Institute'],
        'school-settings.' => ['route' => 'institute.hub', 'label' => 'Institute'],
        'certificates.' => ['route' => 'institute.hub', 'label' => 'Institute'],
        'id-card-templates.' => ['route' => 'institute.hub', 'label' => 'Institute'],
        'buildings.' => ['route' => 'institute.hub', 'label' => 'Institute'],
        'location.' => ['route' => 'location.hub', 'label' => 'Location'],
        'division.' => ['route' => 'location.hub', 'label' => 'Location'],
        'district.' => ['route' => 'location.hub', 'label' => 'Location'],
        'police-station.' => ['route' => 'location.hub', 'label' => 'Location'],
        'post-office.' => ['route' => 'location.hub', 'label' => 'Location'],
        'hr.' => ['route' => 'hr.hub', 'label' => 'HR'],
    ];

    foreach ($hubMap as $prefix => $meta) {
        if (\Illuminate\Support\Str::startsWith($routeName, $prefix)) {
            $hubRoute = $meta['route'];
            $hubLabel = $meta['label'];
            break;
        }
    }

    $segments = $routeName !== '' ? explode('.', $routeName) : [];
    $action = $segments ? end($segments) : '';
    $fallbackEntity = $segments ? \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $segments[0])) : 'Dashboard';

    if ($pageTitle === '') {
        if ($action === 'hub' || $action === 'index') {
            $pageTitle = ($hubRoute && $routeName === $hubRoute) ? ($hubLabel ?: $fallbackEntity) : $fallbackEntity;
        } elseif (in_array($action, ['create', 'edit', 'show', 'preview', 'settings', 'filter', 'print', 'pdf'], true)) {
            $entitySegment = count($segments) > 2 ? $segments[count($segments) - 2] : ($segments[0] ?? 'Item');
            $entityLabel = \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $entitySegment));

            $pageTitle = match ($action) {
                'create' => 'Create ' . \Illuminate\Support\Str::singular($entityLabel),
                'edit' => 'Edit ' . \Illuminate\Support\Str::singular($entityLabel),
                'show' => \Illuminate\Support\Str::singular($entityLabel) . ' Details',
                'preview' => \Illuminate\Support\Str::singular($entityLabel) . ' Preview',
                'settings' => $entityLabel . ' Settings',
                'filter' => $entityLabel . ' Filter',
                'print' => 'Print ' . \Illuminate\Support\Str::singular($entityLabel),
                'pdf' => \Illuminate\Support\Str::singular($entityLabel) . ' PDF',
                default => \Illuminate\Support\Str::headline($action) . ' ' . \Illuminate\Support\Str::singular($entityLabel),
            };
        } else {
            $pageTitle = count($segments) > 1
                ? \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $segments[count($segments) - 1]))
                : $fallbackEntity;
        }
    }

    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('homepage')],
    ];

    if ($hubLabel && $hubRoute) {
        $breadcrumbs[] = ['label' => $hubLabel, 'url' => route($hubRoute)];
    }

    $breadcrumbs[] = ['label' => $pageTitle ?: $fallbackEntity, 'url' => null];
@endphp

<div class="content-header ml-2">
    <div class="container-fluid">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between" style="gap: .5rem;">
            <div style="min-width: 0;">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-1 small">
                        @foreach ($breadcrumbs as $crumb)
                            @if ($loop->last)
                                <li class="breadcrumb-item active text-muted text-truncate" aria-current="page">
                                    {{ $crumb['label'] }}
                                </li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ $crumb['url'] }}" style="text-decoration: none;">{{ $crumb['label'] }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>

                <h1 class="m-0 text-truncate">{{ $pageTitle ?: $fallbackEntity }}</h1>
            </div>

            @if ($hubRoute && $routeName !== $hubRoute)
                <div style="flex-shrink: 0;">
                    <a href="{{ route($hubRoute) }}" class="btn btn-outline-primary btn-sm rounded-pill">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Hub
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
<!-- /.content-header -->
