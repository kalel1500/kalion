<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\Contracts\CooldownStoreFactory;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\Contracts\Mutex;

readonly class CooldownManager
{
    public function __construct(
        private CooldownStoreFactory $storeFactory,
        private Mutex                $mutex,
    ) {}

    public function for(string $key): PendingCooldown
    {
        return new PendingCooldown($key, $this->storeFactory->make($key), $this->mutex);
    }

    public function touch(string $key, ?DateTimeInterface $time = null): void
    {
        $this->storeFactory->make($key)->setLastExecutedAt($time ?? CarbonImmutable::now());
    }

    public function getLastExecutedAt(string $key): ?CarbonImmutable
    {
        return $this->storeFactory->make($key)->getLastExecutedAt();
    }
}
