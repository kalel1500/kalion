<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\Base;

use Thehouseofel\Kalion\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\BoolNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\BoolVo;

abstract class AbstractBoolVo extends AbstractValueObject
{
    protected const CLASS_REQUIRED = BoolVo::class;
    protected const CLASS_NULLABLE = BoolNullVo::class;

    public function __construct($value)
    {
        $this->ensureIsValidValue($value);
        $this->value = is_null($value) ? null : boolval($value);
    }

    public function value(): ?bool
    {
        return $this->isNullReceived() ? null : (bool)$this->value;
    }

    public function valueInt(): ?int
    {
        return $this->isNull() ? null : (int)$this->value();
    }

    public function isTrue(): bool
    {
        return $this->value() === true;
    }

    public function isFalse(): bool
    {
        return $this->value() === false;
    }

    private function ensureIsValidValue($value): void
    {
        $this->checkNullable($value);

        if (! is_null($value) && ! is_valid_bool($value)) {
            throw new InvalidValueException(sprintf('<%s> does not allow the value <%s> as valid boolean.', class_basename(static::class), $value));
        }
    }
}
