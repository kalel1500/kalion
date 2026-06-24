<?php

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\Contracts;

interface Mutex
{
    public function runExclusive(string $key, int $seconds, callable $callback): bool;
}
