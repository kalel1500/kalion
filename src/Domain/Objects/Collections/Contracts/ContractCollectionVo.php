<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Contracts;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\ContractValueObject;

abstract class ContractCollectionVo extends ContractCollectionBase
{
    /**
     * @return ContractValueObject|null
     */
    public function first()
    {
        return parent::first();
    }

    public function firstValue()
    {
        return $this->first()?->value();
    }

    public static function fromArray(?array $values, callable $valueModifierCallback = null): static|null
    {
        if (is_null($values)) return null;

        $valueClass = static::resolveItemType();
        $res = [];
        foreach ($values as $key => $value) {
            if ($value instanceof $valueClass) {
                $res[$key] = $value;
            } else {
                if (!is_null($valueModifierCallback)) {
                    $value = $valueModifierCallback($value);
                }
                $res[$key] = new $valueClass($value);
            }
        }
        return new static($res);
    }
}
