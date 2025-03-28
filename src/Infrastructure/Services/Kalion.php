<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\LoginFieldDto;

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
                    'provider' => 'api',
                ],
            ]);
        }

        if (! config()->has('auth.providers.users.model')) {
            config([
                'auth.providers.users.model' => env('AUTH_MODEL', \Thehouseofel\Kalion\Infrastructure\Models\User::class),
            ]);
        }

        if (! config()->has('auth.providers.api')) {
            config([
                'auth.providers.api' => [
                    'driver' => 'eloquent',
                    'model' => env('AUTH_MODEL', \Thehouseofel\Kalion\Infrastructure\Models\ApiUser::class),
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

    public static function getLoginFieldData(): LoginFieldDto
    {
        $defaultField = config('kalion.auth.field');
        $fields = config('kalion.auth.fields');
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

    public static function getClassUserModel(): string // |\Illuminate\Foundation\Auth\User
    {
        return config('auth.providers.users.model');
    }

    public static function getClassUserEntity(): string
    {
        return config('kalion_user.entity');
    }

    public static function getClassUserRepository(): string
    {
        return config('kalion_user.repository');
    }
}
