<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown;

use Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\Contracts\CooldownStore;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\Contracts\Mutex;

readonly class CooldownManager
{
    public function __construct(
        private CooldownStore $store,
        private Mutex         $mutex,
    )
    {
    }

    public function for(string $key): PendingCooldown
    {
        return new PendingCooldown($key, $this->store, $this->mutex);
    }
}
