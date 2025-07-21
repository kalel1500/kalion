<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Contracts;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\ContractValueObject;

abstract class ContractCollectionVo extends ContractCollectionBase
{
    public function first(): ?ContractValueObject
    {
        return parent::first();
    }

    public function firstValue()
    {
        return $this->first()?->value();
    }

    static function fromArray(?array $values, callable $valueModifierCallback = null): static|null
    {
        if (is_null($values)) return null;

        $valueClass = static::ITEM_TYPE;
        $res = [];
        foreach ($values as $value) {
            if ($value instanceof $valueClass) {
                $res[] = $value;
            } else {
                if (!is_null($valueModifierCallback)) {
                    $value = $valueModifierCallback($value);
                }
                $res[] = new $valueClass($value);
            }
        }
        $static = new static(...$res); // Los 3 puntos son importantes, ya que los constructores también reciben los parámetros destructurados (...)
        return $static;
    }
}
