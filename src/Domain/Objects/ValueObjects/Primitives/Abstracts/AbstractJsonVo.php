<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Thehouseofel\Kalion\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelJsonNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelJsonVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\JsonNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\JsonVo;

abstract class AbstractJsonVo extends AbstractValueObject
{
    protected const CLASS_REQUIRED       = JsonVo::class;
    protected const CLASS_NULLABLE       = JsonNullVo::class;
    protected const CLASS_MODEL_REQUIRED = ModelJsonVo::class;
    protected const CLASS_MODEL_NULLABLE = ModelJsonNullVo::class;

    protected bool $allowInvalidJson = true;

    protected ?array            $arrayValue   = null;
    protected array|object|null $objectValue  = null;
    protected ?string           $encodedValue = null;
    protected bool              $failAtFormat = false;

    public function __construct($value)
    {
        $this->ensureIsValidValue($value);
        $this->setValues($value);
        $this->value = $value;
    }

    protected function ensureIsValidValue($value): void
    {
        $this->checkNullable($value);

        if (!empty($value) && (!is_string($value) && !is_array($value) && !is_object($value))) {
            throw new InvalidValueException(sprintf('<%s> must have a value of type string object or array.', class_basename(static::class)));
        }
    }

    protected function setValues($value): void
    {
        if (empty($value)) return;

        if (is_string($value)) {
            $this->arrayValue   = json_decode($value, true);
            $this->objectValue  = json_decode($value);
            $this->encodedValue = $value;
            if (is_null($this->objectValue)) {
                $this->failAtFormat = true;
                $this->encodedValue = json_encode($value);
                if (!$this->allowInvalidJson) {
                    throw new InvalidValueException(sprintf('Invalid JSON passed to constructor of class <%s>.', class_basename(static::class)));
                }
            }
        }

        if (is_array($value) || is_object($value)) {
            $this->arrayValue   = legacy_json_to_array($value);
            $this->objectValue  = legacy_json_to_object($value);
            $this->encodedValue = json_encode($value);
        }
    }

    public function value(): null|array|object|string
    {
        return $this->value;
    }

    public function valueArray(): ?array
    {
        return $this->arrayValue;
    }

    public function valueObj(): array|object|null
    {
        return $this->objectValue;
    }

    public function valueEncoded(): ?string
    {
        return $this->encodedValue;
    }

    public function failAtFormat(): bool
    {
        return $this->failAtFormat;
    }

    public function isNull(): bool
    {
        return is_null($this->value);
    }

    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    public function isNullStrict(): bool
    {
        return (is_null($this->arrayValue) || is_null($this->objectValue) || is_null($this->encodedValue));
    }

    public function isEmptyStrict(): bool
    {
        return (empty($this->arrayValue) || empty($this->objectValue) || empty($this->encodedValue));
    }


    /*----------------------------------------------------------------------------------------------------------------------------------------------*/
    /*----------------------------------------------------------------MODIFIERS---------------------------------------------------------------------*/


    public function toArray(): static
    {
        $this->value = $this->arrayValue;
        return $this;
    }

    public function toObject(): static
    {
        $this->value = $this->objectValue;
        return $this;
    }

    public function encode(): static
    {
        $this->value = $this->encodedValue;
        return $this;
    }
}
