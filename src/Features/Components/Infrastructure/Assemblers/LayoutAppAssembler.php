<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Components\Infrastructure\Assemblers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Layout\LayoutAppDto;

class LayoutAppAssembler
{
    public static function fromProps(
        bool    $package,
        ?string $headTitle,
        ?string $navbarTitle,
        bool    $flush,
        ?string $pageData,
    ): LayoutAppDto
    {
        $userSettings = kalion()->userSettings()->get();

        return new LayoutAppDto(
            isFromPackage   : $package,
            headTitle       : $headTitle ?? config('app.name'),
            navbarTitle     : $navbarTitle,
            flush           : $flush,
            pageData        : $pageData,
            sidebarEnabled  : ! config('kalion.layout.sidebar_disabled'),
            sidebarCollapsed: $userSettings->sidebar_state_per_page ? self::calculateSidebarCollapsedFromItems() : $userSettings->sidebar_state->isCollapsed(),
            darkMode        : $userSettings->theme->isDark(),
            dataTheme       : $userSettings->theme->getDataTheme(),
            colorTheme      : $userSettings->theme->value,
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
