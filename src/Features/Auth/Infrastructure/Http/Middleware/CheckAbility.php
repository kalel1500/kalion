<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\UnauthorizedException;

class CheckAbility
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $method, string $values): Response
    {
        if (! in_array($method, ['is', 'can'])) {
            throw new InvalidValueException(sprintf('The ability "%s" is not supported in %s middleware. Only "is" and "can" are allowed.', $method, __CLASS__));
        }

        if (! auth()->check()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $user = user();

        if (! method_exists($user, $method)) {
            throw UnauthorizedException::missingTraitHasRoles($user);
        }

        $values = str_replace('+', ',', $values);
        if (! $user->$method($values)) {
            throw $this->getException($method, $values);
        }

        return $next($request);
    }

    protected function getException(string $method, $values): UnauthorizedException
    {
        return $method === 'is'
            ? UnauthorizedException::forRoles($values)
            : UnauthorizedException::forPermissions($values);
    }
}
