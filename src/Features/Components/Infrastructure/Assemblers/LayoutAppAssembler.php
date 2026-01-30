<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Components\Infrastructure\Assemblers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Cookie;
use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Layout\LayoutAppDto;

class LayoutAppAssembler
{
    public static function fromProps(
        ?string $title = null,
        bool    $package = false
    ): LayoutAppDto
    {
        $title         = $title ?? config('app.name');
        $isFromPackage = $package;

        $preferences      = Cookie::readOrNew()->preferences();
        $darkMode         = $preferences->theme->isDark();
        $sidebarCollapsed = $preferences->sidebar_state_per_page ? self::calculateSidebarCollapsedFromItems() : $preferences->sidebar_collapsed;
        $dataTheme        = $preferences->theme->getDataTheme();
        $colorTheme       = $preferences->theme->value;

        return LayoutAppDto::fromArray([
            'title'            => $title,
            'isFromPackage'    => $isFromPackage,
            'darkMode'         => $darkMode,
            'sidebarCollapsed' => $sidebarCollapsed,
            'dataTheme'        => $dataTheme,
            'colorTheme'       => $colorTheme,
        ]);
    }

    private static function calculateSidebarCollapsedFromItems(): bool
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
