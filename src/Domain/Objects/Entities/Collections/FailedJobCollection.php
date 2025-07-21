<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities\Collections;

use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionEntity;
use Thehouseofel\Kalion\Domain\Objects\Entities\FailedJobEntity;

class FailedJobCollection extends ContractCollectionEntity
{
    public const ITEM_TYPE = FailedJobEntity::class;

    public function __construct(FailedJobEntity ...$items)
    {
        $this->items = $items;
    }
}
