<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Config;

use Composer\InstalledVersions;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\LoginFieldDto;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Config\Redirect\RedirectAfterLogin;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Config\Redirect\RedirectDefaultPath;

class Kalion
{
    public const ENUM_NULL_VALUE = 'k_null';

    public static function getShadowClasses(string $normalShadow = 'shadow-md'): string
    {
        return config('kalion.layout.use_elevated_shadows')
            ? 'kal:shadow-xl dark:kal:shadow-black-xl'
            : $normalShadow;
    }

    public static function getLoginFieldData(string $guard = null): LoginFieldDto
    {
        $defaultField = config('kalion.auth.fields.' . get_guard($guard));
        $fields       = config('kalion.auth.available_fields');
        $field        = $fields[$defaultField] ?? $fields['email'];
        return new LoginFieldDto(
            name       : $field['name'],
            label      : $field['label'],
            type       : $field['type'],
            placeholder: $field['placeholder'],
        );
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

    public static function getInstalledVersion(): string
    {
        return InstalledVersions::getVersion('kalel1500/kalion') ?? 'dev';
    }
}
