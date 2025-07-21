<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities\Collections;

use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionEntity;
use Thehouseofel\Kalion\Domain\Objects\Entities\StatusEntity;

class StatusCollection extends ContractCollectionEntity
{
    public const ITEM_TYPE = StatusEntity::class;

    public function __construct(StatusEntity ...$items)
    {
        $this->items = $items;
    }
}
