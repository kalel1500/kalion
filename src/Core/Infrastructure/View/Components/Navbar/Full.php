<?php

namespace Thehouseofel\Kalion\Core\Infrastructure\View\Components\Navbar;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Layout\Collections\NavbarItemCollection;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Layout\NavbarItemDto;
use Thehouseofel\Kalion\Core\Infrastructure\Facades\LayoutData;

class Full extends Component
{
    public bool                               $showSearch;
    public string                             $searchAction;
    public NavbarItemCollection|CollectionAny $items;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->showSearch   = config('kalion_links.navbar.search.show');
        $this->searchAction = safe_route(config('kalion_links.navbar.search.route'), '#');
        $this->items        = NavbarItemCollection::fromArray(config('kalion_links.navbar.items') ?? []);

        $this->items = $this->items->map(function (NavbarItemDto $item) {
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

        return view('kal::components.navbar.full');
    }
}
