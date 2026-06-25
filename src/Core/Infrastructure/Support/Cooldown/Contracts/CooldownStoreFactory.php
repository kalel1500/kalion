<?php

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\Contracts;

interface CooldownStoreFactory
{
    public function make(string $key): CooldownStore;
}
