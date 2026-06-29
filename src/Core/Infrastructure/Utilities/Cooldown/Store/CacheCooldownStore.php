<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cooldown\Store;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Cache\Repository;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cooldown\Contracts\CooldownStore;

class CacheCooldownStore implements CooldownStore
{
    private const KEY_PREFIX = 'cooldown:last-run:';

    public function __construct(
        private readonly Repository $cache,
        private readonly string     $key,
    )
    {
    }

    public function getLastExecutedAt(): ?CarbonImmutable
    {
        $value = $this->cache->get(self::KEY_PREFIX . $this->key);

        return $value ? CarbonImmutable::parse($value) : null;
    }

    public function setLastExecutedAt(DateTimeInterface $time): void
    {
        $this->cache->put(self::KEY_PREFIX . $this->key, $time->format(DateTimeInterface::ATOM));
    }
}
