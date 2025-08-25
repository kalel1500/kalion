<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\Collections;

use Thehouseofel\Kalion\Domain\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts\AbstractCollectionEntity;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\CommentEntity;

#[CollectionOf(CommentEntity::class)]
final class CommentCollection extends AbstractCollectionEntity
{
}
