<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections;

use Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts\AbstractCollectionAny;

final class CollectionAny extends AbstractCollectionAny
{
    public function __construct($items = null)
    {
        $items = (is_null($items)) ? [] : $items;
        parent::__construct($items);
    }
}
