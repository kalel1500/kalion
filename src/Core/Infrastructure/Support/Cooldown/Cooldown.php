<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown;

use Carbon\Carbon;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\PendingCooldown for(string $key)
 * @method static void touch(string $key, Carbon|null $time = null)
 *
 * @see \Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\CooldownManager
 */
class Cooldown extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'kalion.cooldown';
    }
}
