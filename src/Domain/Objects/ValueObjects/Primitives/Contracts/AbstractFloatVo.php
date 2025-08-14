<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelFloat;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelFloatNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\FloatNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\FloatVo;

abstract class AbstractFloatVo extends AbstractValueObject
{
    protected const CLASS_REQUIRED = FloatVo::class;
    protected const CLASS_NULLABLE = FloatNullVo::class;
    protected const CLASS_MODEL_REQUIRED = ModelFloat::class;
    protected const CLASS_MODEL_NULLABLE = ModelFloatNull::class;

    public function __construct(?float $value)
    {
        $this->ensureIsValidValue($value);
        $this->value = $value;
    }

    public function value(): ?float
    {
        return $this->value;
    }

    public function isBiggerThan(float $number): bool
    {
        return $this->value() > $number;
    }

    public function isLessThan(float $number): bool
    {
        return $this->value() < $number;
    }

    public function equals(float $number): bool
    {
        return $this->value() === $number;
    }

    public function isBiggerOrEqualThan(float $number): bool
    {
        return $this->value() >= $number;
    }

    public function isLessOrEqualThan(float $number): bool
    {
        return $this->value() <= $number;
    }

    protected function ensureIsValidValue(?float $value): void
    {
        $this->checkNullable($value);
    }
}
