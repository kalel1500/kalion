<?php

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\Contracts;

use Illuminate\Support\Carbon;

interface CooldownStore
{
    public function getLastExecutedAt(string $key): ?Carbon;

    public function setLastExecutedAt(string $key, Carbon $time): void;
}
