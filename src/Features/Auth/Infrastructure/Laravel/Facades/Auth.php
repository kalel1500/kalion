<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthFactory;

/**
 * @method static guard(string|null $guard = null)
 * @method static user()
 * @method static \Thehouseofel\Kalion\Features\Auth\Domain\Objects\DataObjects\LoginFieldDto getLoginFieldData()
 * @method static string getClassUserModel()
 * @method static string getClassUserEntity()
 * @method static string getClassUserRepository()
 *
 * @see \Thehouseofel\Kalion\Features\Auth\Infrastructure\Support\AuthenticationFactory
 */
class Auth extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return AuthFactory::class;
    }
}
