<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services;

final class Kalion
{
    public static bool $runMigrations     = false;
    public static bool $publishMigrations = false;
    public static bool $registerRoutes    = true;
    public static bool $preferencesCookie = false;

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

    public static function configure(): static
    {
        return new static();
    }


    public function runMigrations(): static
    {
        static::$runMigrations = true;
        return $this;
    }

    public static function shouldRunMigrations(): bool
    {
        return static::$runMigrations;
    }

    public function publishMigrations(): static
    {
        static::$publishMigrations = true;
        return $this;
    }

    public static function shouldPublishMigrations(): bool
    {
        return static::$publishMigrations;
    }

    public function ignoreRoutes(): static
    {
        static::$registerRoutes = false;
        return $this;
    }

    public static function shouldRegistersRoutes(): bool
    {
        return static::$registerRoutes;
    }

    public function enablePreferencesCookie(): static
    {
        static::$preferencesCookie = true;
        return $this;
    }

    public static function enabledPreferencesCookie(): bool
    {
        return static::$preferencesCookie;
    }
}
