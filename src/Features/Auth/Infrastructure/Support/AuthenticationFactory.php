<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure\Support;

use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Authentication as AuthenticationContract;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthFactory;

/**
 * @mixin \Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Authentication
 */
class AuthenticationFactory implements AuthFactory
{
    protected array $guards = [];

    /**
     * Attempt to get the guard from the local cache.
     *
     * @param  string|null  $name
     * @return \Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Authentication
     */
    public function guard(?string $name = null)
    {
        $name = $name ?? config('auth.defaults.guard');

        if (!isset($this->guards[$name])) {
            $this->guards[$name] = app(AuthenticationContract::class, [$name]);
        }

        return $this->guards[$name];
    }

    public function __call($method, $args)
    {
        return $this->guard()->$method(...$args);
    }
}
