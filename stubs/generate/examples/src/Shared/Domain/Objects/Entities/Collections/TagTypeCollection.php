<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities\Collections;

use Src\Shared\Domain\Objects\Entities\TagTypeEntity;
use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionEntity;

final class TagTypeCollection extends ContractCollectionEntity
{
    public const ENTITY = TagTypeEntity::class;

    public function __construct(TagTypeEntity ...$items)
    {
        $this->items = $items;
    }
}
