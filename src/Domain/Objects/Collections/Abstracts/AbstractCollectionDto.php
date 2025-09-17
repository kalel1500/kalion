<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\AbstractDataTransferObject;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Contracts\MakeParamsArrayable;

abstract class AbstractCollectionDto extends AbstractCollectionBase implements MakeParamsArrayable
{
    /**
     * @return AbstractDataTransferObject|null
     */
    public function first(?callable $callback = null, $default = null)
    {
        return parent::first(...func_get_args());
    }

    public function toMakeParams(): array
    {
        return array_map(fn(AbstractDataTransferObject $item) => $item->toMakeParams(), $this->items);
    }

    public static function fromArray(?array $values): ?static
    {
        if (is_null($values)) return null;

        /** @var AbstractDataTransferObject $valueClass */
        $valueClass = static::resolveItemType();
        $res = [];
        foreach ($values as $key => $value) {
            $res[$key] = ($value instanceof $valueClass) ? $value : $valueClass::fromArray($value);
        }
        return new static($res);
    }
}
