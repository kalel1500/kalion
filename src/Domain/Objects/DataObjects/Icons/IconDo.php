<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Icons;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\ContractDataObject;

final class IconDo extends ContractDataObject
{
    public function __construct(
        public readonly string $name,
        public readonly string $name_short
    )
    {
    }

    protected static function createFromArray(array $data): static
    {
        return new static(
            $data['name'] ?? null,
            $data['name_short'] ?? null
        );
    }
}
