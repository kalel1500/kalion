<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Collections;

use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringVo;

final class CollectionStrings extends ContractCollectionVo
{
    protected const ITEM_TYPE = StringVo::class;
}
