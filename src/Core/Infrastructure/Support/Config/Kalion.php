<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Config;

use Composer\InstalledVersions;

class Kalion
{
    public const ENUM_NULL_VALUE = 'k_null';

    public static function getInstalledVersion(): string
    {
        return InstalledVersions::getVersion('kalel1500/kalion') ?? 'dev';
    }
}
