<?php

namespace Thehouseofel\Kalion\Core\Domain\Objects\Collections\Concerns;

/**
 * @internal This trait is not meant to be used or overwritten outside the package.
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
