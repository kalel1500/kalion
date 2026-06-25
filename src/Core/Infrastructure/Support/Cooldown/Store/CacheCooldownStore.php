<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\Store;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Cache\Repository;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\Contracts\CooldownStore;

class CacheCooldownStore implements CooldownStore
{
    private const KEY_PREFIX = 'cooldown:last-run:';

    public function __construct(private readonly Repository $cache) {}

    public function getLastExecutedAt(string $key): ?CarbonImmutable
    {
        $value = $this->cache->get(self::KEY_PREFIX . $key);

        return $value ? CarbonImmutable::parse($value) : null;
    }

    public function setLastExecutedAt(string $key, DateTimeInterface $time): void
    {
        $this->cache->put(self::KEY_PREFIX . $key, $time->format(DateTimeInterface::ATOM));
    }
}
