<?php

namespace Thehouseofel\Kalion\Infrastructure\View\Components\Navbar;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Thehouseofel\Kalion\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Collections\NavbarItemCollection;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\NavbarItemDo;
use Thehouseofel\Kalion\Infrastructure\Facades\Layout;

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
        $this->searchAction = get_url_from_route(config('kalion_links.navbar.search.route'));
        $this->items        = NavbarItemCollection::fromArray(config('kalion_links.navbar.items') ?? []);

        $this->items = $this->items->map(function (NavbarItemDo $item) {
            if (!is_null($dropdown = $item->dropdown) && !is_null($action = $dropdown->get_data_action)) {
                switch ($action) {
                    case 'getNavbarNotifications':
                        $dropdown->setItems(Layout::getNavbarNotifications());
                        break;
                    case 'getUserInfo':
                        $dropdown->setUserInfo(Layout::getUserInfo());
                        break;
                }
            }
            return $item;
        });

        return view('kal::components.navbar.full');
    }
}
