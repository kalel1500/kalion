<?php

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\Contracts;

use Carbon\CarbonImmutable;
use DateTimeInterface;

interface CooldownStore
{
    public function getLastExecutedAt(string $key): ?CarbonImmutable;

    public function setLastExecutedAt(string $key, DateTimeInterface $time): void;
}
