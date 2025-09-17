<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Contracts;

use Thehouseofel\Kalion\Domain\Contracts\ArrayConvertible;

interface Relatable extends ArrayConvertible
{
    public function setWith(string|array|null $with): static;
    public function setIsFull(bool|string|null $isFull): static;

    /**
     * @template T of array|null
     * @param T $data
     * @param string|array|null $with
     * @param bool|string $isFull
     * @return (T is null ? null : static)
     */
    public static function fromArray(?array $data, string|array|null $with = null, bool|string $isFull = null): ?static;
}
