<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePlatformAdmin
{
    public function handle(Request $request, Closure $next): mixed
    {
        abort_unless($request->user()?->isPlatformAdmin(), 403);

        return $next($request);
    }
}
