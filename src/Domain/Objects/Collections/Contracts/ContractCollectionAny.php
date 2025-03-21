<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Contracts;

use Thehouseofel\Kalion\Domain\Contracts\Relatable;

abstract class ContractCollectionAny extends ContractCollectionBase implements Relatable
{
    protected const IS_ENTITY        = false;
    protected const VALUE_CLASS      = 'any';
    protected const VALUE_CLASS_REQ  = 'any';
    protected const VALUE_CLASS_NULL = 'any';

    protected string|array|null $with   = null;
    protected bool|string|null  $isFull = null;

    static function fromArray(?array $values, string|array|null $with = null, bool|string|null $isFull = null): static|null
    {
        if (is_null($values)) return null;
        $collection         = new static($values);
        $collection->with   = $with;
        $collection->isFull = $isFull;
        return $collection;
    }
}
