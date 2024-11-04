<?php

declare(strict_types=1);

namespace Thehouseofel\Hexagonal\Domain\Objects\ValueObjects\Primitives\Contracts;

use Thehouseofel\Hexagonal\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Hexagonal\Domain\Objects\ValueObjects\EntityFields\ModelUnsignedInt;
use Thehouseofel\Hexagonal\Domain\Objects\ValueObjects\EntityFields\ModelUnsignedIntNull;
use Thehouseofel\Hexagonal\Domain\Objects\ValueObjects\Primitives\UnsignedIntNullVo;
use Thehouseofel\Hexagonal\Domain\Objects\ValueObjects\Primitives\UnsignedIntVo;

abstract class ContractUnsignedIntVo extends ContractIntVo
{
    protected const CLASS_REQUIRED = UnsignedIntVo::class;
    protected const CLASS_NULLABLE = UnsignedIntNullVo::class;
    protected const CLASS_MODEL_REQUIRED = ModelUnsignedInt::class;
    protected const CLASS_MODEL_NULLABLE = ModelUnsignedIntNull::class;

    protected function ensureIsValidValue(?int $value): void
    {
        $this->checkAllowNull($value);

        if (!is_null($value) && $value < 0) {
            throw new InvalidValueException(sprintf('<%s> does not allow the value <%s>.', class_basename(static::class), $value));
        }
    }
}
