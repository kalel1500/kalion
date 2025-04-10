<?php

namespace Thehouseofel\Kalion\Domain\Traits;

trait HasGuard
{
    protected ?string $guard = null;

    public function getGuard(): ?string
    {
        return $this->guard;
    }

    public function setGuard(?string $guard): void
    {
        $this->guard = $guard;
    }
}
