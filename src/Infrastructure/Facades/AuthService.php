<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;
use Thehouseofel\Kalion\Domain\Objects\Entities\UserEntity;

/**
 * @method static UserEntity|null userEntity(string|null $guard = null)
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
