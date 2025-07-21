<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Icons;

use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionDo;

final class IconCollection extends ContractCollectionDo
{
    protected const ITEM_TYPE = IconDo::class;

    public function __construct(IconDo ...$items)
    {
        $this->items = $items;
    }
}
