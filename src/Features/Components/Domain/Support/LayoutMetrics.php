<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Components\Domain\Support;

class LayoutMetrics
{
    public const NAVBAR_HEIGHT = [
        'tight'       => '49px',
        'compact'     => '53px',
        'normal'      => '57px',
        'comfortable' => '61px',
    ];

    public static function getShadowClasses(string $normalShadow = ''): string
    {
        return config('kalion.layout.use_elevated_shadows')
            ? 'shadow-glow-2'
            : $normalShadow;
    }

    public static function navbarHeight(): string
    {
        $density = config('kalion.layout.navbar_density', 'normal');

        return self::NAVBAR_HEIGHT[$density] ?? self::NAVBAR_HEIGHT['normal'];
    }
}
