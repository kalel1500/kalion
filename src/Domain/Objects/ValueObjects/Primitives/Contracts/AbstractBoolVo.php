<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts;

use Thehouseofel\Kalion\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelBool;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelBoolNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelDate;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelDateNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\BoolNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\BoolVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\DateNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\DateVo;

abstract class AbstractBoolVo extends AbstractValueObject
{
    protected const CLASS_REQUIRED = BoolVo::class;
    protected const CLASS_NULLABLE = BoolNullVo::class;
    protected const CLASS_MODEL_REQUIRED = ModelBool::class;
    protected const CLASS_MODEL_NULLABLE = ModelBoolNull::class;

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

        if (!is_null($value) && !is_valid_bool($value)) {
            throw new InvalidValueException(sprintf('<%s> does not allow the value <%s> as valid boolean.', class_basename(static::class), $value));
        }
    }
}
