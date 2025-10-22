<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\RoleCollection;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringVo;

class PermissionEntity extends AbstractEntity
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
}
