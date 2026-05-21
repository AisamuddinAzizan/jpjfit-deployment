<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $response;
        }

        if (! $request->user()) {
            return $response;
        }

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => sprintf('%s %s', $request->method(), $request->route()?->getName() ?? 'unknown'),
            'method' => $request->method(),
            'route_name' => $request->route()?->getName(),
            'uri' => $request->path(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'context' => [
                'status_code' => $response->getStatusCode(),
            ],
        ]);

        return $response;
    }
}
