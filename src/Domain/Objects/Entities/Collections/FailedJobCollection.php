<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities\Collections;

use Thehouseofel\Kalion\Domain\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\AbstractCollectionEntity;
use Thehouseofel\Kalion\Domain\Objects\Entities\FailedJobEntity;

#[CollectionOf(FailedJobEntity::class)]
class FailedJobCollection extends AbstractCollectionEntity
{
}
