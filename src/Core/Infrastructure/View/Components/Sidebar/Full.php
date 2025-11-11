<?php

namespace Thehouseofel\Kalion\Core\Infrastructure\View\Components\Sidebar;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Layout\Collections\SidebarItemCollection;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Layout\SidebarItemDto;
use Thehouseofel\Kalion\Core\Infrastructure\Facades\LayoutData;

class Full extends Component
{
    public bool                                $showSearch;
    public string                              $searchAction;
    public SidebarItemCollection|CollectionAny $items;
    public bool                                $hasFooter;
    public SidebarItemCollection               $footer;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->showSearch   = config('kalion_links.sidebar.search.show');
        $this->searchAction = safe_route(config('kalion_links.sidebar.search.route'), '#');
        $this->items        = SidebarItemCollection::fromArray(config('kalion_links.sidebar.items') ?? []);
        $this->footer       = SidebarItemCollection::fromArray(config('kalion_links.sidebar.footer') ?? []);
        $this->hasFooter    = $this->footer->countInt()->isBiggerThan(0);

        $this->items = $this->items->map(function (SidebarItemDto $item) {
            if (! is_null($action = $item->counter_action)) {
                $item->setCounter(LayoutData::$action());
            }
            return $item;
        });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('kal::components.sidebar.full');
    }
}
