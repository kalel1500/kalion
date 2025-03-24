<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services;

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

    public static function getShadowClasses(string $normalShadow = 'shadow-md'): string
    {
        return config('kalion.layout.active_shadows')
            ? 'kal:shadow-xl dark:kal:shadow-black-xl'
            : $normalShadow;
    }
}
