<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Features\Auth\Infrastructure\Laravel\Facades\Auth;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\ApiUserEntity;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\UserEntity;

if (! function_exists('user')) {
    /**
     * Get the currently authenticated user entity.
     *
     * @param string|null $guard
     * @return UserEntity|ApiUserEntity|null
     */
    function user(string $guard = null)
    {
        return Auth::user($guard);
    }
}
