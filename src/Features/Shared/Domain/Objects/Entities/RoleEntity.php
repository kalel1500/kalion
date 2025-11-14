<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\BoolVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\Collections\PermissionCollection;

class RoleEntity extends AbstractEntity
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
}
