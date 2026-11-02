<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;

/**
 * DTO con campos que solo aparecen en `toArray()` (calculados a partir de las props), pero no en
 * `toMakeArray()`: `score` y `initial`.
 */
final class RankedDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly string $name,
        public readonly int    $points,
    )
    {
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'score'   => $this->points * 10,
            'initial' => strtoupper($this->name[0]),
        ];
    }
}

