<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities\Collections;

use Src\Shared\Domain\Objects\Entities\TagEntity;
use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionEntity;

final class TagCollection extends ContractCollectionEntity
{
    public const ENTITY = TagEntity::class;

    public function __construct(TagEntity ...$items)
    {
        $this->items = $items;
    }
}
