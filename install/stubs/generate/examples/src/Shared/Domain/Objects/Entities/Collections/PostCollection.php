<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities\Collections;

use Src\Shared\Domain\Objects\Entities\PostEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Attributes\CollectionOf;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts\AbstractCollectionEntity;

#[CollectionOf(PostEntity::class)]
final class PostCollection extends AbstractCollectionEntity
{
}
