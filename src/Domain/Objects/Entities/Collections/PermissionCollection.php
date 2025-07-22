<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities\Collections;

use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionEntity;
use Thehouseofel\Kalion\Domain\Objects\Entities\PermissionEntity;

final class PermissionCollection extends ContractCollectionEntity
{
    public const ITEM_TYPE = PermissionEntity::class;
}
