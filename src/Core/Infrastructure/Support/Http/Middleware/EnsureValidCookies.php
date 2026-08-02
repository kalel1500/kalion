<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Internal\ConfigParser;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cookies\CookieStore;

class EnsureValidCookies
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var ConfigParser $configParser */
        $configParser = app(ConfigParser::class);

        foreach ($configParser->getActiveCookieStoreClasses() as $storeClass) {
            $store = app($storeClass);
            if ($store instanceof CookieStore) {
                $store->ensureValidCookie();
            }
        }

        return $next($request);
    }
}
