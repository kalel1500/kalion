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

    protected ?array            $valueArray   = null;
    protected array|object|null $valueObject  = null;
    protected ?string           $valueString  = null;
    protected bool              $invalidJson  = false;

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
            $this->valueArray  = json_decode($value, true);
            $this->valueObject = json_decode($value);
            $this->valueString = $value;
        }

        if (is_array($value) || is_object($value)) {
            $this->valueArray  = legacy_json_to_array($value);
            $this->valueObject = legacy_json_to_object($value);
            $this->valueString = (!$decoded = json_encode($value)) ? null : $decoded;
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            if (! $this->allowInvalidJson) {
                throw new InvalidValueException(sprintf('Invalid JSON passed to constructor of class <%s>.', class_basename(static::class)));
            }
            $this->invalidJson = true;
        }
    }

    public function value(): null|array|object|string
    {
        return $this->value;
    }

    public function decodeAssoc(): ?array
    {
        return $this->valueArray;
    }

    public function decodeObj(): array|object|null
    {
        return $this->valueObject;
    }

    public function invalidJson(): bool
    {
        return $this->invalidJson;
    }

    public function isNull(): bool
    {
        return is_null($this->value);
    }

    public function isEmpty(): bool
    {
        return empty($this->value) || empty($this->valueArray);
    }
}
