<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\Contracts\ContractModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Domain\Traits\EntityHasPermissions;

class ApiUserEntity extends ContractEntity
{
    use EntityHasPermissions;

    public function __construct(
        public readonly ContractModelId $id,
        public readonly ModelString     $name,
    )
    {
    }

    protected static function createFromArray(array $data): static
    {
        return new static(
            ModelId::from($data['id'] ?? null),
            ModelString::new($data['name']),
        );
    }

    protected static function createFromChildArray(array $data, array $newFields): static
    {
        return new static(
            ModelId::from($data['id']),
            ModelString::new($data['name']),
            ...$newFields
        );
    }

    protected function toArrayProperties(): array
    {
        return [
            'id'      => $this->id->value(),
            'content' => $this->name->value(),
        ];
    }

    protected function toArrayPropertiesFromChild(array $newFields): array
    {
        return [
            'id'   => $this->id->value(),
            'name' => $this->name->value(),
            ...$newFields
        ];
    }
}
