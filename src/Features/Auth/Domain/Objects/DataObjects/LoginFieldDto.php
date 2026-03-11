<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Domain\Objects\DataObjects;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
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
