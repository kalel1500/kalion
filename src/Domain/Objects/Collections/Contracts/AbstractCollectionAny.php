<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Contracts;

use Thehouseofel\Kalion\Domain\Contracts\Relatable;

abstract class AbstractCollectionAny extends AbstractCollectionBase implements Relatable
{
    protected const ITEM_TYPE = '';

    protected string|array|null $with   = null;
    protected bool|string|null  $isFull = null;

    public static function fromArray(?array $values, string|array|null $with = null, bool|string|null $isFull = null): static|null
    {
        if (is_null($values)) return null;
        $collection         = new static($values);
        $collection->with   = $with;
        $collection->isFull = $isFull;
        return $collection;
    }
}
