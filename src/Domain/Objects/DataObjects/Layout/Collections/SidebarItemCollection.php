<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Collections;

use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionDo;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\SidebarItemDo;

final class SidebarItemCollection extends ContractCollectionDo
{
    protected const ITEM_TYPE = SidebarItemDo::class;

    public function __construct(SidebarItemDo ...$items)
    {
        $this->items = $items;
    }
}
