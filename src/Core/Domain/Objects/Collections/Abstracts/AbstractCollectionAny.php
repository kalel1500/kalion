<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts;

use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Concerns\HasRelatableOptions;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Contracts\Relatable;

abstract class AbstractCollectionAny extends AbstractCollectionBase implements Relatable
{
    use HasRelatableOptions;

    /**
     * A null value represents an empty collection.
     */
    public static function fromArray(?array $data, string|array|null $with = null, bool|string|null $isFull = null): static
    {
        $collection         = new static($data ?? []);
        $collection->with   = $with;
        $collection->isFull = $isFull;
        return $collection;
    }
}
