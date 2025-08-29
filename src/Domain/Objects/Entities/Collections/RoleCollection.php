<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities\Collections;

use Thehouseofel\Kalion\Domain\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts\AbstractCollectionEntity;
use Thehouseofel\Kalion\Domain\Objects\Entities\RoleEntity;

#[CollectionOf(RoleEntity::class)]
class RoleCollection extends AbstractCollectionEntity
{
}
