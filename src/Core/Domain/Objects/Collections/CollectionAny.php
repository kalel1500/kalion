<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\Collections;

use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts\AbstractCollectionAny;

class CollectionAny extends AbstractCollectionAny
{
    public function __construct($items = null)
    {
        $items = (is_null($items)) ? [] : $items;
        parent::__construct($items);
    }
}
