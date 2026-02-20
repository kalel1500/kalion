<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Config;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\LoginFieldDto;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Config\Redirect\RedirectAfterLogin;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Config\Redirect\RedirectDefaultPath;

final class Kalion
{
    public const ENUM_NULL_VALUE = 'k_null';

    public static function getShadowClasses(string $normalShadow = 'shadow-md'): string
    {
        return config('kalion.layout.active_shadows')
            ? 'kal:shadow-xl dark:kal:shadow-black-xl'
            : $normalShadow;
    }

    public static function getLoginFieldData(string $guard = null): LoginFieldDto
    {
        $defaultField = config('kalion.auth.fields.' . get_guard($guard));
        $fields       = config('kalion.auth.available_fields');
        $field        = $fields[$defaultField] ?? $fields['email'];
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
        return ! Kalion::broadcastingEnabled();
    }

    public static function getClassUserModel(string $guard = null): string // |\Illuminate\Foundation\Auth\User
    {
        $provider = config('auth.guards.' . get_guard($guard) . '.provider');
        return config('auth.providers.' . $provider . '.model');
    }

    public static function getClassUserEntity(string $guard = null): string
    {
        return config('kalion.auth.entities.' . get_guard($guard));
    }

    public static function getClassUserRepository(string $guard = null): string
    {
        return config('kalion.auth.repositories.' . get_guard($guard));
    }

    public static function getDefaultAuthGuard(): string
    {
        return config('auth.defaults.guard');
    }

    public static function getClassServiceLayout(): string
    {
        return config('kalion.layout.service');
    }

    public static function getClassServiceAuthentication()
    {
        return config('kalion.auth.services.authentication');
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

    public static function shouldCacheProcessStatus(): bool
    {
        return config('kalion.process.status_should_use_cache');
    }

    public static function redirectAfterLoginTo(callable|string $redirect): void
    {
        $redirect = is_string($redirect) ? fn() => $redirect : $redirect;
        RedirectAfterLogin::redirectUsing($redirect);
    }

    public static function redirectDefaultPathTo(callable|string $path): void
    {
        $path = is_string($path) ? fn() => $path : $path;
        RedirectDefaultPath::redirectUsing($path);
    }
}
