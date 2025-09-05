<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts;

use Thehouseofel\Kalion\Domain\Contracts\Relatable;

abstract class AbstractCollectionAny extends AbstractCollectionBase implements Relatable
{
    protected string|array|null $with   = null;
    protected bool|string|null  $isFull = null;

    /**
     * @template T of array|null
     * @param T $data
     * @param string|array|null $with
     * @param bool|string $isFull
     * @return (T is null ? null : static)
     */
    public static function fromArray($data, string|array|null $with = null, bool|string|null $isFull = null)
    {
        if (is_null($data)) return null;
        $collection         = new static($data);
        $collection->with   = $with;
        $collection->isFull = $isFull;
        return $collection;
    }
}
