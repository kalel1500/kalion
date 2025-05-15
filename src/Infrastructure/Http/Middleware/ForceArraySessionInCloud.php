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
        if (str_contains($request->header('User-Agent', ''), 'kube-probe')) {
            config(['session.driver' => 'array']);
        }

        return $next($request);
    }
}
