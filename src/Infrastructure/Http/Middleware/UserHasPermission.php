<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Thehouseofel\Kalion\Domain\Exceptions\UnauthorizedException;

final class UserHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, $permissions): Response
    {
        if (! auth()->check()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $user = user();

        if (! method_exists($user, 'can')) {
            throw UnauthorizedException::missingTraitHasPermissions($user);
        }

        if (! user()->can($permissions)) {
            throw UnauthorizedException::forPermissions($permissions);
        }

        return $next($request);
    }
}
