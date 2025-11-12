<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects;

use Thehouseofel\Kalion\Core\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;

class NavbarFullDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly bool          $showSearch,
        public readonly string        $searchAction,
        public readonly CollectionAny $items,
    )
    {
    }
}
