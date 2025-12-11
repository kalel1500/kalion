<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateZeroNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateZeroVo;

abstract class AbstractDateZeroVo extends AbstractDateVo
{
    protected const CLASS_REQUIRED = DateZeroVo::class;
    protected const CLASS_NULLABLE = DateZeroNullVo::class;

    protected bool $allowZeros = true;
}
