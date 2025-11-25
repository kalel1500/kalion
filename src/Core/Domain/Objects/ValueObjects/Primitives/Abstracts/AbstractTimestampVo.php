<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\DateFormat;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\TimestampNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\TimestampVo;

abstract class AbstractTimestampVo extends AbstractDateVo
{
    protected const CLASS_REQUIRED = TimestampVo::class;
    protected const CLASS_NULLABLE = TimestampNullVo::class;

    protected static array $formats = [DateFormat::datetime_timestamp, DateFormat::datetime_eloquent_timestamps];

    public function __construct(?string $value, ?array $formats = null)
    {
        parent::__construct($value, $formats);
    }
}
