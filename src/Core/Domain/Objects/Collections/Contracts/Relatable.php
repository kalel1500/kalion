<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\Collections\Contracts;

use Thehouseofel\Kalion\Core\Domain\Contracts\ArrayConvertible;

interface Relatable extends ArrayConvertible
{
    public function setWith(string|array|null $with): static;

    public function setIsFull(bool|string|null $isFull): static;

    /**
     * A null value represents an empty collection.
     */
    public static function fromArray(?array $data, string|array|null $with = null, bool|string|null $isFull = null): static;
}
