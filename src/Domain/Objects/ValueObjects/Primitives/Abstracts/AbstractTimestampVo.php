<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelTimestamp;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelTimestampNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\TimestampNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\TimestampVo;
use Thehouseofel\Kalion\Infrastructure\Services\Date;

abstract class AbstractTimestampVo extends AbstractDateVo
{
    protected const CLASS_REQUIRED = TimestampVo::class;
    protected const CLASS_NULLABLE = TimestampNullVo::class;
    protected const CLASS_MODEL_REQUIRED = ModelTimestamp::class;
    protected const CLASS_MODEL_NULLABLE = ModelTimestampNull::class;

    public function __construct(?string $value, ?array $formats = null)
    {
        $this->formats = [Date::$datetime_eloquent_timestamps];
        parent::__construct($value, $formats);
    }
}
