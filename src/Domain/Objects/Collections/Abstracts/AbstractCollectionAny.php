<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts;

use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\Relatable;
use Thehouseofel\Kalion\Domain\Objects\Collections\Concerns\HasRelatableOptions;

abstract class AbstractCollectionAny extends AbstractCollectionBase implements Relatable
{
    use HasRelatableOptions;

    /**
     * @template T of array|null
     * @param T $data
     * @param string|array|null $with
     * @param bool|string $isFull
     * @return (T is null ? null : static)
     */
    public static function fromArray(?array $data, string|array|null $with = null, bool|string|null $isFull = null): ?static
    {
        if (is_null($data)) return null;
        $collection         = new static($data);
        $collection->with   = $with;
        $collection->isFull = $isFull;
        return $collection;
    }
}
