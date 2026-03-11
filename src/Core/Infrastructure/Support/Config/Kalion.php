<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Config;

use Composer\InstalledVersions;

class Kalion
{
    public const ENUM_NULL_VALUE = 'k_null';

    public static function getShadowClasses(string $normalShadow = 'shadow-md'): string
    {
        return config('kalion.layout.use_elevated_shadows')
            ? 'kal:shadow-xl dark:kal:shadow-black-xl'
            : $normalShadow;
    }

    public static function getInstalledVersion(): string
    {
        return InstalledVersions::getVersion('kalel1500/kalion') ?? 'dev';
    }
}
