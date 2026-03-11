<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Authentication;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthFactory;

if (! function_exists('kauth')) {
    /**
     * Get the available auth instance.
     *
     * @param  string|null  $guard
     * @return ($guard is null ? \Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthFactory : \Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Authentication)
     */
    function kauth($guard = null): AuthFactory|Authentication
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
     * @return \Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthEntity|null
     */
    function user(string $guard = null)
    {
        return kauth($guard)->user();
    }
}
