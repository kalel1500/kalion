<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Contracts;

use Thehouseofel\Kalion\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Domain\Exceptions\RequiredDefinitionException;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\ContractDataObject;
use TypeError;

abstract class ContractCollectionDo extends ContractCollectionBase
{
    protected const IS_ENTITY = false;

    public function first(): ?ContractDataObject
    {
        return $this->items[0] ?? null;
    }

    static function fromArray(?array $values): static|null
    {
        if (is_null($values)) return null;

        if (is_null(static::VALUE_CLASS)) {
            throw new RequiredDefinitionException(sprintf('<%s> needs to define <%s> %s.', class_basename(static::class), 'VALUE_CLASS', 'constant'));
        }

        $valueClass = static::VALUE_CLASS;
        $res = [];
        try {
            foreach ($values as $value) {
                $res[] = ($value instanceof $valueClass)
                    ? $value
                    : ((is_subclass_of($valueClass, \BackedEnum::class)) ? $valueClass::from($value) : $valueClass::fromArray($value));
            }
        } catch (TypeError $exception) {
            throw new InvalidValueException(sprintf('Los valores del array no coinciden con los necesarios para instanciar la clase <%s>. Mira en <fromArray()> del ContractDataObject', $valueClass));
        }
        return new static(...$res); // Los 3 puntos son importantes, ya que los constructores también reciben los parámetros destructurados (...)
    }
}
