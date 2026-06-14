<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
}
