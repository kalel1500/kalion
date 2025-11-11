<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdZeroNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdZeroVo;

abstract class AbstractIdZero extends AbstractId
{
    protected const CLASS_REQUIRED = IdZeroVo::class;
    protected const CLASS_NULLABLE = IdZeroNullVo::class;

    protected ?int $minimumValueForId = 0;
}
