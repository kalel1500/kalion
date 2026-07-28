<?php

namespace Thehouseofel\Kalion\Core\Domain\Objects\Collections\Concerns;

/**
 * @internal This class is intended for internal package usage only.
 */
trait HasRelatableOptions
{
    protected string|array|null $with   = null;
    protected bool|string|null  $isFull = null;

    public function setWith(string|array|null $with): static
    {
        $this->with = $with;
        return $this;
    }

    public function setIsFull(bool|string|null $isFull): static
    {
        $this->isFull = $isFull;
        return $this;
    }
}
