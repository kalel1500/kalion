<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\Collections;

use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;

final class CollectionModelId extends ContractCollectionVo
{
    protected const ITEM_TYPE = ModelId::class;

    public function __construct(ModelId ...$items)
    {
        $this->items = $items;
    }
}
