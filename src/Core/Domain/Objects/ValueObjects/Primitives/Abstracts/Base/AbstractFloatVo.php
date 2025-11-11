<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\FloatNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\FloatVo;

abstract class AbstractFloatVo extends AbstractValueObject
{
    protected const CLASS_REQUIRED = FloatVo::class;
    protected const CLASS_NULLABLE = FloatNullVo::class;

    /**
     * @var float|null
     */
    public $value;

    public function __construct(?float $value)
    {
        $this->ensureIsValidValue($value);
        $this->value = $value;
    }

    public function isBiggerThan(float $number): bool
    {
        return $this->value > $number;
    }

    public function isLessThan(float $number): bool
    {
        return $this->value < $number;
    }

    public function equals(float $number): bool
    {
        return $this->value === $number;
    }

    public function isBiggerOrEqualThan(float $number): bool
    {
        return $this->value >= $number;
    }

    public function isLessOrEqualThan(float $number): bool
    {
        return $this->value <= $number;
    }

    protected function ensureIsValidValue(?float $value): void
    {
        $this->checkNullable($value);
    }
}
