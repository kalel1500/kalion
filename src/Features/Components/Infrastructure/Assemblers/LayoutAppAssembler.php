<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Components\Infrastructure\Assemblers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Layout\LayoutPreferencesCookieStore;
use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Layout\LayoutAppDto;

class LayoutAppAssembler
{
    public static function fromProps(
        bool    $package,
        ?string $headTitle,
    ): LayoutAppDto
    {
        $preferences = LayoutPreferencesCookieStore::readOrNew()->preferences();

        return new LayoutAppDto(
            headTitle       : $headTitle ?? config('app.name'),
            isFromPackage   : $package,
            sidebarEnabled  : ! config('kalion.layout.sidebar_disabled'),
            darkMode        : $preferences->theme->isDark(),
            sidebarCollapsed: $preferences->sidebar_state_per_page ? self::calculateSidebarCollapsedFromItems() : $preferences->sidebar_state->isCollapsed(),
            dataTheme       : $preferences->theme->getDataTheme(),
            colorTheme      : $preferences->theme->value,
        );
    }

    private static function calculateSidebarCollapsedFromItems(): bool
    {
        $links = collect(config('kalion_links.sidebar.items'));

        $firstCollapsed = $links->flatMap(function ($item) {
            // Combinar el array con sus dropdown (si existen)
            return array_merge([$item], $item['dropdown'] ?? []);
        })->first(function ($item) {
            return Arr::get($item, 'route_name') === Route::currentRouteName(); // Puedes ajustar el filtro aquí
        });

        return Arr::get($firstCollapsed, 'collapsed', false);
    }
}
