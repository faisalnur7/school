<?php

namespace App\Http\Middleware;

use App\Services\MobileAppConfigurationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMobileFeatureAvailable
{
    public function __construct(private readonly MobileAppConfigurationService $configuration) {}

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if (! $this->configuration->isActive()) {
            return response()->json([
                'code' => 'mobile_app_inactive',
                'message' => 'The student mobile app is temporarily unavailable.',
                'maintenance_message' => $this->configuration->current()['maintenance_message'],
            ], 503);
        }

        if (! $this->configuration->isFeatureEnabled($feature)) {
            return response()->json([
                'code' => 'mobile_feature_disabled',
                'message' => 'This mobile app feature is currently unavailable.',
            ], 404);
        }

        return $next($request);
    }
}
