<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Thehouseofel\Kalion\Domain\Exceptions\UnauthorizedException;

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

        $userEntity = userEntity();

        if (! method_exists($userEntity, 'is')) {
            throw UnauthorizedException::missingTraitHasPermissions($userEntity);
        }

        if (! userEntity()->can($roles)) {
            throw UnauthorizedException::forRoles($roles);
        }

        return $next($request);
    }
}
