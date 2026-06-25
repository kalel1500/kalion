<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\Store;

use Illuminate\Cache\Repository;
use Carbon\Carbon;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\Contracts\CooldownStore;

class CacheCooldownStore implements CooldownStore
{
    private const KEY_PREFIX = 'cooldown:last-run:';

    public function __construct(private readonly Repository $cache) {}

    public function getLastExecutedAt(string $key): ?Carbon
    {
        $value = $this->cache->get(self::KEY_PREFIX . $key);

        return $value ? Carbon::parse($value) : null;
    }

    public function setLastExecutedAt(string $key, Carbon $time): void
    {
        $this->cache->put(self::KEY_PREFIX . $key, $time->toIso8601String());
    }
}
