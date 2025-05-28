<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities\Collections;

use Src\Shared\Domain\Objects\Entities\CommentEntity;
use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionEntity;

final class CommentCollection extends ContractCollectionEntity
{
    public const ENTITY = CommentEntity::class;

    public function __construct(CommentEntity ...$items)
    {
        $this->items = $items;
    }
}
