<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Config;

use Illuminate\Support\Arr;
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
    protected static array $classes  = [
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
    protected static array $registry = [];
    protected static array $priority = [];
    protected static array $scanPackages = [];

    public static function getClasses(): array
    {
        return static::$classes;
    }

    public static function getRegistry(): array
    {
        return static::$registry;
    }

    public static function getOrderedIdentifiers(): array
    {
        $keys = array_keys(static::$registry);

        // Si no hay prioridad definida, devolvemos las keys tal cual
        if (empty(static::$priority)) {
            return $keys;
        }

        // Mapeamos la prioridad a índices [nombre => orden] para búsqueda O(1)
        $priorityOrder = array_flip(static::$priority);

        usort($keys, function ($a, $b) use ($priorityOrder) {
            // Obtenemos la posición o asignamos el final (PHP_INT_MAX)
            $posA = $priorityOrder[$a] ?? PHP_INT_MAX;
            $posB = $priorityOrder[$b] ?? PHP_INT_MAX;

            // El operador <=> devuelve -1, 0 o 1
            return $posA <=> $posB;
        });

        return $keys;
    }

    public static function getScanPackages(): array
    {
        $packages = config('kalion.packages_to_scan_for_jobs');
        $merged = array_merge(
            is_array($packages) ? $packages : explode(';', $packages),
            static::$scanPackages
        );
        return array_filter($merged); // Limpiar valores vacíos (por el explode)
    }

    public static function setPriority(array $priority): void
    {
        static::$priority = $priority;
    }

    public static function override(array $overrides, string $identifier): void
    {
        static::$registry[$identifier] = array_merge(
            static::$registry[$identifier] ?? [],
            $overrides
        );
    }

    public static function apply(): void
    {
        $defaults = self::getClasses();

        foreach (self::getOrderedIdentifiers() as $id) {
            foreach (static::$registry[$id] as $key => $class) {
                if (config($key) === $defaults[$key]) {
                    config([$key => $class]);
                }
            }
        }

        // Finalmente, nos aseguramos de que Laravel reciba la clase final
        config([
            'auth.providers.users.model'     => config('kalion.auth.models.web'),
            'auth.providers.api_users.model' => config('kalion.auth.models.api'),
        ]);
    }

    public static function registerPackagesToScanJobs(string|array $packages): void
    {
        static::$scanPackages = array_merge(
            static::$scanPackages,
            Arr::wrap($packages)
        );
    }
}
