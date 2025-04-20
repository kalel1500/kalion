<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static userEntity(string|null $guard = null)
 *
 * @see \Thehouseofel\Kalion\Infrastructure\Services\Auth\CurrentUser
 */
final class AuthService extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'authService';
    }
}
