<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Attributes\Computed;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AbilityEntity;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\Collections\RoleCollection;

class PermissionEntity extends AbstractEntity implements AbilityEntity
{
    public function __construct(
        public readonly IdVo|IdNullVo $id,
        public readonly StringVo      $name
    )
    {
    }

    #[RelationOf(RoleCollection::class)]
    public function roles(): RoleCollection
    {
        return $this->getRelation();
    }

    #[Computed(Computed::AS_ATTRIBUTE)]
    public function getIsQuery(): bool
    {
        return $this->computed(fn() => $this->roles()->contains(fn(RoleEntity $role) => $role->getIsQuery()));
    }
}
