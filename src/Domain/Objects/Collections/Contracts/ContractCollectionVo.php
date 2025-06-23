<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Contracts;

use Thehouseofel\Kalion\Domain\Exceptions\RequiredDefinitionException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\ContractValueObject;

abstract class ContractCollectionVo extends ContractCollectionBase
{
    protected const IS_ENTITY = false;

    public function first(): ?ContractValueObject
    {
        return parent::first();
    }

    public function firstValue()
    {
        return $this->first()?->value();
    }

    static function fromArray(?array $values, bool $nullable = true, callable $valueModifierCallback = null): static|null
    {
        if (is_null($values)) return null;

        if (is_null(static::VALUE_CLASS_NULL) || is_null(static::VALUE_CLASS_REQ)) {
            throw new RequiredDefinitionException(sprintf('<%s> needs to define <%s> and <%s> %s.', class_basename(static::class), 'VALUE_CLASS_NULL', 'VALUE_CLASS_REQ', 'constants'));
        }

        $valueClass = ($nullable) ? static::VALUE_CLASS_NULL : static::VALUE_CLASS_REQ;
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
        $static->nullable = $nullable;
        return $static;
    }
}
