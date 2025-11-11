<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Thehouseofel\Kalion\Core\Domain\Exceptions\UnauthorizedException;

final class UserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        if (! auth()->check()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $user = user();

        if (! method_exists($user, 'is')) {
            throw UnauthorizedException::missingTraitHasPermissions($user);
        }

        if (! user()->is($roles)) {
            throw UnauthorizedException::forRoles($roles);
        }

        return $next($request);
    }
}
