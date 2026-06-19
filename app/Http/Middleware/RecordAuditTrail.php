<?php

namespace App\Http\Middleware;

use App\Models\AuditTrail;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RecordAuditTrail
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user() && $this->shouldRecord($request)) {
            AuditTrail::record(
                $this->resolveActionName($request),
                $this->resolveDescription($request),
                $request->user(),
                [
                    'route_name' => $request->route()?->getName(),
                    'http_method' => $request->method(),
                    'ip_address' => $request->ip(),
                ]
            );
        }

        return $response;
    }

    private function shouldRecord(Request $request): bool
    {
        $routeName = (string) $request->route()?->getName();
        $lastSegment = (string) Str::of($routeName)->afterLast('.');

        return in_array($lastSegment, ['store', 'update'], true);
    }

    private function resolveActionName(Request $request): string
    {
        $routeName = (string) $request->route()?->getName();
        $entity = $this->resolveEntityName($routeName);
        $action = $this->resolveActionVerb($routeName);

        return trim($action.' '.$entity);
    }

    private function resolveDescription(Request $request): string
    {
        $routeName = (string) $request->route()?->getName();
        $entity = $this->resolveEntityName($routeName);
        $singular = Str::singular($entity);
        $action = $this->resolveActionVerb($routeName);

        return match ($action) {
            'Viewed' => "Viewed the {$entity} page.",
            'Opened' => "Opened the {$entity} screen.",
            'Created' => "Created a new {$singular}.",
            'Updated' => "Updated the {$singular}.",
            'Deleted' => "Deleted the {$singular}.",
            'Exported' => "Exported the {$singular} as PDF.",
            'Printed' => "Printed the {$singular}.",
            'Toggled' => "Toggled the {$singular} status.",
            'Resent' => "Resent the {$singular} request.",
            default => "Performed an action on {$entity}.",
        };
    }

    private function resolveActionVerb(string $routeName): string
    {
        $lastSegment = (string) Str::of($routeName)->afterLast('.');

        return match ($lastSegment) {
            'store' => 'Created',
            'update' => 'Updated',
            default => 'Performed',
        };
    }

    private function resolveEntityName(string $routeName): string
    {
        if ($routeName === '') {
            return 'Action';
        }

        $segments = array_values(array_filter(explode('.', $routeName)));
        $lastSegment = end($segments) ?: '';
        $actionSegments = [
            'index', 'show', 'edit', 'create', 'store', 'update', 'destroy', 'delete',
            'toggle', 'toggle-status', 'toggle_status', 'print', 'pdf', 'hub', 'settings',
            'resend',
        ];

        if (in_array($lastSegment, $actionSegments, true) && count($segments) > 1) {
            array_pop($segments);
        }

        $entitySegment = $segments ? end($segments) : $routeName;

        return Str::headline(str_replace(['-', '_'], ' ', $entitySegment));
    }
}
