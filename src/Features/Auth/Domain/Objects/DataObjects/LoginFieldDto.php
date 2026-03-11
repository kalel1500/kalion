<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Domain\Objects\DataObjects;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;

final class LoginFieldDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly string $type,
        public readonly string $placeholder
    )
    {
    }
}
