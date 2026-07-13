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

    public const NAVBAR_TITLE_SPACING = [
        'none' => 'ms-0',
        'xs'   => 'ms-1',
        'sm'   => 'ms-2',
        'md'   => 'ms-3',
        'lg'   => 'ms-4',
        'xl'   => 'ms-6',
    ];

    public const ROUNDED_VARIANTS = [
        'none'    => 'rounded-none',
        '2xs'     => 'rounded-xxs',     /* --   -> 2px  = 2px */
        'xs'      => 'rounded-xs',      /* 2px  -> 4px  = 4px */
        'sm'      => 'rounded-sm',      /* 4px  -> 6px  = 6px */
        'md'      => 'rounded-md',      /* 6px  -> ??   = 6px */
        'rounded' => 'rounded',         /* --   -> 8px  = 8px */
        'base'    => 'rounded-base',    /* --   -> 12px = 12px */
        'lg'      => 'rounded-lg',      /* 8px  -> 16px = 16px */
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

    public static function navbarTitleSpacingClass(): string
    {
        $spacing = config('kalion.layout.navbar_title_spacing', 'md');

        return self::NAVBAR_TITLE_SPACING[$spacing] ?? self::NAVBAR_TITLE_SPACING['md'];
    }
}
