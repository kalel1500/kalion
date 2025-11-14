<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\TimestampNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\TimestampVo;
use Thehouseofel\Kalion\Core\Infrastructure\Services\Date;

abstract class AbstractTimestampVo extends AbstractDateVo
{
    protected const CLASS_REQUIRED = TimestampVo::class;
    protected const CLASS_NULLABLE = TimestampNullVo::class;

    protected static array $formats = ['Y-m-d H:i:s.u', 'Y-m-d\TH:i:s.u\Z'];

    public function __construct(?string $value, ?array $formats = null)
    {
        parent::__construct($value, $formats);
    }
}
