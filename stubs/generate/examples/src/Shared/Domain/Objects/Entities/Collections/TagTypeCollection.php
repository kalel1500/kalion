<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities\Collections;

use Src\Shared\Domain\Objects\Entities\TagTypeEntity;
use Thehouseofel\Kalion\Domain\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionEntity;

#[CollectionOf(TagTypeEntity::class)]
final class TagTypeCollection extends ContractCollectionEntity
{
}
