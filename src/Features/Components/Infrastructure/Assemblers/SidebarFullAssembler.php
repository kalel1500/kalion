<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Components\Infrastructure\Assemblers;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Layout\Collections\SidebarItemCollection;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Layout\SidebarItemDto;
use Thehouseofel\Kalion\Core\Infrastructure\Facades\LayoutData;
use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Sidebar\SidebarFullDto;

class SidebarFullAssembler
{
    public static function fromProps(): SidebarFullDto
    {
        $showSearch   = config('kalion_links.sidebar.search.show');
        $searchAction = safe_route(config('kalion_links.sidebar.search.route'), '#');
        $items        = SidebarItemCollection::fromArray(config('kalion_links.sidebar.items') ?? []);
        $footer       = SidebarItemCollection::fromArray(config('kalion_links.sidebar.footer') ?? []);
        $hasFooter    = $footer->countInt()->isBiggerThan(0);

        $items = $items->map(function (SidebarItemDto $item) {
            if (! is_null($action = $item->counter_action)) {
                $item->setCounter(LayoutData::$action());
            }
            return $item;
        });
        return SidebarFullDto::fromArray([
            'showSearch'   => $showSearch,
            'searchAction' => $searchAction,
            'items'        => $items,
            'hasFooter'    => $hasFooter,
            'footer'       => $footer,
        ]);
    }
}
