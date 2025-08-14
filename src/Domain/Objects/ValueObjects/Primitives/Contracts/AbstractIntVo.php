<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelInt;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIntNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IntNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IntVo;

abstract class AbstractIntVo extends AbstractValueObject
{
    protected const CLASS_REQUIRED = IntVo::class;
    protected const CLASS_NULLABLE = IntNullVo::class;
    protected const CLASS_MODEL_REQUIRED = ModelInt::class;
    protected const CLASS_MODEL_NULLABLE = ModelIntNull::class;

    public function __construct(?int $value)
    {
        $this->ensureIsValidValue($value);
        $this->value = $value;
    }

    public function value(): ?int
    {
        return $this->value;
    }

    public function isBiggerThan(int $number): bool
    {
        $other = new IntVo($number);
        return $this->value() > $other->value();
    }

    public function isLessThan(int $number): bool
    {
        $other = new IntVo($number);
        return $this->value() < $other->value();
    }

    public function equals(int $number): bool
    {
        $other = new IntVo($number);
        return $this->value() === $other->value();
    }

    public function isBiggerOrEqualThan(int $number): bool
    {
        $other = new IntVo($number);
        return $this->value() >= $other->value();
    }

    public function isLessOrEqualThan(int $number): bool
    {
        $other = new IntVo($number);
        return $this->value() <= $other->value();
    }

    protected function ensureIsValidValue(?int $value): void
    {
        $this->checkNullable($value);
    }
}
