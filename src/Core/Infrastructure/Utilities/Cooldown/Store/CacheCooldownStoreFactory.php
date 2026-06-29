<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cooldown\Store;

use Illuminate\Cache\Repository;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cooldown\Contracts\CooldownStore;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cooldown\Contracts\CooldownStoreFactory;

readonly class CacheCooldownStoreFactory implements CooldownStoreFactory
{
    public function __construct(private Repository $cache) {}

    public function make(string $key): CooldownStore
    {
        return new CacheCooldownStore($this->cache, $key);
    }
}
