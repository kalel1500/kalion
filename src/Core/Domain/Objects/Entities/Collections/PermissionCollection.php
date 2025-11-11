<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities\Collections;

use Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts\AbstractCollectionEntity;
use Thehouseofel\Kalion\Domain\Objects\Collections\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Objects\Entities\PermissionEntity;

#[CollectionOf(PermissionEntity::class)]
class PermissionCollection extends AbstractCollectionEntity
{
}
