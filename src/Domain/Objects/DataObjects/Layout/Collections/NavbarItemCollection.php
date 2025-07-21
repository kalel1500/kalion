<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Collections;

use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionDo;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\NavbarItemDo;

final class NavbarItemCollection extends ContractCollectionDo
{
    protected const ITEM_TYPE = NavbarItemDo::class;

    public function __construct(NavbarItemDo ...$items)
    {
        $this->items = $items;
    }
}
