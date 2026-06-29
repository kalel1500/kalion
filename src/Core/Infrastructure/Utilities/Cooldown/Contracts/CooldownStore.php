<?php

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cooldown\Contracts;

use Carbon\CarbonImmutable;
use DateTimeInterface;

interface CooldownStore
{
    public function getLastExecutedAt(): ?CarbonImmutable;

    public function setLastExecutedAt(DateTimeInterface $time): void;
}
