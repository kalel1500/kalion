<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\Entities\Collections;

use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts\AbstractCollectionEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Attributes\CollectionOf;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\PermissionEntity;

#[CollectionOf(PermissionEntity::class)]
class PermissionCollection extends AbstractCollectionEntity
{
}
