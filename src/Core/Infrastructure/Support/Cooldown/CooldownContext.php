<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown;

class CooldownContext
{
    private bool $skipUpdate = false;

    public function skipUpdateLastExecutedAt(): void
    {
        $this->skipUpdate = true;
    }

    public function shouldUpdateLastExecutedAt(): bool
    {
        return ! $this->skipUpdate;
    }
}
