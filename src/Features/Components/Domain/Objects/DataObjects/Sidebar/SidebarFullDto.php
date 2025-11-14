<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Sidebar;

use Thehouseofel\Kalion\Core\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;
use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Sidebar\Items\Collections\SidebarItemCollection;

class SidebarFullDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly bool                  $showSearch,
        public readonly string                $searchAction,
        public readonly CollectionAny         $items,
        public readonly bool                  $hasFooter,
        public readonly SidebarItemCollection $footer,
    )
    {
    }
}
