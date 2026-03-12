<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthFactory;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Guard;

if (! function_exists('kauth')) {
    /**
     * Get the available auth instance.
     *
     * @param  string|null  $guard
     * @return ($guard is null ? \Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthFactory : \Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Guard)
     */
    function kauth($guard = null): AuthFactory|Guard
    {
        if (is_null($guard)) {
            return app(AuthFactory::class);
        }

        return app(AuthFactory::class)->guard($guard);
    }
}

if (! function_exists('user')) {
    /**
     * Get the currently authenticated user entity.
     *
     * @param string|null $guard
     * @return \Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthenticatableEntity|null
     */
    function user(string $guard = null)
    {
        return kauth($guard)->user();
    }
}
