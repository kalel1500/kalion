<?php

namespace Thehouseofel\Hexagonal\Infrastructure\View\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Thehouseofel\Hexagonal\Infrastructure\Services\CookieService;

class App extends Component
{
    public $title;
    public $isFromPackage;
    public $darkMode;
    public $sidebarCollapsed;
    public $dataTheme;
    public $colorTheme;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $title = null,
        bool $package = false
    )
    {
        $this->title = $title ?? config('app.name');
        $this->isFromPackage = $package;

        $preferences = CookieService::readOrNew()->preferences();
        $this->darkMode         = $preferences->theme()->isDark();
        $this->sidebarCollapsed = $preferences->sidebar_state_per_page() ? $this->calculateSidebarCollapsedFromItems() : $preferences->sidebar_collapsed();
        $this->dataTheme        = $preferences->theme()->getDataTheme();
        $this->colorTheme       = $preferences->theme()->value();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|Closure|string
     */
    public function render()
    {
        return view('kal::components.layout.app');
    }

    private function calculateSidebarCollapsedFromItems(): bool
    {
        $links = collect(config('kalion_links.sidebar.items'));

        $firstCollapsed = $links->flatMap(function ($item) {
            // Combinar el array con sus sub_links (si existen)
            return array_merge([$item], $item['sub_links'] ?? []);
        })->first(function ($item) {
            return Arr::get($item, 'route_name') === Route::currentRouteName(); // Puedes ajustar el filtro aquí
        });

        return Arr::get($firstCollapsed, 'collapsed', false);
    }
}
