<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects;

class TabulatorFilterDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly string $field,
        public readonly string $type,
        public readonly mixed  $value,
    )
    {
    }
}
