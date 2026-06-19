<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.partials._top-nav', 'layouts.partials._header'], function ($view) {
            $routeName = Route::currentRouteName() ?? '';
            $meta = $this->buildAdminNavigationMeta($routeName);

            $view->with($meta);
            $view->with('inkySearchEntries', $this->buildInkySearchEntries());
        });

        View::composer('pages.*.hub', function ($view) {
            $user = auth()->user();

            if (!$user) {
                return;
            }

            $routePermissions = [];

            $canAccessRoute = function (?string $routeName) use ($user, &$routePermissions): bool {
                if (!$routeName) {
                    return false;
                }

                if (! array_key_exists($routeName, $routePermissions)) {
                    $route = Route::getRoutes()->getByName($routeName);

                    if (!$route) {
                        $routePermissions[$routeName] = [];
                    } else {
                        $routePermissions[$routeName] = collect($route->gatherMiddleware())
                            ->filter(fn ($middleware) => str_starts_with($middleware, 'permission:'))
                            ->flatMap(function ($middleware) {
                                $raw = substr($middleware, strlen('permission:'));

                                return array_map('trim', explode(',', $raw));
                            })
                            ->filter()
                            ->values()
                            ->all();
                    }
                }

                $permissions = $routePermissions[$routeName];

                if (empty($permissions)) {
                    return true;
                }

                return $user->hasAnyPermission($permissions);
            };

            if ($view->offsetExists('cards')) {
                $cards = collect($view->offsetGet('cards'))
                    ->filter(fn ($card) => $canAccessRoute($card['route'] ?? null))
                    ->values()
                    ->all();

                $view->with('cards', $cards);
            }

            if ($view->offsetExists('sections')) {
                $sections = collect($view->offsetGet('sections'))
                    ->map(function ($cards) use ($canAccessRoute) {
                        return collect($cards)
                            ->filter(fn ($card) => $canAccessRoute($card['route'] ?? null))
                            ->values()
                            ->all();
                    })
                    ->filter(fn ($cards) => !empty($cards))
                    ->all();

                $view->with('sections', $sections);
            }
        });
    }

    private function buildAdminNavigationMeta(string $routeName): array
    {
        $pageTitle = '';
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
            if (Str::startsWith($routeName, $prefix)) {
                $hubRoute = $meta['route'];
                $hubLabel = $meta['label'];
                break;
            }
        }

        $segments = $routeName !== '' ? explode('.', $routeName) : [];
        $action = $segments ? end($segments) : '';
        $fallbackEntity = $segments ? Str::headline(str_replace(['-', '_'], ' ', $segments[0])) : 'Dashboard';

        if ($pageTitle === '') {
            if ($action === 'hub' || $action === 'index') {
                $pageTitle = ($hubRoute && $routeName === $hubRoute) ? ($hubLabel ?: $fallbackEntity) : $fallbackEntity;
            } elseif (in_array($action, ['create', 'edit', 'show', 'preview', 'settings', 'filter', 'print', 'pdf'], true)) {
                $entitySegment = count($segments) > 2 ? $segments[count($segments) - 2] : ($segments[0] ?? 'Item');
                $entityLabel = Str::headline(str_replace(['-', '_'], ' ', $entitySegment));

                $pageTitle = match ($action) {
                    'create' => 'Create ' . Str::singular($entityLabel),
                    'edit' => 'Edit ' . Str::singular($entityLabel),
                    'show' => Str::singular($entityLabel) . ' Details',
                    'preview' => Str::singular($entityLabel) . ' Preview',
                    'settings' => $entityLabel . ' Settings',
                    'filter' => $entityLabel . ' Filter',
                    'print' => 'Print ' . Str::singular($entityLabel),
                    'pdf' => Str::singular($entityLabel) . ' PDF',
                    default => Str::headline($action) . ' ' . Str::singular($entityLabel),
                };
            } else {
                $pageTitle = count($segments) > 1
                    ? Str::headline(str_replace(['-', '_'], ' ', $segments[count($segments) - 1]))
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

        return compact('routeName', 'pageTitle', 'hubRoute', 'hubLabel', 'breadcrumbs');
    }

    private function buildInkySearchEntries(): array
    {
        $user = auth()->user();
        $entries = [];
        $seen = [];

        $canAccessRoute = function (?string $routeName) use ($user, &$seen): bool {
            if (! $routeName) {
                return false;
            }

            if (! array_key_exists($routeName, $seen)) {
                $route = Route::getRoutes()->getByName($routeName);

                if (! $route) {
                    $seen[$routeName] = false;
                } else {
                    if ($this->isAlwaysVisibleInkyRoute($routeName)) {
                        $seen[$routeName] = true;
                    } else {
                        $permissions = $this->resolveInkyRoutePermissions($routeName, $route->gatherMiddleware());

                        $seen[$routeName] = empty($permissions)
                            ? false
                            : ($user ? $user->hasAnyPermission($permissions) : false);
                    }
                }
            }

            return $seen[$routeName];
        };

        foreach (Route::getRoutes() as $route) {
            $routeName = $route->getName();
            $middleware = collect($route->gatherMiddleware());
            $methods = collect($route->methods())->map(fn ($method) => strtoupper($method));

            $uri = trim($route->uri(), '/');

            if (
                ! $routeName
                || ! $methods->contains('GET')
                || ! $middleware->contains(fn ($value) => $value === 'auth' || Str::startsWith($value, 'auth:') || Str::startsWith($value, 'permission:'))
                || ! $canAccessRoute($routeName)
                || Str::contains($uri, '{')
                || Str::contains($routeName, ['.pdf', '.print', 'pdf', 'print'])
                || Str::contains($routeName, ['.store', '.update', '.destroy', '.delete', '.toggle-status', '.toggle_status'])
            ) {
                continue;
            }

            $label = $this->buildSearchLabel($routeName, $uri);
            $module = $this->inferSearchModuleLabel($routeName);
            $keywords = $this->buildSearchKeywords($routeName, $uri, $label, $module);

            $entries[$routeName] = [
                'label' => $label,
                'module' => $module,
                'url' => route($routeName),
                'route' => $routeName,
                'uri' => $uri,
                'keywords' => $keywords,
            ];
        }

        return array_values($entries);
    }

    private function isAlwaysVisibleInkyRoute(string $routeName): bool
    {
        return Str::startsWith($routeName, ['account.', 'profile.']);
    }

    private function resolveInkyRoutePermissions(string $routeName, array $middleware): array
    {
        $permissions = collect($middleware)
            ->filter(fn ($value) => str_starts_with($value, 'permission:'))
            ->flatMap(function ($value) {
                $raw = substr($value, strlen('permission:'));

                return array_map('trim', explode(',', $raw));
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (!empty($permissions)) {
            return $permissions;
        }

        $fallbackPermissions = [
            'homepage' => ['view_dashboard'],
            'dashboard' => ['view_dashboard'],
            'admissions.' => ['view_academics'],
            'academics.' => ['view_academics'],
            'analytics.' => ['view_academics'],
            'library.' => ['view_academics'],
            'attendance.' => ['view_attendance'],
            'students.' => ['view_students'],
            'teacher-section-assignments.' => ['view_students'],
            'dormitory.' => ['view_students'],
            'fees.' => ['view_fees'],
            'fee-categories.' => ['view_fees'],
            'fee-sets.' => ['view_fees'],
            'scholarships.' => ['view_fees'],
            'free-studentships.' => ['view_fees'],
            'transports.' => ['view_fees'],
            'financials.' => ['view_financials'],
            'incomes.' => ['view_financials'],
            'expenses.' => ['view_financials'],
            'transactions.' => ['view_financials'],
            'income-categories.' => ['view_financials'],
            'expense-categories.' => ['view_financials'],
            'reports.' => ['view_reports'],
            'shareholders.' => ['view_shareholders'],
            'shareholder-transactions.' => ['view_shareholders'],
            'hr.' => ['view_hr'],
            'accounts.' => ['view_accounts'],
            'account-groups.' => ['view_accounts'],
            'accounts-list.' => ['view_accounts'],
            'ledger.' => ['view_accounts'],
            'accounting-periods.' => ['view_accounts'],
            'journal-entries.' => ['view_accounts'],
            'bank-accounts.' => ['view_accounts'],
            'mobile-banking-accounts.' => ['view_accounts'],
            'hand-cash.' => ['view_accounts'],
            'assets.' => ['view_assets'],
            'asset-categories.' => ['view_assets'],
            'asset-purchases.' => ['view_assets'],
            'asset-issues.' => ['view_assets'],
            'budget.' => ['view_budget'],
            'budget-allocations.' => ['view_budget'],
            'users.' => ['view_users'],
            'roles.' => ['view_users'],
            'permissions.' => ['view_users'],
            'permission-categories.' => ['view_users'],
            'audit-trails.' => ['view_users'],
            'institute.' => ['view_institute_settings'],
            'school-settings.' => ['view_institute_settings'],
            'certificates.' => ['view_institute_settings'],
            'id-card-templates.' => ['view_institute_settings'],
            'buildings.' => ['view_institute_settings'],
            'location.' => ['view_location_settings'],
            'division.' => ['view_location_settings'],
            'district.' => ['view_location_settings'],
            'police-station.' => ['view_location_settings'],
            'post-office.' => ['view_location_settings'],
            'website.' => ['view_website_management'],
            'inventory.' => ['view_inventory'],
            'transport.' => ['manage_transports'],
            'result.' => ['view_results'],
            'results.' => ['view_results'],
            'exams.' => ['manage_exams'],
            'marks.' => ['manage_exams'],
            'onlineexams.' => ['manage_exams'],
            'student-subjects.' => ['manage_student_subjects'],
        ];

        foreach ($fallbackPermissions as $prefix => $permissionsForPrefix) {
            if (Str::startsWith($routeName, $prefix)) {
                return $permissionsForPrefix;
            }
        }

        return [];
    }

    private function buildSearchLabel(string $routeName, string $uri): string
    {
        if (str_starts_with($routeName, 'fees.collect_payment')) {
            return 'Collect Payment';
        }

        if ($routeName === 'fees.collect') {
            return 'Collect Payments';
        }

        $segments = collect(explode('.', $routeName))
            ->map(fn ($segment) => Str::headline(str_replace(['-', '_'], ' ', $segment)))
            ->values()
            ->all();

        $action = strtolower((string) last($segments));
        $action = str_replace(' ', '_', $action);
        $entity = count($segments) > 1 ? $segments[count($segments) - 2] : ($segments[0] ?? 'Page');
        $entitySingular = Str::singular($entity);

        return match ($action) {
            'create' => 'Create ' . $entitySingular,
            'edit' => 'Edit ' . $entitySingular,
            'show' => $entitySingular . ' Details',
            'index' => Str::headline($entity),
            'hub' => Str::headline($entity),
            'filter' => Str::headline($entity) . ' Filter',
            'collect' => 'Collect Payments',
            default => Str::headline(str_replace(['-', '_'], ' ', $uri ?: $routeName)),
        };
    }

    private function inferSearchModuleLabel(string $routeName): string
    {
        $prefixes = [
            'website.' => 'Website',
            'inventory.' => 'Inventory',
            'students.' => 'Students',
            'attendance.' => 'Attendance',
            'fees.' => 'Fees',
            'payments.' => 'Fees',
            'financials.' => 'Financials',
            'expenses.' => 'Financials',
            'incomes.' => 'Financials',
            'hr.' => 'HR',
            'accounts.' => 'Accounts',
            'bank-accounts.' => 'Accounts',
            'mobile-banking-accounts.' => 'Accounts',
            'assets.' => 'Assets',
            'users.' => 'Users',
            'roles.' => 'Users',
            'permissions.' => 'Users',
            'classes.' => 'Academics',
            'sections.' => 'Academics',
            'groups.' => 'Academics',
            'sessions.' => 'Academics',
            'subjects.' => 'Academics',
            'teachers.' => 'Academics',
            'lms.' => 'Academics',
            'lessonplan.' => 'Academics',
            'lessons.' => 'Academics',
            'topics.' => 'Academics',
            'reports.' => 'Reports',
        ];

        foreach ($prefixes as $prefix => $label) {
            if (Str::startsWith($routeName, $prefix)) {
                return $label;
            }
        }

        return Str::headline(explode('.', $routeName)[0] ?? 'Navigation');
    }

    private function buildSearchKeywords(string $routeName, string $uri, string $label, string $module): string
    {
        $keywords = [
            $routeName,
            $uri,
            $label,
            $module,
            str_replace(['.', '-', '_'], ' ', $routeName),
            str_replace(['/', '-', '_'], ' ', $uri),
        ];

        if (Str::contains($routeName, ['fees.collect', 'collect_payment'])) {
            $keywords[] = 'collect';
            $keywords[] = 'collect payment';
            $keywords[] = 'payment collection';
            $keywords[] = 'fee collection';
        }

        if (Str::contains($routeName, ['payments.'])) {
            $keywords[] = 'payment';
            $keywords[] = 'receipt';
            $keywords[] = 'collect';
        }

        return implode(' ', array_unique(array_filter($keywords)));
    }
}
