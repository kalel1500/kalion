<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\LoginFieldDto;
use Thehouseofel\Kalion\Infrastructure\Services\Config\Redirect\RedirectDefaultPath;
use Thehouseofel\Kalion\Infrastructure\Services\Config\Redirect\RedirectAfterLogin;

final class Kalion
{
    public static function setLogChannels(): void
    {
        config([
            'logging.channels.queues' => [
                'driver'               => 'single',
                'path'                 => storage_path('logs/queues.log'),
                'level'                => env('LOG_LEVEL', 'debug'),
                'replace_placeholders' => true,
            ],
            'logging.channels.loads'  => [
                'driver'               => 'single',
                'path'                 => storage_path('logs/loads.log'),
                'level'                => env('LOG_LEVEL', 'debug'),
                'replace_placeholders' => true,
            ]
        ]);
    }

    public static function setAuthApiGuards(): void
    {
        if (! config()->has('auth.guards.api')) {
            config([
                'auth.guards.api' => [
                    'driver' => 'session',
                    'provider' => 'api_users',
                ],
            ]);
        }

        $authConfigPath = config_path('auth.php');
        $defaultLine = "'model' => env('AUTH_MODEL', App\\Models\\User::class),";
        if (file_exists($authConfigPath)) {
            $authConfigContents = file_get_contents($authConfigPath);
            if (str_contains($authConfigContents, $defaultLine)) {
                config([
                    'auth.providers.users.model' => env('AUTH_MODEL', \Thehouseofel\Kalion\Infrastructure\Models\User::class),
                ]);
            }
        }

        if (! config()->has('auth.providers.api_users')) {
            config([
                'auth.providers.api_users' => [
                    'driver' => 'eloquent',
                    'model' => env('AUTH_MODEL_API', \Thehouseofel\Kalion\Infrastructure\Models\ApiUser::class),
                ],
            ]);
        }
    }

    public static function getShadowClasses(string $normalShadow = 'shadow-md'): string
    {
        return config('kalion.layout.active_shadows')
            ? 'kal:shadow-xl dark:kal:shadow-black-xl'
            : $normalShadow;
    }

    public static function getLoginFieldData(string $guard = null): LoginFieldDto
    {
        $defaultField = config('kalion.auth.fields.'.get_guard($guard));
        $fields = config('kalion.auth.available_fields');
        $field = $fields[$defaultField] ?? $fields['email'];
        return LoginFieldDto::fromArray([
            'name'        => $field['name'],
            'label'       => $field['label'],
            'type'        => $field['type'],
            'placeholder' => $field['placeholder'],
        ]);
    }

    public static function broadcastingEnabled(): bool
    {
        return config('kalion.broadcasting_enabled');
    }

    public static function broadcastingDisabled(): bool
    {
        return !Kalion::broadcastingEnabled();
    }

    public static function getClassUserModel(string $guard = null): string // |\Illuminate\Foundation\Auth\User
    {
        $provider = config('auth.guards.'.get_guard($guard).'.provider');
        return config('auth.providers.'.$provider.'.model');
    }

    public static function getClassUserEntity(string $guard = null): string
    {
        return config('kalion.auth.entities.'.get_guard($guard));
    }

    public static function getClassUserRepository(string $guard = null): string
    {
        return config('kalion.auth.repositories.'.get_guard($guard));
    }

    public static function getDefaultAuthGuard(): string
    {
        return config('auth.defaults.guard');
    }

    public static function getClassServiceLayout(): string
    {
        return config('kalion.layout.service');
    }

    public static function getClassServiceCurrentUser()
    {
        return config('kalion.auth.services.current_user');
    }

    public static function getClassServiceLogin()
    {
        return config('kalion.auth.services.login');
    }

    public static function getClassServiceRegister()
    {
        return config('kalion.auth.services.register');
    }

    public static function getClassServicePasswordReset()
    {
        return config('kalion.auth.services.password_reset');
    }

    public static function redirectAfterLoginTo(callable|string $redirect): void
    {
        $redirect = is_string($redirect) ? fn () => $redirect : $redirect;
        RedirectAfterLogin::redirectUsing($redirect);
    }

    public static function redirectDefaultPathTo(callable|string $path): void
    {
        $path = is_string($path) ? fn () => $path : $path;
        RedirectDefaultPath::redirectUsing($path);
    }
}
