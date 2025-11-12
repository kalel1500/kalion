<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Components\Infrastructure\Assemblers;

use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Navbar\Items\Collections\NavbarItemCollection;
use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Navbar\Items\NavbarItemDto;
use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Navbar\NavbarFullDto;
use Thehouseofel\Kalion\Features\Components\Infrastructure\Facades\LayoutData;

class NavbarFullAssembler
{
    public static function fromProps(): NavbarFullDto
    {
        $showSearch   = config('kalion_links.navbar.search.show');
        $searchAction = safe_route(config('kalion_links.navbar.search.route'), '#');
        $items        = NavbarItemCollection::fromArray(config('kalion_links.navbar.items') ?? []);

        $items = $items->map(function (NavbarItemDto $item) {
            if (! is_null($dropdown = $item->dropdown) && ! is_null($action = $dropdown->get_data_action)) {
                switch ($action) {
                    case 'getNavbarNotifications':
                        $dropdown->setItems(LayoutData::getNavbarNotifications());
                        break;
                    case 'getUserInfo':
                        $dropdown->setUserInfo(LayoutData::getUserInfo());
                        break;
                }
            }
            return $item;
        });

        return NavbarFullDto::fromArray([
            'showSearch'   => $showSearch,
            'searchAction' => $searchAction,
            'items'        => $items,
        ]);
    }
}
