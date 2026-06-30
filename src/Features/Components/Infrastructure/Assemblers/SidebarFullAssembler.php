<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Components\Infrastructure\Assemblers;

use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Sidebar\Items\Collections\SidebarItemCollection;
use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Sidebar\Items\SidebarItemDto;
use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Sidebar\SidebarFullDto;
use Thehouseofel\Kalion\Features\Components\Domain\Support\Contracts\LayoutData;

class SidebarFullAssembler
{
    public static function fromProps(): SidebarFullDto
    {
        $layoutData = app(LayoutData::class);

        $footer    = SidebarItemCollection::fromArray(config('kalion_links.sidebar.footer') ?? []);
        $hasFooter = $footer->countInt()->isBiggerThan(0);
        $items     = SidebarItemCollection::fromArray(config('kalion_links.sidebar.items') ?? []);
        $items     = $items->map(function (SidebarItemDto $item) use ($layoutData) {
            if (! is_null($action = $item->counter_action)) {
                $item->setCounter($layoutData->$action());
            }
            return $item;
        });

        return new SidebarFullDto (
            showSearch  : config('kalion_links.sidebar.search.show'),
            searchAction: safe_route(config('kalion_links.sidebar.search.route'), '#'),
            items       : $items,
            hasFooter   : $hasFooter,
            footer      : $footer,
        );
    }
}
