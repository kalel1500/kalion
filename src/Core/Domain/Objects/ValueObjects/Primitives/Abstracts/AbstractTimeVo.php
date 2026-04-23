<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\DateFormat;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\TimeNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\TimeVo;

abstract class AbstractTimeVo extends AbstractDateVo
{
    protected const CLASS_REQUIRED = TimeVo::class;
    protected const CLASS_NULLABLE = TimeNullVo::class;

    protected static array $formats = [DateFormat::time];
}
