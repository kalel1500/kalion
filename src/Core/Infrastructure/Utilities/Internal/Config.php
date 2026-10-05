<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Internal;

/**
 * @internal This class is intended for internal package usage only.
 */
class Config
{
    public function authEnabled(): bool
    {
        return (bool) config('kalion.auth.enabled', true);
    }

    public function abilitiesEnabled(): bool
    {
        return config('kalion.auth.abilities_enabled', true);
    }
}

