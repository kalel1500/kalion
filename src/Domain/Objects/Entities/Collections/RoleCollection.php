<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities\Collections;

use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionEntity;
use Thehouseofel\Kalion\Domain\Objects\Entities\RoleEntity;

final class RoleCollection extends ContractCollectionEntity
{
    public const ENTITY = RoleEntity::class;

    public function __construct(RoleEntity ...$items)
    {
        $this->items = $items;
    }
}
