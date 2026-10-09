<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IntVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;

/**
 * DTO con props cuyo nombre coincide con funciones de PHP (`count`, `date`).
 */
final class CounterDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly IntVo    $count,
        public readonly StringVo $date,
    )
    {
    }
}

