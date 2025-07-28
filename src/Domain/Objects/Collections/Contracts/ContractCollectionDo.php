<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Contracts;

use BackedEnum;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\ContractDataObject;

abstract class ContractCollectionDo extends ContractCollectionBase
{
    /**
     * @return ContractDataObject|null
     */
    public function first(?callable $callback = null, $default = null)
    {
        return parent::first(...func_get_args());
    }

    public static function fromArray(?array $values): ?static
    {
        if (is_null($values)) return null;

        $valueClass = static::resolveItemType();
        $res = [];
        foreach ($values as $key => $value) {
            $res[$key] = ($value instanceof $valueClass)
                ? $value
                : (is_subclass_of($valueClass, BackedEnum::class)
                    ? $valueClass::from($value)
                    : $valueClass::fromArray($value)
                );
        }
        return new static($res);
    }
}
