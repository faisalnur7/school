<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): mixed
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $permissionNames = collect($permissions)
            ->flatMap(fn (string $permission) => explode(',', $permission))
            ->map(fn (string $permission) => trim($permission))
            ->filter()
            ->values()
            ->all();

        if (empty($permissionNames)) {
            abort(403, 'Unauthorized.');
        }

        if (!$request->user()->hasAnyPermission($permissionNames)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
