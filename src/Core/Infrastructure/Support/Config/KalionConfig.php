<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Config;

use Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Auth\AuthenticationService;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Auth\LoginService;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Auth\PasswordResetService;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Auth\RegisterService;
use Thehouseofel\Kalion\Features\Components\Domain\Services\BaseLayoutData;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\ApiUserEntity;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\UserEntity;
use Thehouseofel\Kalion\Features\Shared\Infrastructure\Models\ApiUser;
use Thehouseofel\Kalion\Features\Shared\Infrastructure\Models\User;
use Thehouseofel\Kalion\Features\Shared\Infrastructure\Repositories\Eloquent\EloquentApiUserRepository;
use Thehouseofel\Kalion\Features\Shared\Infrastructure\Repositories\Eloquent\EloquentUserRepository;

class KalionConfig
{
    /**
     * Estos son los namespaces "de fábrica".
     * Si Kalion cambia de estructura, solo lo cambias aquí.
     */
    public static function classes(): array
    {
        return [
            'kalion.layout.service'               => BaseLayoutData::class,
            'kalion.auth.models.web'              => User::class,
            'kalion.auth.models.api'              => ApiUser::class,
            'kalion.auth.entities.web'            => UserEntity::class,
            'kalion.auth.entities.api'            => ApiUserEntity::class,
            'kalion.auth.repositories.web'        => EloquentUserRepository::class,
            'kalion.auth.repositories.api'        => EloquentApiUserRepository::class,
            'kalion.auth.services.authentication' => AuthenticationService::class,
            'kalion.auth.services.login'          => LoginService::class,
            'kalion.auth.services.register'       => RegisterService::class,
            'kalion.auth.services.password_reset' => PasswordResetService::class,
        ];
    }

    /**
     * Intenta sobrescribir configuraciones de Kalion solo si el usuario
     * no ha definido un valor personalizado en su .env o config/kalion.php
     */
    public static function override(array $overrides, bool $force = false): void
    {
        $defaults     = self::classes();
        $isAppCalling = self::isCallingFromApp();

        foreach ($overrides as $key => $class) {
            if (($force && $isAppCalling) || config($key) === $defaults[$key]) {
                config([$key => $class]);
            }
        }
    }

    protected static function isCallingFromApp(): bool
    {
        $backtrace  = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $callerFile = $backtrace[1]['file'] ?? '';
        return str_starts_with($callerFile, base_path('app'));
    }
}
