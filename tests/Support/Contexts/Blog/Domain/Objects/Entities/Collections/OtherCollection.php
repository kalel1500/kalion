<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\Collections;

use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts\AbstractCollectionEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Attributes\CollectionOf;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\OtherEntity;

#[CollectionOf(OtherEntity::class)]
final class OtherCollection extends AbstractCollectionEntity
{
}
