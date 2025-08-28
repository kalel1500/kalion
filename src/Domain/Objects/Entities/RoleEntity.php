<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\PermissionCollection;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelBool;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;

final class RoleEntity extends AbstractEntity
{
    public function __construct(
        public readonly ModelId|ModelIdNull $id,
        public readonly ModelString     $name,
        public readonly ModelBool       $all_permissions,
        public readonly ModelBool       $is_query
    )
    {
    }

    protected static function createFromArray(array $data): static
    {
        return new static(
            ModelId::from($data['id'] ?? null),
            ModelString::new($data['name']),
            ModelBool::new($data['all_permissions']),
            ModelBool::new($data['is_query'])
        );
    }

    protected function props(): array
    {
        return [
            'id'              => $this->id->value(),
            'name'            => $this->name->value(),
            'all_permissions' => $this->all_permissions->value(),
            'is_query'        => $this->is_query->value(),
        ];
    }

    #[RelationOf(PermissionCollection::class)]
    public function permissions(): PermissionCollection
    {
        return $this->getRelation();
    }
}
