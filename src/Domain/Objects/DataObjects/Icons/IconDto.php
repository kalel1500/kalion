<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Icons;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\AbstractDataTransferObject;

final class IconDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly string $name,
        public readonly string $name_short
    )
    {
    }
}
