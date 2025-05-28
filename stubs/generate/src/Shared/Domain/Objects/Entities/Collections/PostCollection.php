<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities\Collections;

use Src\Shared\Domain\Objects\Entities\PostEntity;
use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionEntity;

final class PostCollection extends ContractCollectionEntity
{
    public const ENTITY = PostEntity::class;

    public function __construct(PostEntity ...$items)
    {
        $this->items = $items;
    }
}
