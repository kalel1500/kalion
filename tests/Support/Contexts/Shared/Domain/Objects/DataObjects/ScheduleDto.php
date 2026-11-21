<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\TimestampVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\TimeVo;

/**
 * DTO with one property of each date VO.
 */
final class ScheduleDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly TimestampVo $starts_at,
        public readonly DateNullVo  $ends_on,
        public readonly TimeVo      $opens_at,
    )
    {
    }
}
