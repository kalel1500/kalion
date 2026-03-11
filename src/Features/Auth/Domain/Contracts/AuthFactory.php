<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Domain\Contracts;

interface AuthFactory
{
    /**
     * Get a guard instance by name.
     *
     * @param  string|null  $name
     * @return \Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Guard
     */
    public function guard($name = null);
}
