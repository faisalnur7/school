<?php

namespace App\Http\Middleware;

use App\Services\StudentAudienceService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentApiAccount
{
    public function __construct(private readonly StudentAudienceService $audience) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || !$user->is_active || !$this->audience->studentForUser($user)) {
            return response()->json(['message' => 'A student account is required.'], 403);
        }
        return $next($request);
    }
}
