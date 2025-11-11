<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects;

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
