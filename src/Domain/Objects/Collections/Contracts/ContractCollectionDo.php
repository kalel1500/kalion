<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Contracts;

use Thehouseofel\Kalion\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Domain\Exceptions\RequiredDefinitionException;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\ContractDataObject;
use TypeError;

abstract class ContractCollectionDo extends ContractCollectionBase
{
    public function first(): ?ContractDataObject
    {
        return parent::first();
    }

    public static function fromArray(?array $values): ?static
    {
        if (is_null($values)) return null;

        $valueClass = static::resolveItemType();
        $res = [];
        try {
            foreach ($values as $key => $value) {
                $res[$key] = ($value instanceof $valueClass)
                    ? $value
                    : ((is_subclass_of($valueClass, \BackedEnum::class)) ? $valueClass::from($value) : $valueClass::fromArray($value));
            }
        } catch (TypeError $exception) {
            throw new InvalidValueException(sprintf('Los valores del array no coinciden con los necesarios para instanciar la clase <%s>. Mira en <fromArray()> del ContractDataObject', $valueClass));
        }
        return new static($res);
    }
}
