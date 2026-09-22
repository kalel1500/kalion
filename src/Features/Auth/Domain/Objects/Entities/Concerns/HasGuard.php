<?php

namespace Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\Concerns;

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
