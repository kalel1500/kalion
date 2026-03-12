<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure;

use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Guard as GuardContract;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthFactory;

/**
 * @mixin \Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Guard
 */
class AuthManager implements AuthFactory
{
    protected array $guards = [];

    /**
     * Attempt to get the guard from the local cache.
     *
     * @param  string|null  $name
     * @return \Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Guard
     */
    public function guard($name = null)
    {
        $name = $name ?? config('auth.defaults.guard');

        if (!isset($this->guards[$name])) {
            $this->guards[$name] = app(GuardContract::class, [$name]);
        }

        return $this->guards[$name];
    }

    public function __call($method, $args)
    {
        return $this->guard()->$method(...$args);
    }
}
