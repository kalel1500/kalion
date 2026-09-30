<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts;

use Thehouseofel\Kalion\Core\Domain\Contracts\ArrayResolvable;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Contracts\MakeArrayable;

abstract class AbstractCollectionDto extends AbstractCollectionBase implements MakeArrayable, ArrayResolvable
{
    public function first(?callable $callback = null, $default = null)
    {
        return parent::first(...func_get_args());
    }

    public function toMakeArray(): array
    {
        return array_map(fn(AbstractDataTransferObject $item) => $item->toMakeArray(), $this->items);
    }

    /**
     * A null value represents an empty collection.
     */
    public static function fromArray(?array $data): static
    {
        /** @var AbstractDataTransferObject $valueClass */
        $valueClass = static::resolveItemType();
        $res        = [];
        foreach ($data ?? [] as $key => $value) {
            $res[$key] = ($value instanceof $valueClass) ? $value : $valueClass::fromArray($value);
        }
        return new static($res);
    }

    /**
     * A null value represents an empty collection.
     */
    public static function resolveFromArray(?array $data): static
    {
        /** @var AbstractDataTransferObject $valueClass */
        $valueClass = static::resolveItemType();
        $res        = [];
        foreach ($data ?? [] as $key => $value) {
            $res[$key] = ($value instanceof $valueClass) ? $value : $valueClass::resolveFromArray($value);
        }
        return new static($res);
    }
}
