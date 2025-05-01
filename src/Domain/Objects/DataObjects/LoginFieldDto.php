<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\ThemeVo;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
final class LoginFieldDto extends ContractDataObject
{
    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly string $type,
        public readonly string $placeholder
    )
    {
    }

    protected static function createFromArray(array $data): static
    {
        return new static(
            $data['name'],
            $data['label'],
            $data['type'],
            $data['placeholder']
        );
    }
}
