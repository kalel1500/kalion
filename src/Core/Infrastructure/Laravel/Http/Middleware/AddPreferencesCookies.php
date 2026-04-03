<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Thehouseofel\Kalion\Core\Infrastructure\Laravel\Facades\LayoutPreferences;

class AddPreferencesCookies
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        LayoutPreferences::ensureValidCookie();

        return $next($request);
    }
}
