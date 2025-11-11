<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IntNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IntVo;

abstract class AbstractIntVo extends AbstractValueObject
{
    protected const CLASS_REQUIRED = IntVo::class;
    protected const CLASS_NULLABLE = IntNullVo::class;

    /**
     * @var int|null
     */
    public $value;

    public function __construct(?int $value)
    {
        $this->ensureIsValidValue($value);
        $this->value = $value;
    }

    public function isBiggerThan(int $number): bool
    {
        $other = new IntVo($number);
        return $this->value > $other->value;
    }

    public function isLessThan(int $number): bool
    {
        $other = new IntVo($number);
        return $this->value < $other->value;
    }

    public function equals(int $number): bool
    {
        $other = new IntVo($number);
        return $this->value === $other->value;
    }

    public function isBiggerOrEqualThan(int $number): bool
    {
        $other = new IntVo($number);
        return $this->value >= $other->value;
    }

    public function isLessOrEqualThan(int $number): bool
    {
        $other = new IntVo($number);
        return $this->value <= $other->value;
    }

    protected function ensureIsValidValue(?int $value): void
    {
        $this->checkNullable($value);
    }
}
