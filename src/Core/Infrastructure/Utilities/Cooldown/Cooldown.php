<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cooldown;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cooldown\PendingCooldown for(string $key)
 * @method static void touch(string $key, \DateTimeInterface|null $time = null)
 *
 * @see \Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cooldown\CooldownManager
 */
class Cooldown extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'kalion.cooldown';
    }
}
