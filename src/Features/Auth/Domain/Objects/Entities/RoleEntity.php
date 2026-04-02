<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Attributes\Computed;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\BoolVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AccessEntity;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\Collections\PermissionCollection;

class RoleEntity extends AbstractEntity implements AccessEntity
{
    public function __construct(
        public readonly IdVo|IdNullVo $id,
        public readonly StringVo      $name,
        public readonly BoolVo        $all_permissions,
        public readonly BoolVo        $is_query
    )
    {
    }

    #[RelationOf(PermissionCollection::class)]
    public function permissions(): PermissionCollection
    {
        return $this->getRelation();
    }

    #[Computed(Computed::AS_ATTRIBUTE)]
    public function getIsQuery(): bool
    {
        return $this->computed(fn() => $this->is_query->isTrue());
    }
}
