<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities\Collections;

use Thehouseofel\Kalion\Domain\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionEntity;
use Thehouseofel\Kalion\Domain\Objects\Entities\PermissionEntity;

#[CollectionOf(PermissionEntity::class)]
final class PermissionCollection extends ContractCollectionEntity
{
}
