<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ForceArraySessionInCloud
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = $request->header('User-Agent', '');
        $cloudUserAgent = config('kalion.web_middlewares.force_array_session_in_cloud.cloud_user_agent_value');
        if (str_contains($userAgent, $cloudUserAgent)) {
            config(['session.driver' => 'array']);
        }

        return $next($request);
    }
}
