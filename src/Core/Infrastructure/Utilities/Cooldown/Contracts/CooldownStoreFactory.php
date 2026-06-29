<?php

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cooldown\Contracts;

interface CooldownStoreFactory
{
    public function make(string $key): CooldownStore;
}
