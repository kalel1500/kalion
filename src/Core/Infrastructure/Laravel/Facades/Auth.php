<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Auth\Contracts\Authentication as AuthenticationContract;

/**
 * @method static user(string|null $guard = null)
 *
 * @see \Thehouseofel\Kalion\Core\Infrastructure\Support\Auth\AuthenticationService
 */
final class Auth extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return AuthenticationContract::class;
    }
}
