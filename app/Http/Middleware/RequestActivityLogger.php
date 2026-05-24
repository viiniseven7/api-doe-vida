<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestActivityLogger
{
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);

        /** @var Response $response */
        $response = $next($request);

        if (!$this->shouldSkip($request)) {
            Log::info('Requisicao processada.', [
                'method' => $request->method(),
                'path' => $request->path(),
                'route' => $request->route()?->getName() ?? $request->route()?->uri(),
                'status' => $response->getStatusCode(),
                'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
                'user_id' => $request->user()?->id,
                'ip' => $request->ip(),
            ]);
        }

        return $response;
    }

    private function shouldSkip(Request $request): bool
    {
        return $request->is('logs') || $request->is('_ignition/*') || $request->is('favicon.ico');
    }
}
