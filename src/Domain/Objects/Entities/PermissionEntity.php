<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\RoleCollection;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;

final class PermissionEntity extends ContractEntity
{
    public function __construct(
        public readonly ModelId|ModelIdNull $id,
        public readonly ModelString     $name
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

    protected function toArrayProperties(): array
    {
        return [
            'id'   => $this->id->value(),
            'name' => $this->name->value(),
        ];
    }

    public function roles(): RoleCollection
    {
        return $this->getRelation('roles');
    }

    public function setRoles(array $value): void
    {
        $this->setRelation($value, 'roles', RoleCollection::class);
    }
}
